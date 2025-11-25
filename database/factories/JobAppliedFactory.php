<?php

namespace Database\Factories;

use App\Models\JobApplied;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Jobs;
use App\Models\UserDetail;
use App\Models\DropdownList;
use Illuminate\Http\UploadedFile;




/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobApplied>
 */
class JobAppliedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $job= Jobs::inRandomOrder()
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
            $alreadyApplied = JobApplied::where('job_id', $job->id)
            ->where('created_by', $user->id)
            ->exists();
         // If already applied, try again recursively
         if ($alreadyApplied) {
            return $this->definition();
        }
        $loc = DropdownList::where('dropdown_id', 1)->inRandomOrder()->first();
        $locationValue = $loc ? $loc->id : null;
        return [
            //
            'created_by' => $user->id,
            'job_id' => $job->id,
            'skills' => implode(', ', $this->faker->randomElements([
                        'PHP', 'Laravel', 'MySQL', 'HTML', 'CSS', 'JavaScript',
                        'Vue.js', 'React', 'Node.js', 'Git', 'REST API', 'Python',
                        'Bootstrap', 'jQuery', 'SQL', 'OOP', 'AJAX', 'Docker'
                    ], $this->faker->numberBetween(2, 5))),
            'qualification' => $this->faker->randomElement([
                                '8th',
                                '10th',
                                '12th',
                                'Diploma',
                                'BE_Civil',
                                'BE',
                                'BTech',
                                'BCA',
                                'BSC',
                                'BCom',
                                'BA',
                                'BBA',
                                'ME'
                            ]),
                            'experience' =>  $this->faker->randomElement([
                                '1',
                                '2',
                                '3',
                                '4',
                                '5'
                            ]),
            'location' => $locationValue,
            'current_salary' => $this->faker->numberBetween(3000, 40000),
            'expected_salary' => $this->faker->numberBetween(3000, 40000),
            'notes' => $this->faker->text(5),
            'resume' => UploadedFile::fake()->create('income_tax.pdf', 100, 'application/pdf'),
            'created_at' => now(),
            'updated_at' => now(),


        ];
    }
}
