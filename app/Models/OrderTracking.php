<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory;
    protected $fillable = [
        'OrderID',
        'CurrentStatus',
        'StatusDate',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'OrderID');
    }
}
