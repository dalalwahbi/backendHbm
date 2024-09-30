<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'Name',
        'Description',
        'Price',
        'StockQuantity',
        'Category',
    ];
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'ProductID');
    }
}
