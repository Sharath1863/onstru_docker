<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DropdownList;
use App\Models\Hub;
use App\Models\UserDetail;

class ProductsFactory extends Factory
{
    public function definition(): array
    {
        // Get all categories for dropdown_id = 3
        $categoryCollection = DropdownList::where('dropdown_id', 3)->get();
        $category = DropdownList::where('dropdown_id', 3)
        ->inRandomOrder()
        ->first();

        // Pick a random user
        $user = UserDetail::inRandomOrder()
            ->whereIn('as_a', ['Contractor', 'Consultant','Vendor'])
            ->first();  

        // Pick a random hub
        $hub = Hub::inRandomOrder()->first();

        return [
            'created_by' => $user->id,
            'name' => fake()->randomElement(['Steel', 'Bricks', 'Cement', 'Sand']),
            'brand_name' => fake()->randomElement(['ABC Steel', 'XYZ Bricks', 'BuildWell Cement', 'Mega Sand']),
            'category' => $category->id,  // convert array to JSON
            'd_days' => 15,
            'd_km' => $this->faker->numberBetween(10, 1000),
            'availability' => 'In Stock',
            'location' => $hub->location_id,
            'hub_id' => $hub->id,
            'specifications' => json_encode([
                'Color' => $this->faker->safeColorName(),
                'Weight' => $this->faker->randomFloat(1, 1, 50) . ' kg',
                'Height' => $this->faker->numberBetween(50, 200) . 'X' . $this->faker->numberBetween(30, 150),
            ]),
            'ship_charge' => json_encode([
                [
                    'from' => (string) $this->faker->numberBetween(10, 100),
                    'to' => (string) $this->faker->numberBetween(101, 500),
                    'price' => (string) $this->faker->numberBetween(50, 500),
                ]
            ]),
            'image' => json_encode([   // convert array to JSON
                'image1' => 'product_images/' . $this->faker->uuid . '.webp',
                'image2' => 'product_images/' . $this->faker->uuid . '.jpg',
            ]),
            'catlogue' => $this->faker->randomElement([
                'product_catalogue/68fa02d272052.pdf',
                'product_catalogue/68feeecbebc7a.pdf'
            ]),
            'cover_img' => $this->faker->randomElement([
                'product_catalogue/68fa02d272052.pdf',
                'product_catalogue/68feeecbebc7a.pdf'
            ]),
            'mrp' =>  $this->faker->randomFloat(2, 10, 50000),
            'sp' => $this->faker->randomFloat(2, 10, 1000), // Selling price: 2 decimals, between 10 and 1000
            'tax_percentage' => $this->faker->numberBetween(0, 28), // Tax percentage 0% to 28%
            'product_unit' => $this->faker->randomElement(['kg', 'g', 'liter', 'pcs']), // Random product unit
            'moq' => $this->faker->numberBetween(1, 100), // Minimum Order Quantity
            'key_feature' => $this->faker->sentence(), // Short descriptive sentence
            'size' => $this->faker->numberBetween(1, 100), // Size options
            'hsn' => $this->faker->numerify('####'), // HSN code: 4 digits
            'margin' => $this->faker->randomFloat(2, 0, 50), // Margin in percentage, e.g., 0.00 - 50.00
            'cashback_price' => $this->faker->randomFloat(2, 0, 1000), // Cashback price, e.g., 0.00 - 1000.00
            // 'margin'  moq
                ];
    }
}
