<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
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

        Category::create([
            'category'  => 'Laptop'
        ]);

        Product::create([
            'featured_image'    => 'featured_image/featured_image1.jpg',
            'name'              => 'Lenovo Ideapad 5',
            'slug'              => 'lenovo-ideapad-5',
            'description'       => 'This is description laptop Lenovo Ideapad 5',
            'price'             => 550000.00,
            'status'            => 'available',
            'posted_by'         => 1,
            'category_id'       => 1,
        ]);
    }
}