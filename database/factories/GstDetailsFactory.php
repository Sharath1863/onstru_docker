<?php

namespace Database\Factories;

use App\Models\GstDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GstDetails>
 */
class GstDetailsFactory extends Factory
{
    protected $model = GstDetails::class;

    public function definition(): array
    {
        return [
            'gst_number' => '29ABCDE1234F1Z5',
            'gst_verify' => $this->faker->randomElement(['yes', 'no']), // 80% chance of being true
            // 'business_name' => $this->faker->company(),
            // 'address' => $this->faker->address(),
            // 'state' => $this->faker->state(),
            // 'pan_number' => strtoupper($this->faker->bothify('?????#####')),
        ];
    }
}
