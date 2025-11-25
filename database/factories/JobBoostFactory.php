<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Jobs;
use App\Models\Charge;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobBoost>
 */
class JobBoostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobs = Jobs::inRandomOrder()
            ->where('highlighted', 0)
            ->where('click', 0)
            ->where('status', 'active')
            ->where('approvalstatus', 'approved')
            ->first();
    
        $jobId = $jobs ? $jobs->id : 1;
    
        $from = $this->faker->dateTimeBetween('now', '+3 months');
        $to = $this->faker->dateTimeBetween($from, (clone $from)->modify('+30 days')); // 'to' after 'from'
    
        $job_day_charge = Charge::where('category', 'job_boost')
            ->where('status', 'active')
            ->latest()
            ->value('charge') * 1.18;
    
        $days = $from->diff($to)->days + 1;
        $total_amount = $job_day_charge * $days;
    
        return [
            'job_id' => $jobId,
            'from' => $from,
            'to' => $to,
            'amount' => $total_amount,
            'day_charge' => $job_day_charge,
            'click' => 0,
            'status' => 'active',
           // Jobs::where('id', $jobId)->update(['highlighted' => 1]);
        ];
    }
    
}
