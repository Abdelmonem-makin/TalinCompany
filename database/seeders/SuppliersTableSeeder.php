<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        Supplier::create(["name" => "Adam",  "phone" => "0120000001", "address" => "المنطقة"]);
        Supplier::create(["name" => "Ahmed",  "phone" => "0120000002", "address" => "المنطقة"]);
    }
}
