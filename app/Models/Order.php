<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'OrderID'; // Define custom primary key

    protected $fillable = [
        'SupplierID',
        'PaymentID',
        'CustomerID',
        'Status',
    ];

    public function orderTracking()
    {
        return $this->hasMany(OrderTracking::class, 'OrderID');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'OrderID');
    }

    public function supplier()
    {
        return $this->belongsTo(User::class, 'SupplierID');
    }

    public function customerInfo()
    {
        return $this->belongsTo(CustomerInfo::class, 'CustomerID');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'OrderID');
    }


}
