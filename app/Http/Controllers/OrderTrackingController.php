<?php

namespace App\Http\Controllers;

use App\Models\OrderTracking;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderTrackingController extends Controller
{
    public function updateTracking(Request $request, $orderId)
    {
        // Validate incoming request
        $request->validate([
            'CurrentStatus' => 'required|in:en attente,attribué,en transit,livré,annulé',
        ]);

        // Begin transaction
        DB::beginTransaction();

        try {
            // Find the order by ID
            $order = Order::findOrFail($orderId);

            // Create a new order tracking entry
            $orderTracking = OrderTracking::create([
                'OrderID' => $order->OrderID,
                'CurrentStatus' => $request->CurrentStatus,
                'StatusDate' => now(), // Current timestamp
            ]);

            // Update the order status
            $order->Status = $request->CurrentStatus;
            $order->save();

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Order tracking updated successfully!',
                'orderTracking' => $orderTracking
            ], 200);

        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            DB::rollBack();
            return response()->json(['error' => 'Failed to update order tracking: ' . $e->getMessage()], 500);
        }
    }
    public function getOrderTrackingStatus($orderId)
    {
        // Find the order tracking status for the given OrderID
        $trackingStatus = OrderTracking::where('OrderID', $orderId)->orderBy('StatusDate', 'desc')->first();

        if (!$trackingStatus) {
            return response()->json(['error' => 'No tracking status found for this order.'], 404);
        }

        return response()->json([
            'OrderID' => $orderId,
            'CurrentStatus' => $trackingStatus->CurrentStatus,
            'StatusDate' => $trackingStatus->StatusDate,
        ]);
    }
}

