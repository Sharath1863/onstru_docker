<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Jobs;
use App\Models\UserDetail;
use App\Models\SavedJobs;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavedJobs>
 */
class SavedJobsFactory extends Factory
{

   
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $job=Jobs::inRandomOrder()
        ->where('status', 'active')
        ->where('approvalstatus', 'approved')
        ->first();

        if (!$job) {
            $job = Jobs::factory()->create([
                'status' => 'active',
                'approvalstatus' => 'approved',
            ]);
        }

        $user = UserDetail::inRandomOrder()
        ->where('you_are', ['Consumer', 'Professional'])
        ->first();      
        if (!$user) {
            $user = UserDetail::factory()->create([
                'you_are' => 'Consumer',
            ]);
        }
  

        $existing = SavedJobs::where('jobs_id', $job->id)
            ->where('c_by', $user->id)
            ->exists();
        // If already applied, try again recursively
        if ($existing) {
            $existing->delete();
            return [
                'jobs_id' => $job->id,
                'c_by' => $user->id,
                'status' => 'unsaved',
            ];
        }

        return [
            //
            'jobs_id' =>$job->id,
            'c_by' =>$user->id,
            'status' =>'saved',
        ];
    }
}
