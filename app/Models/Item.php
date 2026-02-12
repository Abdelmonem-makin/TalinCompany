<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;
use App\Models\InvoiceLine;
use App\Models\SaleLine;
use App\Models\PurchaseLine;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'company',
        'price',
        'stock',
    ];
   function sales()
    {
        return $this->belongsToMany(sales::class, 'item_sales')->withPivot('quantity', 'sales_price');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoiceLines()
    {
        return $this->hasMany(InvoiceLine::class);
    }

    public function saleLines()
    {
        return $this->hasMany(SaleLine::class, 'item_id');
    }

    public function purchaseLines()
    {
        return $this->hasMany(PurchaseLine::class, 'item_id');
    }

    public function Stocks() :HasMany
    {
        return $this->hasMany(stock::class, 'item_id');
    }
}
