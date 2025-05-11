<?php

namespace Database\Factories;

use App\Models\Tasks;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks>
 */
class TasksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Tasks::class;

    public function definition(): array
    {
        return [
            'title'=> $this->faker->randomElement(['Contract','Agreement','S&P']),
            'sender_name'=> $this->faker->name(),
            'sender_contact_no'=> $this->faker->phoneNumber(),
            'sender_address'=> $this->faker->address(),
            'sender_city'=> $this->faker->city(),
            'sender_location_url'=> $this->faker->url(),
            'recipient_name'=> $this->faker->name(),
            'recipient_contact_no'=> $this->faker->phoneNumber(),
            'recipient_address'=> $this->faker->address(),
            'recipient_city'=> $this->faker->city(),
            'recipient_location_url'=> $this->faker->url(),
            // 'image_url'=> $this->faker->imageUrl(300,400),
            'status'=> $this->faker->randomElement(['PENDING','DELIVERY','COMPLETED','CANCELLED']),
            // 'user_id'=> User::factory(),
        ];
    }
}
