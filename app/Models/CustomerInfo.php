<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInfo extends Model
{

    use HasFactory;
    protected $primaryKey = 'CustomerID'; // Custom primary key

    protected $fillable = [
        'Name',
        'SupplierID',
        'Email',
        'Phone',
        'Address',
        'City',
    ];
    public function supplier()
    {
        return $this->belongsTo(User::class, 'SupplierID');
    }
}
