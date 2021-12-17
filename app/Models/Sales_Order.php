<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales_Order extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sales_order_list_accurate';
    protected $fillable = [
        'so_number',
        'order_date',
        'period_date',
        'payment_method',
        'customer_name',
        'customer_code',
        'salesman',
        'delivery_address',
        'total_quantities',
        'total_amount',
        'status',
        'deleted',
        'contact_number'
    ];
}
