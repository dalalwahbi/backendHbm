<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\OrderTracking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    
        // Get the authenticated user ID (Supplier)
        $supplierId = Auth::id();
    
        // Validate the request data
        $request->validate([
            'product_ids' => 'required|array',
            'quantities' => 'required|array',
            'customer_id' => 'required|exists:customer_infos,CustomerID', // Adjust based on your customer table
        ]);
    
        // Begin transaction
        DB::beginTransaction();
    
        try {
            $totalAmount = 0; // Initialize total amount
    
            // Loop through each product and calculate the total amount
            foreach ($request->product_ids as $index => $productId) {
                $product = Product::findOrFail($productId);
    
                // Check if enough stock is available
                if ($product->StockQuantity >= $request->quantities[$index]) {
                    // Add to total amount
                    $totalAmount += $product->Price * $request->quantities[$index];
                } else {
                    // Rollback transaction if stock is insufficient
                    DB::rollBack();
                    return response()->json(['error' => 'Not enough stock for product ID ' . $productId], 400);
                }
            }
    
            // Step 1: Create the order first
            $order = Order::create([
                'SupplierID' => $supplierId, // Using the authenticated supplier's ID
                'CustomerID' => $request->customer_id,
                'Status' => 'en attente', // Initial order status
            ]);
    
            // Create the payment and link it to the order
            $payment = Payment::create([
                'OrderID' => $order->OrderID,
                'PaymentStatus' => 'en attente',
                'PaymentMethod' => 'not specified',
                'Amount' => $totalAmount,
            ]);
    
            // Assign PaymentID to the order
            $order->PaymentID = $payment->PaymentID; // Assign the PaymentID to the order
            $order->save(); // Save the changes to the order
    
            // Step 3: Create order items and decrement product stock
            foreach ($request->product_ids as $index => $productId) {
                $product = Product::findOrFail($productId);
    
                // Create an order item
                OrderItem::create([
                    'OrderID' => $order->OrderID,
                    'ProductID' => $productId,
                    'Quantity' => $request->quantities[$index],
                    'Price' => $product->Price,
                ]);
    
                // Decrement product stock quantity
                $product->StockQuantity -= $request->quantities[$index];
                $product->save();
            }
    
            // Create initial order tracking entry
            OrderTracking::create([
                'OrderID' => $order->OrderID,
                'CurrentStatus' => 'en attente', // Initial tracking status
                'StatusDate' => now(), // Current timestamp
            ]);
    
            // Commit the transaction
            DB::commit();
    
            // Log success
            Log::info('Order created successfully', ['order_id' => $order->OrderID, 'payment_id' => $payment->PaymentID]);
    
            // Return success message with order and payment details
            return response()->json(['message' => 'Order created successfully!', 'order_id' => $order->OrderID, 'payment_id' => $payment->PaymentID], 201);
    
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();
    
            // Log the error for debugging
            Log::error('Order creation failed', ['error' => $e->getMessage()]);
    
            // Return error response
            return response()->json(['error' => 'Order creation failed: ' . $e->getMessage()], 500);
        }
    }
    
    // Get all orders for a supplier
    public function getOrders()
    {
        $orders = Order::where('SupplierID', Auth::id())->with('orderItems.product')->get();
        return response()->json($orders);
    }
    public function getOrdersAdmin()
    {
        $orders = Order::where('SupplierID', Auth::id())
            ->with(['orderItems.product', 'payment']) // Add 'payment' relationship
            ->get()
            ->map(function ($order) {
                // Get product names
                $productNames = $order->orderItems->map(function ($item) {
                    return $item->product->Name; // Assuming 'ProductName' is the field in 'Product'
                });
    
                // Get the total for the order using the calculateOrderTotal function
                $total = $this->calculateOrderTotal($order->OrderID);
    
                return [
                    'orderId' => $order->OrderID,
                    'productNames' => $productNames,
                    'status' => $order->status,
                    'paymentStatus' => $order->payment->PaymentStatus, // Access PaymentStatus from the 'payments' table
                    'total' => $total,
                ];
            });
    
        return response()->json($orders);
    }
    
    
    

    // Get details for a specific order
    public function getOrder($orderId)
    {
        $order = Order::where('SupplierID', Auth::id())->with('orderItems.product')->find($orderId);
        if ($order) {
            return response()->json($order);
        } else {
            return response()->json(['error' => 'Order not found'], 404);
        }
    }
    // Function to calculate the total sum of products in an order
    public function calculateOrderTotal($orderId)
    {
        // Retrieve the order by OrderID with its related order items
        $order = Order::with('orderItems')->find($orderId);
    
        if (!$order) {
            return null; // Return null if order not found
        }
    
        // Calculate the total sum
        $total = $order->orderItems->sum(function ($item) {
            return $item->Price * $item->Quantity; // Assuming Price is the price per product and Quantity is how many of that product
        });
    
        // Return the total as a value, not as a JsonResponse
        return $total;
    }
    
    //update order
    public function updateOrder(Request $request, $orderId)
    {
        // Find the order for the authenticated supplier
        $order = Order::where('SupplierID', Auth::id())->find($orderId);
        
        // Check if the order exists and has the status "en attente"
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status !== 'en attente') {
            return response()->json(['error' => 'Cannot edit order. Status is not "en attente".'], 403);
        }

        // Validate the incoming request data
        $request->validate([
            'CustomerID' => 'required|exists:customer_infos,CustomerID',
            // Add other fields that can be updated
            'Status' => 'sometimes|in:en attente,attribué,en transit,livré,annulé', // Only allow valid status updates
            // Add any other validations as needed
        ]);

        // Update the order with new data
        $order->update($request->only(['CustomerID', 'Status'])); // Update the fields as needed

        return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
    }
    public function deleteOrder($orderId)
    {
        // Find the order for the authenticated supplier
        $order = Order::where('SupplierID', Auth::id())->find($orderId);

        // Check if the order exists
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Delete the order
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
