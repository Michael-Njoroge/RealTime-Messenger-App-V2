<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //Sender Id
        $senderId = $this->faker->randomElement([0,1]);
        if($senderId == 0){
            $senderId = $this->faker->randomElement(
                \App\Models\User::where('id' ,'!=', 1)->pluck('id')->toArray()
            );
            $receiverId = 1;
        }else{
            $receiverId = $this->faker->randomElement(
                \App\Models\User::pluck('id')->toArray()
            );
        }
        
        //Group Id
        $groupId = null;
        if($this->faker->boolean(50)){
            $groupId = $this->faker->randomElement(
                \App\Models\Group::pluck('id')->toArray()
            );
            $group = \App\Models\Group::find($groupId);
            $senderId = $this->faker->randomElement(
                $group->users->pluck('id')->toArray()
            );
            $receiverId = null;
        }
        
        return [
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'group_id' => $groupId,
            'message' => fake()->realText(200),
            'created_at' => fake()->dateTimeBetween('-1year','now'),
        ];;
    }
}
