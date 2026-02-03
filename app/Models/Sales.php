<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $casts = [
        'date' => 'datetime'
    ];
    protected $fillable = [
        'customer_id',
        'invoice_number', 
        'user_id',
        'date',
        'type',
        'total',
        // 'status',
    ];
    function payment()  {
        return $this->morphMany(Account::class , 'payable');
    }
    public function lines()
    {
        return $this->hasMany(SaleLine::class, 'sale_id');
    }
    public function Stock()
    {
        return $this->hasMany(Stock::class, 'reference_id');
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class , 'customer_id');
    }
    public function user()
    {
        return $this->belongsTo(user::class);
    }
    function item()
    {
        return $this->belongsToMany(item::class, 'item_sales')->withPivot('stock', 'sales_price');
    }
}
