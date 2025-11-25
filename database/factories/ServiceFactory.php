<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DropdownList;
use App\Models\UserDetail;
use App\Models\ServiceBoost;



/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $servicecat = DropdownList::where('dropdown_id', 4)->inRandomOrder()->first();
        $loc = DropdownList::where('dropdown_id', 1)->inRandomOrder()->first();
        $user = UserDetail::inRandomOrder()->where('you_are', 'Business')->first();
        $s3Keys = ["project_images/69180d78e78aa.webp","project_images/69180db2b5063.jpg","project_images/69180db8ce159.jpg","project_images/69180db9d7b27.jpg",
                 "project_images/68e5e42b024f8.jpg","project_images/68e5e458d2c66.jpg","project_images/68e5e45a8d1d3.jpg",
                 "service_images\/68f1bdf4a2703.jpg","service_images\/68f1bdf5e9385.jpg"];
    
        // Pick random number of images (1–3) for sub_images
        $subImages = collect($s3Keys)->shuffle()->take(rand(1,3))->values()->toArray();
        $mainImage = $subImages[0] ?? null;
        return [ 
            //
            'created_by'=>$user->id,
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
            'service_type' => $servicecat,
            'price_per_sq_ft' => $this->faker->randomFloat(2, 0, 10000),
            'location' => $loc,
            'description'=> $this->faker->text(5),
            'image' => $mainImage,
            'sub_images' => json_encode($subImages),
            // 'video'      => $video,
            'click' => 0,
            'highlighted' => 0,
            'approvalstatus' => 'pending',
            'status' => 1,
            ];
    }

    /*
    public function configure()
    {
        return $this->afterCreating(function ($service) {

            ServiceBoost::create([
                'service_id' => $service->id,
                'type' => 'list',
                'click' => 0,
                'amount' => 100,
                'status' => 'active',
            ]);

        });
    }
        */
}
