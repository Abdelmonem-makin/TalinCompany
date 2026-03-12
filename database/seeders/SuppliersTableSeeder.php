<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        Supplier::create(["name" => "مورد A",   "phone" => "0120000001", "address" => "المنطقة"]);
        Supplier::create(["name" => "مورد B",  "phone" => "0120000002", "address" => "المنطقة"]);
    }
}
