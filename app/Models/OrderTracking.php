<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory;
    protected $table = 'order_tracking'; // Specify the table name

    protected $fillable = [
        'OrderID',
        'CurrentStatus',
        'StatusDate',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }

    // Eloquent Event for Created or Updated status
    protected static function boot()
    {
        parent::boot();

        // Listen for when an OrderTracking record is created or updated
        static::created(function ($orderTracking) {
            $orderTracking->updateOrderStatus();
        });

        static::updated(function ($orderTracking) {
            $orderTracking->updateOrderStatus();
        });
    }

    // Update the Order's status with the latest OrderTracking status
    public function updateOrderStatus()
    {
        // Get the related order
        $order = $this->order;

        // Update the Order's status field with the latest CurrentStatus
        $order->Status = $this->CurrentStatus;
        $order->save();
    }
}
