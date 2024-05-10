<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         //Users
        User::factory()->create([
            'name' => 'Michael Njoroge',
            'email' => 'mikethecoder12@gmail.com',
            'password' => bcrypt('password@123'),
            'is_admin' => true
        ]);
        
        User::factory()->create([
            'name' => 'Lucy Nyambura',
            'email' => 'lucy12@gmail.com',
            'password' => bcrypt('password@123')
        ]);
        
        User::factory(10)->create();

        //Groups
        for ($i = 0; $i < 5; $i++){
            $group = Group::factory()->create([
                'owner_id' =>1,
            ]);

            $users = User::inRandomOrder()->limit(rand(2,5))->pluck('id');
            $group->users()->attach(array_unique([1, ...$users]));
        }

        //Messages
        Message::factory(1000)->create();
        $messages = Message::whereNull('group_id')->orderBy('created_at')->get();
        
        //Conversations
        $conversations = $messages->groupBy(function($message){
            return collect([$message->sender_id,$message->receiver_id])->sort()->implode('_');
        })->map(function($groupedMessages){
            return [
                'user_id1' => $groupedMessages->first()->sender_id,
                'user_id2' => $groupedMessages->first()->receiver_id,
                'last_message_id' => $groupedMessages->last()->id,
                'created_at' => new Carbon(),
                'updated_at' => new Carbon(),

            ];
        })->values();

        Conversation::insertOrIgnore($conversations->toArray());

    }
}
