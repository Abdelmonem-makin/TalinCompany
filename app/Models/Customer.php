<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $casts = [
        'id' => 'integer'
    ];
    protected $fillable = [
        'id',
        'name',
        'phone',
        'address',
        'account_id',
    ];
    function Sales()
    {
        return $this->hasMany(Sales::class, 'customer_id');
    }
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class ,'account_id');
    }
    public function bank()
    {
        return $this->belongsTo(Bank::class, 'account_id');
    }
}
