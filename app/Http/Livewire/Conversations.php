<?php

namespace App\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;

class Conversations extends Component
{
    use ChatHelper;

    public $conversations = [];
    public $activeConversation;
    public $myUserId;

    public $totalUnread = 0;
    public $search = '';
    public $foundConversations = [];
    public $foundUsers = [];

    public function getListeners()
    {
        return [
            'newChatMessageLocal' => 'newChatMessageLocalHandler',
            'readMessagesLocal' => 'readMessagesLocalHandler',
        ];
    }

    public function newChatMessageLocalHandler($event)
    {
        $message = Message::find($event['messageId']);

        if($message['user_id'] === auth()->id()) {
            return;
        }
        $this->getConversations();
    }

    public function readMessagesLocalHandler($event)
    {
        if(auth()->id() === $event['userId']) {
            $this->getConversations();
        }
    }

    /**
     * Open conversation from conversationId.
     */
    public function openConversation($conversationId)
    {
        $this->resetSearch();
        $this->activeConversation = Conversation::find($conversationId);
        $this->emit('showConversation', $this->activeConversation->id);
    }

    public function mount()
    {
        $this->myUserId = auth()->user()->id;
        $this->getConversations();
        $this->openConversation(2);
    }

    /**
     * Get existing conversations with interlocutor informations and unread counts.
     */
    public function getConversations()
    {
        $this->conversations = auth()->user()
            ->conversationsWithInterlocutorName()
            ->withCount('unreadMessages')->get()->toArray();

        // get sum of all unread messages
        $this->totalUnread = array_sum(Arr::pluck($this->conversations, 'unread_messages_count'));
    }

    /**
     * Create conversation with interlocutorId.
     */
    public function createConversation($interlocutorId)
    {
        $this->activeConversation = Conversation::create([
            'type' => 'private',
            'participant_a_id' => $this->myUserId,
            'participant_b_id' => $interlocutorId,
        ]);

        $this->getConversations();
        $this->openConversation($this->activeConversation->id);
    }

    /**
     * Reset search attribute to close search.
     */
    public function resetSearch()
    {
        $this->reset('search');
    }

    /**
     * Hook on search attribute, triggered by input.
     */
    public function updatedSearch($value)
    {

        $ignoredIds = [Auth::id(), ...Arr::pluck($this->conversations, 'interlocutorId')];

        $this->foundUsers = User::select(['id', 'name', 'activity'])
            ->where('name', 'like', '%'.$value.'%')
            ->whereNotIn('id', $ignoredIds)
            ->get();

        $this->foundConversations = Arr::where($this->conversations, function ($c) use ($value) {
            return stripos($c['interlocutorName'], $value) !== false;
        });
    }

    public function render()
    {
        return view('livewire.conversations');
    }
}
