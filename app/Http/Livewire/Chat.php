<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Message;
use App\Models\Conversation;

class Chat extends Component
{
    public $userID = 5;
    public $targetID = 6;
    public $conversationID;
    public $chat_list;
    public function mount()
    {
        // $this->userID = @auth()->user()->id);
        $this->getChatHistory();

    }

    public function getChatHistory(){
        $this->conversationID = Conversation::where('participant_a_id', $this->userID)->where('participant_b_id', $this->targetID)->value('id');
        $this->chat_list = Message::where('conversation_id', $this->conversationID)->orderBy('id', 'desc')->get();
    }
    public function render()
    {
        return view('livewire.chat');
    }
}