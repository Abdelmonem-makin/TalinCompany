<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomersTableSeeder extends Seeder
{
    public function run()
    {
        Customer::create(["name" => "عميل تجريبي 1",  "phone" => "0100000001", "address" => "المدينة", "address" => "المدينة"]);
        Customer::create(["name" => "عميل تجريبي 2",   "phone" => "0100000002", "address" => "المدينة", "address" => "المدينة"]);
    }
}
