<?php

namespace Database\Factories;

use App\Models\Products;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;

class ProductsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Products::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        Log::info(asset('/storage/products/images/default.jpg'));
        return [
            'title' => $this->faker->paragraph(2),
            'description' => $this->faker->paragraph(8),
            'price' => rand(1, 10000),
            'image' => 'http://127.0.0.1:8000/storage/products/images/default.jpg',
            'owner' => User::all()->random()->id,
        ];
    }
}
