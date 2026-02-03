<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersTableSeeder extends Seeder
{
    public function run()
    {
        Supplier::create(["name" => "Adam",  "phone" => "0120000001", "address" => "المنطقة"]);
        Supplier::create(["name" => "khaled",  "phone" => "0120000002", "address" => "المنطقة"]);
        Supplier::create(["name" => "Soluman",  "phone" => "0120000002", "address" => "المنطقة"]);
        Supplier::create(["name" => "Husham",  "phone" => "0120000002", "address" => "المنطقة"]);
    }
}
