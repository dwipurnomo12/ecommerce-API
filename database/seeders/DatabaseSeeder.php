<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create role
        $adminRole = Role::create(['name' => 'Admin']);
        $customerRole = Role::create(['name' => 'Customer']);


        //Create user
        $admin = User::create([
            'name'      => 'Dwi Purnomo',
            'email'     => 'dwi@example.test',
            'password'  => bcrypt('password'),
        ]);
        $customer = User::create([
            'name'      => 'Robert Davis Chaniago',
            'email'     => 'robert@example.test',
            'password'  => bcrypt('password'),
        ]);

        // Assign role to user
        $admin->assignRole($adminRole);
        $customer->assignRole($customerRole);
    }
}
