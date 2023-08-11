<?php

namespace App\Http\Livewire;

use App\Events\NewChatMessage;
use App\Events\ReadMessages;
use App\Events\setTyping;
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
    public $onlineState = [];
    public $numUnread = [];
    public $totalUnread = 0;
    public $content;
    public $searchWord = '';
    public $isTypingInterlocutor = false;

    public function getListeners()
    {
        return [
            'echo:chat,NewChatMessage' => 'getNewMessage',
            'echo:read,ReadMessages' => 'getReadMessages',
            'echo:typingState,setTyping' => 'setTyping',
            'readMessage' => 'readMessage',
            'setTyping' => 'broadcastTyping'
        ];
    }

    /**
     * get conversation collection from conversation ID
     */
    public function openConversation($conversationId)
    {
        $this->conversation = Conversation::find($conversationId);
        $this->getMessages();
        $this->reset('content');
    }

    public function mount()
    {
        $this->myUserId = auth()->user()->id;
        $this->conversations = auth()->user()->conversations();
        $this->getUnreadMessage();
        $this->getUser();
    }

    /**
     * Send message
     */
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

    /**
     * emit 'updateMessage' livewire event to update message history.
     */
    public function getMessages()
    {
        $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
        $this->emit('updateMessages');
    }

    /**
     * event handler for broadcast event : NewChatMessage
     */
    public function getNewMessage($event)
    {
        $message = Message::find($event['messageId']);
        $this->getUnreadMessage();
        if ($this->conversation !== null && $message->conversation->id === $this->conversation->id) {
            $message['user_id'] !== auth()->user()->id && $this->messages->push($message);
            $this->emit('updateMessages');
        }
    }

    /**
     * emphasize search word in string.
     */
    public function emphasize($string, $word)
    {
        $index1 = stripos($string, $word);
        $index2 = strlen($word);
        return substr($string, 0, $index1) . '<b>' . substr($string, $index1, $index2) . '</b>' . substr($string, $index1 + $index2);
    }

    /**
     * Get users without users who have conversation
     */
    public function getUser()
    {
        $interlocutorIds = $this->conversations->map(function ($conversation) {
            return $conversation->interlocutor(true);
        })->all();
        $this->users = User::whereNotIn('id', $interlocutorIds)
            ->get();
    }

    /**
     * Create conversation with interlocutorId
     */
    public function createConversation($interlocutorId)
    {
        $this->conversation = Conversation::create([
            'type' => 'private',
            'participant_a_id' => $this->myUserId,
            'participant_b_id' => $interlocutorId,
        ]);
        $this->conversations = auth()->user()->conversations();
        $this->getUser();
        $this->getUnreadMessage();
    }

    /**
     * livewire event handler that make unread message to read message
     */
    public function readMessage()
    {
        Message::where('conversation_id', $this->conversation->id)->where('user_id', '!=', $this->myUserId)->whereNull('seen')->update(['seen' => now()]);
        $this->getUnreadMessage();
        broadcast(new ReadMessages($this->conversation->id))->toOthers();
    }

    /**
     * Broadcast event handler that get message to update reading state.
     */
    public function getReadMessages($event)
    {
        if ($this->conversation !== null && $event['conversationId'] === $this->conversation->id) {
            $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
        }
    }

    /**
     * Get number of each unread messages and total unread messages.
     */
    public function getUnreadMessage()
    {
        foreach ($this->conversations as $conversation) {
            $this->numUnread[$conversation->id] = Message::where('conversation_id', $conversation->id)->where('user_id', '!=', $this->myUserId)->whereNull('seen')->count();
        }
        $this->totalUnread = array_sum($this->numUnread);
    }

    /**
     * broadcast event handler that set typing state. 
     */
    public function setTyping($event)
    {
        if ($this->conversation !== null && $event['conversationId'] === $this->conversation->id) {
            $this->isTypingInterlocutor = $event['isTyping'];
        }
    }

    /**
     * livewire event handler that emit broadcast event : setTyping
     * 
     */
    public function broadcastTyping($event)
    {
        broadcast(new setTyping($this->conversation->id, $event))->toOthers();
    }

    /**
     * check online state in every request.
     */
    public function booted()
    {
        $users = User::all();
        foreach ($users as $user) {
            $this->onlineState[$user->id] = ($user->activity < now()) ? false : true;
        }
    }

    public function render()
    {
        return view('livewire.chat');
    }
}