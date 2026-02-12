<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Supplier;

class ItemsTableSeeder extends Seeder
{
    public function run()
    {
        $supplier = Supplier::first();
        Item::create(['name' => ' بندول',   'price' => 1500, 'company'=>'الوفاق' ,'type' =>'Box','stock' => 0]);
        Item::create(['name' => ' اسبرين',   'price' => 2000, 'company'=>'تالين' ,'type' =>'Box',   'stock' => 0]);
        Item::create(['name' => ' سامكسون',   'price' => 2500, 'company'=>'تالين' ,'type' =>'Box',   'stock' => 0]);
    }
}
