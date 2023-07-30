<?php

namespace Database\Seeders;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Movie;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ConversationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user1 = User::where('email', 'test1@test.com')->first();
        $user2 = User::where('email', 'test2@test.com')->first();


        $conversation = Conversation::create([
            'type' => 'private',
            'participant_a_id' => $user1->id,
            'participant_b_id' => $user2->id,
            'booking_id' => null,
        ]);

        $message1 = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user1->id,
            'answered_message_id' => null,
            'activity_id' => null,
            'mark' => null,
            'content' => 'Hello, can I ask you something?',
            'seen' => Carbon::now()->subMinutes(15),
            'created_at' => Carbon::now()->subMinutes(22),
        ]);

        $message2 = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user2->id,
            'answered_message_id' => null,
            'activity_id' => null,
            'mark' => null,
            'content' => "Hi, sure - what's on your mind?",
            'seen' => Carbon::now()->subMinutes(13),
            'created_at' => Carbon::now()->subMinutes(18),
        ]);

        $message3 = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user1->id,
            'answered_message_id' => null,
            'activity_id' => null,
            'mark' => null,
            'content' => 'I was just wondering how many visitors where in the movie "Last Samurai"?',
            'seen' => Carbon::now()->subMinutes(5),
            'created_at' => Carbon::now()->subMinutes(19),
        ]);

        $message4 = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user1->id,
            'answered_message_id' => null,
            'activity_id' => null,
            'mark' => null,
            'content' => 'And is it still possible to get the movie "Around the World"?',
            'seen' => Carbon::now()->subMinutes(4),
            'created_at' => Carbon::now()->subMinutes(16),
        ]);

        $message5 = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user2->id,
            'answered_message_id' => $message3->id,
            'activity_id' => null,
            'mark' => null,
            'content' => "Don't know, sorry. Take a look at https://www.facebook.com/TheLastSamuraiFilm/",
            'seen' => null,
            'created_at' => Carbon::now()->subMinutes(15),
        ]);


    }
}
