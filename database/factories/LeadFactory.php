<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDetail;
use App\Models\DropdownList;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = UserDetail::inRandomOrder()->first();   
        $servicecat = DropdownList::where('dropdown_id', 5)->inRandomOrder()->first();
        $loc = DropdownList::where('dropdown_id', 1)->inRandomOrder()->first();
        return [
            //

            'title'=> $this->faker->randomElement([
                    'Plumbing Repair',
                    'Cleaning Service',
                    'Carpentry Work',
                    'AC Installation',
                    'Electrical Maintenance',
                    'Home Painting',
                    'Gardening Service',
                    'office wall painting'
                ]),
            'service_type' => $servicecat,
            'buildup_area' => $this->faker->randomFloat(2, 0, 10000),
            'budget' => $this->faker->numberBetween(1000, 50000),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'location' => $loc,
            'description' => $this->faker->text(5),
            'created_by' =>  $user->id,
          
            ];
        
    }
}
