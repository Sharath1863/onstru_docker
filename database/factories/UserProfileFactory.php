<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\DropdownList;
use Illuminate\Http\UploadedFile;
use App\Models\UserDetail;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = UserDetail::inRandomOrder()->where('you_are','!=','Consumer')->first();
        $isBussiness = $user && $user->you_are === 'Business'; 
        $isCont_Consul = $user && in_array($user->as_a, ['Contractor', 'Consultant']);
        $isVendor = $user && $user->as_a === 'Vendor';
        $isStudent = $user && $user->you_are === 'Professional' && $user->type_of === 29;
        $isWorking = $user && $user->you_are === 'Professional' && $user->type_of === 30; 
       // $isContractor = $user && $user->you_are === 'Contractor'; 

        $pro = DropdownList::where('dropdown_id', 1)->inRandomOrder()->first();
        $projectvalue = $pro ? $pro->id : null;

        $pur = DropdownList::where('dropdown_id', 11)->inRandomOrder()->first();
        $purposevalue = $pur ? $pur->id : null;

        $des = DropdownList::where('dropdown_id', 13)->inRandomOrder()->first();
        $designationvalue = $pur ? $pur->id : null;
        return [
            //
            'bank_name' => $isBussiness ? $this->faker->name() : null,
            'acct_holder' =>$isBussiness ? $this->faker->name() : null,
            'acct_no' =>$isBussiness ? $this->faker->bankAccountNumber() : null,
            'acct_type' =>$isBussiness ? $this->faker->randomElement(['Savings', 'Current']) : null,
            'ifsc_code' =>$isBussiness ? strtoupper($this->faker->bothify('????0######')) : null,       //letters and numbers start 0 
            'branch_name' =>$isBussiness ? $this->faker->city() . ' Branch' :null,
            'project_category' =>$isCont_Consul ? $projectvalue :null,
            'your_purpose' => ($isCont_Consul || $isVendor) ?  $purposevalue : null,
            'services_offered' =>$isCont_Consul ? $this->faker->text(5) :null,
            'projects_ongoing' =>$isCont_Consul ? $this->faker->randomDigitNotNull() :null,   //1 to 9
            'ongoing_details' =>$isCont_Consul ? $this->faker->text(5) :null,
            'labours' =>$isCont_Consul ? $this->faker->randomDigitNotNull() :null,   //1 to 9
            'mobilization' =>$isCont_Consul ? $this->faker->text(5) :null,
            'strength' =>($isCont_Consul || $isVendor) ?
                            $this->faker->text(5) :null,
            'client_tele' =>($isCont_Consul || $isVendor) ? $this->faker->numberBetween(6000000000, 9999999999) :null,
            'customer' =>($isCont_Consul || $isVendor) ? $this->faker->name() :null,
            'delivery_timeline' =>$isVendor ? $this->faker->text(5) :null,
            'location_catered' =>$isVendor ? $this->faker->text(5) :null,
            'professional_status' => 
                            $isStudent ? 'Student' : 
                            ($isWorking ? 'Working' : null),

           // 'professional_status' =>$isStudent   $isWorking ? $this->faker->randomElement(['Student', 'Working']) : null,
            'education' =>
                        ($isStudent || $isWorking) ?
                        $this->faker->randomElement([
                            'B.Tech. IT',
                            'B.E. CSE',
                            'B.Sc. CS',
                            'ECE',
                            'EEE',
                        ])
                        : null,
            'college' => $isStudent ? $this->faker->name() : null,
            'designation' => $isWorking ? $designationvalue : null,
            'employment_type' => $isWorking ? $this->faker->randomElement([
                'Part-Time',
                'Full-Time',
                'Intern',
                'Contract'
            ]) : null,
            'experience' => $isWorking ? $this->faker->randomElement([
                '1',
                '2',
                '3',
                '4',
                '5'
            ]) : null,
            'projects_handled' =>$isWorking ? $this->faker->randomDigitNotNull() :null,   //1 to 9
            'expertise' =>$isWorking ? $this->faker->text(5) : null,
            'current_ctc' =>$isWorking ? $this->faker->numberBetween(3000, 2500000) : null,
            'notice_period' =>$isWorking ? $this->faker->numberBetween(10, 20) :null,
            'aadhar_no' => ($isStudent || $isWorking) ?
                           $this->faker->numerify('############') : null,
            'pan_no' => ($isStudent || $isWorking) ?
                        $this->faker->numerify('##########') : null,
            'income_tax' => $isCont_Consul ? UploadedFile::fake()->create('income_tax.pdf', 100, 'application/pdf') : null,
            'c_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),

        ];
    }
}
