<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Message;
use App\Models\Conversation;

class Chat extends Component
{
    public $userID = 5;
    public $userAvatarPath= 'assets/avatar/5.jfif';
    public $targetID = 6;
    public $targetAvatarPath= 'assets/avatar/6.jfif';
    public $conversationID;
    public $chat_list;
    public function mount()
    {
        // $this->userID = @auth()->user()->id);
        $this->getChatHistory();

    }

    public function getChatHistory(){
        $this->conversationID = Conversation::where('participant_a_id', $this->userID)->where('participant_b_id', $this->targetID)->value('id');
        $this->chat_list = Message::where('conversation_id', $this->conversationID)->orderBy('id', 'asc')->get();
    }
    public function render()
    {
        return view('livewire.chat');
    }
}