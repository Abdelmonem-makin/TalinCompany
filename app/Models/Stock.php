<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'item_id',
        'change',
        'type',
        'reference_id',
        'note',
    ];

    public function item() :BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

