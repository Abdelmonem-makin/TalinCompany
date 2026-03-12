<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'amount',
        'date',
        'description',
        'category',
    ];

    public function account()
    {
        return $this->belongsTo(bank::class);
    }
}
