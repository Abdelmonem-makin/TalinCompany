<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
              // Ensure the 'super_admin' role exists
        $role = Role::firstOrCreate(
            ['name' => 'super_admin'],
            ['display_name' => 'Super Admin', 'description' => 'User with full access']
        );

        $user = \App\Models\User::create([
            'name' => 'super_admin',
            // 'email' => 'super_admin@app.com',
            'password' => bcrypt('password')
        ]);

        $user->addRole('super_admin');
    }
}
