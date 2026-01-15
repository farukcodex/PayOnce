<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'amount',
        'currency',
        'stripe_session_id',
        'stripe_payment_intent_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
