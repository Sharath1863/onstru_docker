<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDetail;
use App\Models\DropdownList;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   
    public function definition(): array
    {
        $user = UserDetail::inRandomOrder()
        ->where('as_a', ['Contractor', 'Consultant'])
        ->first();  
        $loc = DropdownList::where('dropdown_id', 1)->inRandomOrder()->first();  
        $startDate = $this->faker->dateTimeBetween('now', '+1 year');
        $s3Keys = ["project_images/69180d78e78aa.webp","project_images/69180db2b5063.jpg","project_images/69180db8ce159.jpg","project_images/69180db9d7b27.jpg",
                  "project_images/68e5e42b024f8.jpg","project_images/68e5e458d2c66.jpg","project_images/68e5e45a8d1d3.jpg"];
    
        // Pick random number of images (1–3) for sub_images
        $subImages = collect($s3Keys)->shuffle()->take(rand(1,3))->values()->toArray();
        $mainImage = $subImages[0] ?? null;
        return [
            //
             'created_by' => $user->id,
              'title'=> $this->faker->randomElement([
                'Plumbing Repair',
                'Deep Cleaning Service',
                'Carpentry Work',
                'AC Installation',
                'Electrical Maintenance',
                'Home Painting',
                'Gardening Service',
                'Pest Control Service',
            ]),
            'description' => $this->faker->text(5),
            'location' => $loc,
            'start_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d').' +6 months')->format('Y-m-d'),
            'key_outcomes' => $this->faker->text(5),
            'prjt_budget' => $this->faker->numberBetween(1000, 50000),
            'job_role' => $this->faker->text(5),
            'responsibilities' => $this->faker->text(5),
            'image' => $mainImage,
            'sub_image' => json_encode($subImages),
            'amount' => 500,
            'status' => 'active',
        ];
    }
}
