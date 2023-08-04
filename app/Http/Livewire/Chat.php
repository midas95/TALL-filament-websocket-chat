<?php

namespace App\Http\Livewire;

use App\Events\NewChatMessage;
use Livewire\Component;
use App\Models\Message;
use App\Models\Conversation;
use Livewire\Request;


class Chat extends Component
{
    public $targetID;
    public $userAvatarPath;
    public $targetAvatarPath;
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
    protected $listeners = ['echo:chat,NewChatMessage' => 'getNewMessage'];

    public function mount()
    {
        $this->message['user_id'] = $this->conversation['participant_a_id'] = auth()->user()->id;
        if ($this->message['user_id'] == 5) {
            $this->targetID = 6;
            $this->userAvatarPath = 'assets/avatar/5.jfif';
            $this->targetAvatarPath = 'assets/avatar/6.jfif';
        } elseif ($this->message['user_id'] == 6) {
            $this->targetID = 5;
            $this->userAvatarPath = 'assets/avatar/5.jfif';
            $this->targetAvatarPath = 'assets/avatar/6.jfif';
        } 
        elseif ($this->message['user_id'] == 2) {
            $this->targetID = 7;
            $this->userAvatarPath = 'assets/avatar/5.jfif';
            $this->targetAvatarPath = 'assets/avatar/6.jfif';
        } else {
            $this->targetID = 2;
            $this->userAvatarPath = 'assets/avatar/6.jfif';
            $this->targetAvatarPath = 'assets/avatar/5.jfif';
        }

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
        broadcast(new NewChatMessage($this->content, $this->message['user_id'], $this->targetID, $message->id))->toOthers();
        $this->chat_list->push($message);
        $this->reset('content');
    }

    public function getChatHistory()
    {
        $this->chat_list = Message::where('conversation_id', $this->message['conversation_id'])->orderBy('id', 'asc')->get();
    }
    public function getConversation()
    {
        $this->message['conversation_id'] = Conversation::where('participant_a_id', $this->message['user_id'])->where('participant_b_id', $this->targetID)->value('id') ?? Conversation::where('participant_a_id', $this->targetID)->where('participant_b_id', $this->message['user_id'])->value('id');
        if (!$this->message['conversation_id']) {
            $conversation = new Conversation;
            $conversation->type = $this->conversation['type'];
            $conversation->participant_a_id = $this->conversation['participant_a_id'];
            $conversation->participant_b_id = $this->targetID;
            $conversation->booking_id = $this->conversation['booking_id'];
            $conversation->save();

            $this->message['conversation_id'] = $conversation->id;
        }
    }
    public function getNewMessage($event)
    {
        if ($event['to'] == auth()->user()->id) {
            if ($event['from'] == $this->targetID) {
                // now i am chatting
                $message = Message::find($event['messageID']);
                // dd($message);
                $this->chat_list->push($message);
            } else {
                // notification on

            }
        }
    }
    public function render()
    {
        return view('livewire.chat');
    }
}