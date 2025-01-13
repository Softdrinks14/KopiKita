<?php

namespace App\Models;

use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'table_no',
        'order_date',
        'order_time',
        'status',
        'total'
    ];

    public function sumOrderPrice() {
        $total = OrderDetail::where('order_id', $this->id)->select('price', 'qty')->get();
        return $total->sum(function($detail) {
            return $detail->price * $detail->qty;
        });
    }

    /**
     * Get all of the orderDetail for the orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDetail(): HasMany
    {
        return $this->hasMany(orderDetail::class, 'order_id', 'id');
    }
}
