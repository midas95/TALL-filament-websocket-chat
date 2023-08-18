<div class="container">
    <div class="flex">
        <div class="grow max-w-xs p-2 mr-3 border-solid border-2 border-slate-500 ">
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent"
                    role="tablist">
                    <li
                        class="flex-1 w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 cursor-pointer">
                        Bizs
                    </li>
                    <li
                        class="flex-1 w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 cursor-pointer">
                        Bookings
                    </li>
                    <li class="flex-1 w-full p-4 border-b-2 rounded-t-lg cursor-pointer flex items-center ">
                        Private
                        @if( $totalUnread > 0)
                        <div class="rounded-full h-5 w-5 ml-1 flex items-center justify-center bg-red-500 text-white">{{$totalUnread}}</div>
                        @endif
                    </li>
                </ul>
            </div>
            <div class='flex items-center'>
                @if($searchWord !== '')
                    <div class='mt-3' wire:click="resetSearch">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="32" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </div>
                @endif
                <input wire:model="searchWord" class="rounded-md w-full mt-3" placeholder="Search Biz or Person">
            </div>
            <ul class="h-[65vh] overflow-y-auto">
                <h3 class='text-xl font-bold py-2'>Conversations</h3>
                @php
                    $conversationCounter = 0;
                @endphp
                @foreach ($conversations as $c)
                    @if (stripos($c->interlocutor()->name, $searchWord) !== false)
                        @php
                                $conversationCounter++;
                        @endphp                    
                        <li wire:click="openConversation({{ $c->id }})"
                            class="flex items-center cursor-pointer relative {{ $conversation && $conversation->id === $c->id ? 'bg-gray-200' : '' }}">
                            @if($onlineState[$c->interlocutor()->id])
                                <div class="absolute items-center justify-center w-4 h-4 text-xs font-bold text-white bg-green-500 border-2 border-white rounded-full top-1 left-9"></div>
                            @endif
                            <img src='{{ $c->interlocutor()->getAvatarUrl() }}' alt='avatar'
                                class='m-2 w-10 h-10 rounded-3xl' /> {!! $this->emphasize($c->interlocutor()->name, $searchWord) !!}
                                @if( $numUnread[$c->id] > 0)
                                    <div class="rounded-full h-5 w-5 ml-1 flex items-center justify-center bg-red-500 text-white">{{$numUnread[$c->id]}}</div>
                                @endif
                        </li>
                    @endif
                @endforeach
                @if($conversationCounter === 0)
                    <div class='text-center'>There is no matched Conversation</div>
                @endif
                @if($searchWord !== '')
                    <h3 class='text-xl font-bold py-2'>Users</h3>
                    @php
                        $userCounter = 0;
                    @endphp
                    @foreach ($users as $u)
                        @if (stripos($u->name, $searchWord) !== false && $u->id !== $myUserId)
                            @php
                                $userCounter++;
                            @endphp
                            <li wire:click="createConversation({{ $u->id }})"
                                class="flex items-center cursor-pointer">
                                <img src='{{ $u->getAvatarUrl() }}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
                                {!! $this->emphasize($u->name, $searchWord) !!}
                            </li>
                        @endif
                    @endforeach
                    @if($userCounter===0)
                        <div class='text-center'>There is no matched User</div>
                    @endif
                @endif
            </ul>
        </div>
        <div class='grow'>
            @if ($conversation)
                <div class='flex items-center border-b-2 border-slate-500 relative'>
                    <img src='{{ $conversation->interlocutor()->getAvatarUrl() }}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
                    <span>{{ $conversation->interlocutor()->name }}</span>
                    <span class='absolute text-xs bottom-0.5 left-12'>Last online {{$lastOnline}}</span>
                </div>
                <div class="h-[65vh] overflow-y-auto" id="chatBox">
                    <?php $uId = 0;
                    $repeated = false; ?>
                    @foreach ($messages as $message)
                        <?php if ($uId != $message->user_id) {
                            $uId = $message->user_id;
                            $repeated = false;
                        } else {
                            $repeated = true;
                        }  ?>
                        <x-chat.message :$message :$myUserId :$repeated :$conversation/>
                    @endforeach
                </div>
                <x-chat.input :$isTypingInterlocutor :$conversation :$answeredMessage/>
            @endif
        </div>
    </div>
</div>


<script>
    let readTimecounter;
    let typingTimer;
    document.addEventListener('livewire:load', function() {
        Livewire.on('updateMessages', (read = true) => {
            clearTimeout(readTimecounter)
            let chatBox = document.getElementById("chatBox");
            chatBox.scrollTop = chatBox.scrollHeight;
            if(read) {
                readTimecounter = setTimeout(()=>{
                    @this.readMessage()
                },3000)
            }
        })
    })
    const setTyping = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (e.ctrlKey) {
                let value = e.target.value + '\r\n';
                @this.content = value;
            } else {
                @this.send();
            }
        } else if ( e.key ==='ArrowUp' && @this.content.trim() === '' ){
            @this.editMessage();
        } 
        else {
            clearTimeout(typingTimer);
            @this.broadcastTyping(true);
            typingTimer = setTimeout(() => {
                @this.broadcastTyping(false);
            }, 1000);
        }
    }
    const showAction = (e) => {
        let action = e.target.nextElementSibling;
        if (action) {
         action.style.visibility = 'visible';
         }
    }
    const editAction = (id)=>{
        @this.editMessage(id);
    }
    const answerAction = (id)=>{
        @this.answerMessage(id);
    }
    const cancelAnswer = ()=>{
        @this.answerMessage();
    }
    const markAction = (id)=>{
        @this.markMessage(id);
    }

    document.addEventListener('mouseup', function(e) {
        let container = document.querySelector('.action[style*="visibility: visible"]');
        if(container){
            container.style.visibility = 'hidden'
        }
    });
</script>
