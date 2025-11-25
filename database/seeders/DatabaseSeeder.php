<?php

namespace Database\Seeders;

use App\Models\GstDetails;
use App\Models\UserDetail;
use App\Models\UserProfile;
use App\Models\Jobs;
use App\Models\JobBoost;
use App\Models\JobApplied;
use App\Models\SavedJobs;
use App\Models\Service;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Products;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //UserDetail::factory(1)->has(GstDetails::factory(), 'gst')->create();
       // UserProfile::factory(1)->create();


        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        /*user profile create
        UserProfile::factory(1)->create();
        */


        /*Jobs Create-
        Jobs::factory(1)->create();
        */

        //JobBoost::factory(1)->create();
        // create 1 job boost
        /*  job boost seeder
        $jobBoost = JobBoost::factory()->create();
        Jobs::where('id', $jobBoost->job_id)->update(['highlighted' => 1]);
        */

       /* Jobs Apply-
       JobApplied::factory(1)->create();
      */

      /*SavedJobs::factory(1)->create();*/
      
      /*service factory*/
      //Service::factory(1)->create();

      /*lead factory*/
      //Lead::factory(1)->create();
    
     //project
    // Project::factory(1)->create();

     //product
     Products::factory(1)->create();


    }
}
