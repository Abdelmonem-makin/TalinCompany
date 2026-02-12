<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
       protected $fillable = [
        'id',
        'name',
        'kind',
        'type',
        'number',
        'balance',
    ];
        public function transactions()
    {
        return $this->hasMany(Transaction::class,'account_id');
    }

    public function customers()
    {
        return $this->hasOne(Customer::class ,'account_id');
    }
    public function Expense()
    {
        return $this->hasMany(Expense::class ,'account_id');
    }
    public function suppliers()
    {
        return $this->hasOne(Supplier::class ,'account_id');
    }
}
