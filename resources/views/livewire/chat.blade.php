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
                    <li class="flex-1 w-full p-4 border-b-2 rounded-t-lg cursor-pointer">
                        Private
                    </li>
                </ul>
            </div>
            <input wire:model="searchWord" class="rounded-md w-full mt-3" placeholder="Search Biz or Person">
            <ul class="h-[65vh] overflow-y-auto">
                <h3 class='text-xl font-bold py-2'>Conversations</h3>
                @foreach ($conversations as $c)
                    @if (stripos($c->interlocutor()->name, $searchWord) !== false)
                        <li wire:click="openConversation({{ $c->id }})"
                            class="flex items-center cursor-pointer {{ $conversation && $conversation->id === $c->id ? 'bg-gray-200' : '' }}">
                            <img src='{{ $c->interlocutor()->getAvatarUrl() }}' alt='avatar'
                                class='m-2 w-10 h-10 rounded-3xl' /> {!! $this->emphasize($c->interlocutor()->name, $searchWord) !!}
                        </li>
                    @endif
                @endforeach
                <h3 class='text-xl font-bold py-2'>Users</h3>
                @foreach ($users as $u)
                    @if (stripos($u->name, $searchWord) !== false && $u->id !== $myUserId)
                        <li wire:click="createConversation({{ $u->id }})"
                            class="flex items-center cursor-pointer">
                            <img src='{{ $u->getAvatarUrl() }}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
                            {!! $this->emphasize($u->name, $searchWord) !!}
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class='grow'>
            <div class='flex items-center border-b-2 border-slate-500'>
                <img src='{{ auth()->user()->getAvatarUrl() }}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
                <span>{{ auth()->user()->name }}</span>
            </div>
            @if ($conversation)
                <div class="h-[65vh] overflow-y-auto" id="chatBox">
                    <?php $uId = 0;
                    $repeated = false; ?>
                    @foreach ($messages as $message)
                        <?php if ($uId != $message->user_id) {
                            $uId = $message->user_id;
                            $repeated = false;
                        } else {
                            $repeated = true;
                        } ?>
                        <x-chat.message :$message :$myUserId :$repeated :$conversation :$loop />
                    @endforeach
                </div>
                <x-chat.input />
            @endif
        </div>
    </div>
</div>


<script>
    document.addEventListener('livewire:load', function() {
        Livewire.on('updateMessages', () => {
            var chatBox = document.getElementById("chatBox");
            chatBox.scrollTop = chatBox.scrollHeight;
        })
    })
</script>
