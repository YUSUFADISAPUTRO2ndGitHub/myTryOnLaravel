<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales_Order_Details extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sales_order_details_accurate';
    protected $fillable = [
        'so_number',
        'name',
        'product_code',
        'quantity_bought',
        'price_per_unit',
        'total_price',
        'oid'
    ];
}
