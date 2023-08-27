<?php

namespace App\Http\Livewire;

use App\Events\IsTyping;
use App\Events\NewChatMessage;
use App\Events\ReadMessages;
use Livewire\Component;
use App\Models\Message;
use App\Models\Conversation;

class Chat extends Component
{

    public $conversationId;
    public $conversation;
    public $myUserId;
    public $messages = [];

    public $content = '';
    public $typingData = null;
    public $lastOnline = '';
    public $editMessageId = 0;
    public $answerMessage;

    public $alpineInit = false;

    public function getListeners()
    {
        return [
            'echo:chat,NewChatMessage' => 'getNewMessage',
            'echo:read,ReadMessages' => 'getReadMessages',
            'echo:typingState,IsTyping' => 'setTyping',
            'showConversation',
            'broadcastTyping'
        ];
    }

    public function mount()
    {
        $this->myUserId = auth()->user()->id;
        $this->showConversation($this->conversationId);
    }

    /**
     * Show conversation by id
     */
    public function showConversation($conversationId)
    {
        $this->conversation = Conversation::find($conversationId);
        $this->getLastOnline();
        $this->getMessages();
        $this->reset('content');
        $this->reset('answerMessage');

    }

    /**
     * Send message
     */
    public function send()
    {
        if (empty(trim($this->content))) {
            return;
        }

        $answeredMessageId = null;
        if ($this->answerMessage) {
            $answeredMessageId = $this->answerMessage->id;
        }

        if ($this->editMessageId === 0) {
            // new message
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
            // edit message
            Message::find($this->editMessageId)
                ->update([
                    'content' => $this->content,
                    'answered_message_id' => $answeredMessageId
                ]);
            broadcast(new ReadMessages($this->conversation->id, auth()->id()))->toOthers();
            $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
            $this->reset('editMessageId');
        }
        $this->reset('content');
        $this->reset('answerMessage');
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
     * Create conversation with interlocutorId
     */
    public function createConversation($interlocutorId)
    {
        $this->conversation = Chat::create([
            'type' => 'private',
            'participant_a_id' => $this->myUserId,
            'participant_b_id' => $interlocutorId,
        ]);
        $this->getLastOnline();
    }

    /**
     * Mark messages as read and broadcast ReadMessages
     */
    public function readMessage()
    {
        Message::where('conversation_id', $this->conversation->id)
            ->where('user_id', '!=', $this->myUserId)
            ->whereNull('seen')
            ->withoutTimestamps()
            ->update(['seen' => now()]);
        broadcast(new ReadMessages($this->conversation->id, auth()->id()));
    }

    /**
     * Show last online time
     */
    public function getLastOnline()
    {
        $activity = $this->conversation->interlocutor()->activity;

        $lastOnline = $activity->isoFormat('dddd, Y-mm-dd [at] HH:mm');

        if($activity->isToday()){
            $minutes = $activity->diffInMinutes();
            if($minutes < 60) {
                $lastOnline = $activity->diffForHumans();
            }else{
                $lastOnline = 'today at ' . $activity->format('H:i');
            }
        }else if($activity->isYesterday()){
            $lastOnline = 'yesterday at ' . $activity->format('H:i');
        }else if($activity->diffInDays() <= 6){
            $lastOnline = $activity->isoFormat('dddd [at] HH:mm');
        }

        $this->lastOnline = $lastOnline;
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
            $this->answerMessage = Message::find($message->answered_message_id);
        } else {
            $this->editMessageId = $id;
            $message = Message::find($this->editMessageId);
            $this->content = $message->content;
            $this->answerMessage = Message::find($message->answered_message_id);
        }
    }

    public function cancelEdit()
    {
        $this->reset('answerMessage');
        $this->reset('editMessageId');
        $this->reset('content');

    }

    public function setAnswerMessage($id)
    {
        $this->answerMessage = Message::find($id);
    }

    public function resetAnswerMessage()
    {
        $this->reset('answerMessage');
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

        Message::where('id', $id)->withoutTimestamps()->update(['mark' => implode(',', $mark)]);
        $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
    }

    /**
     * broadcast: IsTyping
     */
    public function broadcastTyping($typing)
    {
        broadcast(new IsTyping($this->conversation->id, $typing, auth()->user()->only(['id', 'name'])))->toOthers();
    }

    /**
     * Event handlers
     *
     */

    /**
     * broadcast event handler: add message and emit updateMessages for interlocutor if conversation is active
     */
    public function getNewMessage($event)
    {
        // workaround for echo:Listeners on multiple components
        $this->emit('newChatMessageLocal', $event);

        $message = Message::find($event['messageId']);

        if($message['user_id'] === auth()->id()) {
            return;
        }

        if (
            $this->conversation !== null &&
            $this->conversation->id === $message->conversation_id

        ) {
            $this->messages->push($message);
            $this->emit('updateMessages');
        }

    }

    /**
     * broadcast event handler: get Messages with updated reading state
     */
    public function getReadMessages($event)
    {
        // workaround for echo:Listeners on multiple components
        $this->emit('readMessagesLocal', $event);

        if ($this->conversation !== null && $event['conversationId'] === $this->conversation->id) {
            $this->messages = Message::where('conversation_id', $this->conversation->id)->orderBy('id', 'asc')->get();
        }
    }

    /**
     * broadcast event handler: set typingUser
     */
    public function setTyping($event)
    {
        if($this->conversation == null){
            return;
        }
        if ( $this->conversation->id === $event['conversationId'] || ($this->typingData && $this->typingData['conversationId'] === $event['conversationId'])) {
            if($event['isTyping']){
                $this->typingData = $event;
            }else if($this->typingData && $this->typingData['user']['id'] == $event['user']['id']){
                $this->typingData = null;
            }
        }
    }


    /**
     * check online state in every request.
     */
    public function updatedAlpineInit($value)
    {
        $this->emit('updateMessages');
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
