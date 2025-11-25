<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserDetail;
use App\Models\DropdownList;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jobs>
 */
class JobsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = UserDetail::inRandomOrder()->where('you_are', 'Business')->first();
        $jobcat = DropdownList::where('dropdown_id', 4)->inRandomOrder()->first();
        $jobcategoryValue = $jobcat ? $jobcat->id : null;

        $loc = DropdownList::where('dropdown_id', 1)->inRandomOrder()->first();
        $locationValue = $loc ? $loc->id : null;
        return [
            //
            'created_by' => $user->id,
            'title' => $this->faker->jobTitle(),
            'category' =>  $jobcategoryValue,
            'shift' =>
                $this->faker->randomElement([
                'Full-Time', 'Part-Time', 'Intern', 'Contract', 'freelancer'
                ]),
            'salary' => $this->faker->numberBetween(3000, 40000),
            'description' => $this->faker->text(5),
            'location' => $locationValue,
            'sublocality' => $this->faker->text(5),      //doubt
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
            'highlighted' => 0,
            'click' => 0,
            'experience' =>  $this->faker->randomElement([
                            '1',
                            '2',
                            '3',
                            '4',
                            '5'
                        ]),
            'benfit' => $this->faker->text(5),
            'no_of_openings' => $this->faker->numberBetween(1, 10),
            'approvalstatus' =>'pending',
            'benfit' => $this->faker->text(5),
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
