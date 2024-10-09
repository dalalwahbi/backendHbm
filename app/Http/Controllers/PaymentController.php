<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function updatePaymentStatus(Request $request, $orderId)
    {
        // Validate input status to be either "payé" or "annulé"
        $validated = $request->validate([
            'PaymentStatus' => 'required|in:payé,annulé',
        ]);
    
        // Find the order by its ID
        $order = Order::find($orderId);
    
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
    
        // Find the payment associated with the order using PaymentID, not 'id'
        $payment = Payment::where('PaymentID', $order->PaymentID)->first();
    
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
    
        // Update the PaymentStatus based on the request input
        $payment->PaymentStatus = $validated['PaymentStatus'];
        $payment->save();
    
        return response()->json(['message' => 'Payment status updated successfully', 'payment' => $payment]);
    }

    
    
}
