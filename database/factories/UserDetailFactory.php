<?php

namespace Database\Factories;

use App\Models\DropdownList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDetail>
 */
class UserDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $youAreOptions = ['Business', 'Professional', 'Consumer'];

        $your_are = $this->faker->randomElement($youAreOptions);
        $asAOptions = match ($your_are) {
            'Business' => ['Vendor', 'Contractor', 'Consultant'],
            'Professional' => ['Technical', 'Non-Technical'],
            default => [null],
        };

        $asA = $this->faker->randomElement($asAOptions);

        $dropIdMap = [
            'Vendor' => 6,
            'Contractor' => 7,
            'Consultant' => 8,
            'Technical' => 9,
            'Non-Technical' => 9,
        ];

        $drop_id = $dropIdMap[$asA] ?? null;

        $dropdown = DropdownList::where('dropdown_id', $drop_id)->inRandomOrder()->first();
        $dropdownValue = $dropdown ? $dropdown->value : null;

        $loc = DropdownList::where('dropdown_id', 1)->inRandomOrder()->first();
        $locationValue = $loc ? $loc->id : null;

        $nameParts = explode(' ', strtolower($this->faker->name()));

        $first = preg_replace('/[^a-z]/', '', $nameParts[0] ?? 'user');
        $last = preg_replace('/[^a-z]/', '', $nameParts[1] ?? $this->faker->word());

        // randomly pick dot or underscore
        $separator = $this->faker->randomElement(['.', '_']);

        $user_name = "{$first}{$separator}{$last}" ?: 'user_'.$this->faker->unique()->numberBetween(1000, 9999);

        // $user_name = preg_replace('/[^a-z_.]/', '', strtolower($this->faker->name())) ?: 'user_'.$this->faker->unique()->numberBetween(1000, 9999);

        //    'badge' => 0 ?$this->faker->randomElement(['newbie', 'intermediate', 'expert']),

        return [
            'name' => $this->faker->name(),
            'you_are' => $your_are,
            'as_a' => $asA,
            'type_of' => $dropdownValue,
            'address' => $this->faker->address(),
            'location' => $locationValue,
            'user_name' => $user_name,
            'profile_img' => $this->faker->imageUrl(200, 200, 'people'), // fake image
            'bio' => $this->faker->paragraph(),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'email' => $this->faker->unique()->safeEmail(),
            'number' => $this->faker->numberBetween(6000000000, 9999999999),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'badge' => '0',
            'otp' => $this->faker->numberBetween(1111, 9999),
            'otp_status' => 'yes',
            'created_at' => now(),
            'updated_at' => now(),
            'password' => 123456,
            // 'type' => Type::inRandomOrder()->value('id') ?? 1, // foreign key
        ];
    }
}
