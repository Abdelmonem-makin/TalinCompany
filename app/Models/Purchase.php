<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';
    protected $casts = [
        'date' => 'datetime'
    ];
    protected $fillable = [
        'supplier_id',
        'date',
        'total',
        'status',
    ];

    public function lines()
    {
        return $this->hasMany(PurchaseLine::class, 'purchase_id');
    }

    // alias for views that expect `purchaseLines`
    public function purchaseLines()
    {
        return $this->lines();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
