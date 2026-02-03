<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLine extends Model
{
    use HasFactory;

    protected $table = 'purchase_lines';

    protected $fillable = [
        'purchase_id',
        'item_id',
        'quantity',
        'unit_price',
        'total',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'purchase_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
