<?php

namespace App\Http\Livewire;

use App\Events\NewChatMessage;
use App\Events\ReadMessages;
use Livewire\Component;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;

class Chat extends Component
{

    public $conversations;
    public $conversation;
    public $users;
    public $myUserId;
    public $messages = [];
    public $content;
    public $searchWord = '';
    public function getListeners()
    {
        return [
            'echo:chat,NewChatMessage' => 'getNewMessage',
            'echo:read,ReadMessages' => 'getReadMessages',
            'readMessage' => 'readMessage',
        ];
    }

    public function openConversation($conversationId)
    {
        $this->conversation = Conversation::find($conversationId);
        $this->getMessages();
        $this->reset('content');
    }
    public function mount()
    {
        $this->conversations = auth()->user()->conversations();
        $this->getUser();
        $this->myUserId = auth()->user()->id;
    }

    public function send()
    {
        if (empty(trim($this->content))) {
            return;
        }
        $message = Message::create([
            'conversation_id' => $this->conversation->id,
            'user_id' => $this->myUserId,
            'content' => $this->content,
        ]);

        broadcast(new NewChatMessage($message->id, $this->conversation->id))->toOthers();
        $this->messages->push($message);
        $this->emit('updateMessages', false);
        $this->reset('content');
    }

    public function getMessages()
    {
        $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
        $this->emit('updateMessages');
    }
    public function getNewMessage($event)
    {
        $message = Message::find($event['messageId']);
        if ($this->conversation !== null && $message->conversation->id === $this->conversation->id) {
            $message['user_id'] !== auth()->user()->id && $this->messages->push($message);
            $this->emit('updateMessages');
        } else {
        }
    }
    public function emphasize($string, $word)
    {
        $index1 = stripos($string, $word);
        $index2 = strlen($word);
        return substr($string, 0, $index1) . '<b>' . substr($string, $index1, $index2) . '</b>' . substr($string, $index1 + $index2);
    }
    public function getUser()
    {
        $interlocutorIds = $this->conversations->map(function ($conversation) {
            return $conversation->interlocutor(true);
        })->all();
        $this->users = User::whereNotIn('id', $interlocutorIds)
            ->get();
    }
    public function createConversation($targetId)
    {
        $this->conversation = Conversation::create([
            'type' => 'private',
            'participant_a_id' => $this->myUserId,
            'participant_b_id' => $targetId,
        ]);
        $this->conversations = auth()->user()->conversations();
        $this->getUser();
    }
    public function readMessage()
    {
        Message::where('conversation_id', $this->conversation->id)->where('user_id', '!=', $this->myUserId)->whereNull('seen')->update(['seen' => now()]);
        broadcast(new ReadMessages($this->conversation->id))->toOthers();
    }
    public function getReadMessages($event)
    {
        if($event['conversationId'] === $this->conversation->id){
            $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
        }
    }
    public function render()
    {
        return view('livewire.chat');
    }
}