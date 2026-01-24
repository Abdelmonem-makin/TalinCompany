<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $casts = [
        'id' => 'integer'
    ];
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'address',
        'account_id',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
    public function Purchase()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'account_id');
    }
}
