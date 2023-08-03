<?php

namespace App\Http\Livewire;

use App\Events\NewChatMessage;
use Livewire\Component;
use App\Models\Message;
use App\Models\Conversation;

class Chat extends Component
{
    public $targetID = 6;
    public $userAvatarPath = 'assets/avatar/5.jfif';
    public $targetAvatarPath = 'assets/avatar/6.jfif';
    public $conversation = [
        'type' => 'private',
        'participant_a_id' => Null,
        'participant_b_id' => Null,
        'booking_id' => Null,
    ];

    public $chat_list;
    public $content;
    public $message = [
        'conversation_id' => Null,
        'user_id' => Null,
        'answered_message_id' => Null,
        'activity_id' => Null,
        'mark' => Null,
        'seen' => Null,

    ];

    public function mount()
    {
        $this->message['user_id'] = $this->conversation['participant_a_id'] = 5;
        // auth()->user()->id;
        $this->conversation['participant_b_id'] = $this->targetID;
        $this->getConversation();
        $this->getChatHistory();

    }

    public function send()
    {
        if (is_null($this->content)) {
            return;
        }
        $message = new Message;
        $message->conversation_id = $this->message['conversation_id'];
        $message->user_id = $this->message['user_id'];
        $message->answered_message_id = $this->message['answered_message_id'];
        $message->activity_id = $this->message['activity_id'];
        $message->mark = $this->message['mark'];
        $message->content = $this->content;
        $message->seen = $this->message['seen'];
        $message->save();

        // $chat_user = User::find($this->chat_id);
        //sending event with message content and the user Isend it to , and the numb of the Unreaded Messages
        event(new NewChatMessage($this->content, $this->message['user_id']));
        $this->getChatHistory();
        $this->reset('content');
    }

    public function getChatHistory()
    {
        $this->chat_list = Message::where('conversation_id', $this->message['conversation_id'])->orderBy('id', 'asc')->get();
    }
    public function getConversation()
    {
        if (Conversation::where('participant_a_id', $this->message['user_id'])->where('participant_b_id', $this->targetID)) {
            $this->message['conversation_id'] = Conversation::where('participant_a_id', $this->message['user_id'])->where('participant_b_id', $this->targetID)->value('id');
        } else {
            $conversation = new Conversation;
            $conversation->type = $this->conversation['type'];
            $conversation->participant_a_id = $this->conversation['participant_a_id'];
            $conversation->participant_b_id = $this->conversation['participant_b_id'];
            $conversation->booking_id = $this->conversation['booking_id'];
            $conversation->save();

            $this->message['conversation_id'] = $conversation->id;            
        }

    }
    public function render()
    {
        return view('livewire.chat');
    }
}