<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';
    protected $casts = [
        'expiry' => 'date',
    ];

    protected $fillable = [
        'item_id',
        'quantity',
        'type',
        'remaining',
        'status',
        'reference_id',
        'purchase_id',
        'note',
        'expiry',
        'is_expired',
    ];

    public function item() :BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
        public function Sales() :BelongsTo
    {
        return $this->belongsTo(Sales::class,'reference_id');
    }

    public function purchase() :BelongsTo
    {
        return $this->belongsTo(Purchases::class ,'reference_id');
    }
}

