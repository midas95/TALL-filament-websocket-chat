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
    public $content = '';
    public $searchWord = '';
    public $isTypingInterlocutor = false;
    public $lastOnline = '';
    public $editMessageId = 0;
    public $myLastMessageId = 0;
    public $answeredMessage;

    public function getListeners()
    {
        return [
            'echo:chat,NewChatMessage' => 'getNewMessage',
            'echo:read,ReadMessages' => 'getReadMessages',
            'echo:typingState,setTyping' => 'setTyping',
        ];
    }

    /**
     * get conversation collection from conversation ID
     */
    public function openConversation($conversationId)
    {
        $this->conversation = Conversation::find($conversationId);
        $this->getLastOnline();
        $this->getMessages();
        $this->reset('content');
        $this->reset('answeredMessage');
        $this->resetSearch();
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
        if ($this->answeredMessage) {
            $answeredMessageId = $this->answeredMessage->id;
        } else {
            $answeredMessageId = null;
        }
        if ($this->editMessageId === 0) {
            $message = Message::create([
                'conversation_id' => $this->conversation->id,
                'user_id' => $this->myUserId,
                'content' => $this->content,
                'answered_message_id' => $answeredMessageId,
            ]);
            broadcast(new NewChatMessage($message->id, $this->conversation->id))->toOthers();
            $this->messages->push($message);
            $this->emit('updateMessages', false);
        } else {
            Message::find($this->editMessageId)->update(['content' => $this->content, 'answered_message_id' => $answeredMessageId, 'updated_at' => now(), 'edited' => true]);
            broadcast(new ReadMessages($this->conversation->id))->toOthers();
            $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
            $this->reset('editMessageId');
        }
        $this->reset('content');
        $this->reset('answeredMessage');
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
        $this->getLastOnline();
        $this->conversations = auth()->user()->conversations();
        $this->getUser();
        $this->getUnreadMessage();
        $this->resetSearch();
    }

    /**
     * function that make unread message to read message
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
     * Show last online time
     */
    public function getLastOnline()
    {
        $activity = $this->conversation->interlocutor()->activity;
        $dateTime = new \DateTime($activity);
        $dateTime->sub(new \DateInterval('PT5M'));
        $this->lastOnline = $dateTime->format('d-m, l, H:i');
    }
    /**
     * to get message to edit.
     */
    public function editMessage($id = 0)
    {
        if ($id === 0) {
            $message = Message::where('conversation_id', $this->conversation->id)->where('user_id', $this->myUserId)->latest()->first();
            $this->editMessageId = $message->id;
            $this->content = $message->content;
            $this->answeredMessage = Message::find($message->answered_message_id);
        } else {
            $this->editMessageId = $id;
            $message = Message::find($this->editMessageId);
            $this->content = $message->content;
            $this->answeredMessage = Message::find($message->answered_message_id);
        }
    }
    /**
     * return message collection or set answered message.
     */
    public function answerMessage($id = null, $returnable = false)
    {
        if ($id) {
            if ($returnable) {
                return Message::find($id);
            } else {
                $this->answeredMessage = Message::find($id);
            }
        } else {
            $this->reset('answeredMessage');
        }
    }

    /**
     * Set mark id in message mark
     */
    public function markMessage($id)
    {
        $message = Message::find($id);
        $mark = explode(',', $message->mark);
        $key = array_search(strval($this->myUserId), $mark);

        if ($key === false) {
            array_push($mark, strval($this->myUserId));
        } else {
            unset($mark[$key]);
        }
        $message->mark = implode(',', $mark);
        $message->save();
        $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
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
     * handler that emit broadcast event : setTyping
     * 
     */
    public function broadcastTyping($event)
    {
        broadcast(new setTyping($this->conversation->id, $event))->toOthers();
    }

    /**
     * reset search word to go back from search
     */
    public function resetSearch()
    {
        $this->reset('searchWord');
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