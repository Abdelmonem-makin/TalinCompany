<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable',
        'payment_method',
        'bank',
        'cach',
        'total',
        'name',
        'type',
    ];
    function payable() : MorphTo {
        return $this->morphTo();
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class ,);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}
