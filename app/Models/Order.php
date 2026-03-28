<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'shop_id', 'order_code', 'name', 'phone', 'address', 'note', 'total', 'status', 'document_path', 'payment_path', 'amandement_path', 'turnitin_result'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    use HasFactory;
}
