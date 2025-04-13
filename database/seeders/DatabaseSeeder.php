<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductGallery;
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
            'price'             => 5500000.00,
            'status'            => 'available',
            'posted_by'         => 1,
            'category_id'       => 1,
        ]);
        ProductGallery::create([
            'product_id'    => 1,
            'image'         => 'gallery_product/img_gallery1.jpg'
        ]);
        ProductGallery::create([
            'product_id'    => 1,
            'image'         => 'gallery_product/img_gallery2.jpg'
        ]);

        Discount::create([
            'discount_code'     => 'IDF2025',
            'discount_name'     => 'Diskon Idul fitri 2025 10%',
            'discount_amount'   => 10,
            'start_date'        => '2025-04-10',
            'end_date'          => '2025-04-20',
            'is_active'         => true,
        ]);
    }
}