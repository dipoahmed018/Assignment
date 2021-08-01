<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Products::truncate(); 
        // \App\Models\User::factory(10)->create();
        Products::factory(30)->create();
    }
}
