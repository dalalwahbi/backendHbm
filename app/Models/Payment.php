<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $primaryKey = 'PaymentID'; // Use the name of your primary key column

    protected $fillable = [
        'OrderID',
        'PaymentStatus',
        'PaymentMethod',
        'Amount',
        'PaymentDate',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }
}
