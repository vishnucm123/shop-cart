<?php

// database/seeders/CouponsTableSeeder.php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('coupons')->insert([
            ['code' => 'DISCOUNT10', 'discount_percentage' => 10, 'max_discount' => 75],
            // Add more coupons if needed
        ]);
    }
}

