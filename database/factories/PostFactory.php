<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Products;

class PostFactory extends Factory
{
    protected $model = \App\Models\Products::class;

    public function definition(): array
    {
        static $i = 0;
        $i++;
        return [
            'name'        => $this->faker->words(3, true),
            'brand_name'  => $this->faker->company,
            'category' => $this->faker->randomElement([5, 6, 7]),
            'base_price'       => $this->faker->randomFloat(2, 100, 5000),
            'availability' => $this->faker->randomElement(['In Stock', 'Out Of Stock']),
            'highlighted'   => $this->faker->boolean,
            'status'      => 'active',
            'created_by' => 4,
            'created_at'  => now()->addMinutes($i * 5),
            'updated_at'  => now()->addMinutes($i * 5),
        ];
    }
}
