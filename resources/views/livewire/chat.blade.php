<div class="container">
    <div class="flex">
        <div class="grow max-w-xs">
            <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                <ul class="flex text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                    <li class="flex-1 w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 cursor-pointer">
                        Bizs
                    </li>
                    <li class="flex-1 w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 cursor-pointer">
                        Bookings
                    </li>
                    <li class="flex-1 w-full p-4 border-b-2 rounded-t-lg cursor-pointer">
                        Private
                    </li>
                </ul>
            </div>
            <ul>
                @foreach ($conversations as $c)
                    <li wire:click="openConversation({{$c->id}})" class="flex items-center cursor-pointer {{$conversation && $conversation->id == $c->id ? 'bg-gray-200' : ''}}"><img src='{{$c->interlocutor()->getAvatarUrl()}}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' /> {{$c->interlocutor()->name}}</li>
                @endforeach
            </ul>
            <input class="rounded-md w-full mt-3" placeholder="Search Biz or Person" >
        </div>
        @if($conversation)
            <div class="grow overflow-y-auto h-full" >
                <?php $uId = 0; $repeated = false; ?>
                @foreach ($messages as $message)
                    <?php if($uId != $message->user_id) {$uId = $message->user_id; $repeated = false;} else {$repeated = true;}  ?>
                    <x-chat.message :$message :$myUserId :$repeated :$conversation :$loop />
                @endforeach
                <x-chat.input />
            </div>
        @endif
    </div>



</div>

