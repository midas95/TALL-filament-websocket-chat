
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
        @if($search !== '')
            <div class='mt-3' wire:click="resetSearch">
                <x-chat.icons name='chevron-left' height='24' width="24" class="my-auto text-red-800 cursor-pointer" />
            </div>
        @endif
        <input wire:model.debounce.300ms="search" @keydown.escape="@this.resetSearch()" type="text" class="rounded-md w-full mt-3 text-black" placeholder="Search Biz or Person">
    </div>
    <div class="h-[65vh] overflow-y-auto">
        @if($search == '')
            <h3 class='text-xl font-bold py-2'>Conversations</h3>
            @foreach ($conversations as $c)
                <div wire:click="openConversation({{ $c['id'] }})" class="flex items-center cursor-pointer relative  {{ $activeConversation && $activeConversation->id === $c['id'] ? 'bg-gray-200 text-black' : '' }}">
                    @if($this->isOnline($c['interlocutorActivity']))
                        <div class="absolute items-center justify-center w-4 h-4 text-xs font-bold text-white bg-green-500 border-2 border-white rounded-full top-1 left-9"></div>
                    @endif
                    <img src="{{ $this->getAvatarUrl($c['interlocutorName']) }}" alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
                    {{$c['interlocutorName']}}
                    @if($c['unread_messages_count'] > 0)
                        <div class="rounded-full h-5 w-5 ml-1 flex items-center justify-center bg-red-500 text-white">{{$c['unread_messages_count']}}</div>
                    @endif
                </div>
            @endforeach
        @else
            <h3 class=' font-bold py-2'>Conversations</h3>
            @forelse($foundConversations as $c)
                <div wire:click="openConversation({{ $c['id'] }})" class="flex items-center cursor-pointer relative  {{ $activeConversation && $activeConversation->id === $c['id'] ? 'bg-gray-200 text-black' : '' }}">
                    @if($this->isOnline($c['interlocutorActivity']))
                        <div class="absolute items-center justify-center w-4 h-4 text-xs font-bold text-white bg-green-500 border-2 border-white rounded-full top-1 left-9"></div>
                    @endif
                    <img src="{{ $this->getAvatarUrl($c['interlocutorName']) }}" alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />

                    {!! $this->emphasize($c['interlocutorName'], $search) !!}
                    @if($c['unread_messages_count'] > 0)
                        <div class="rounded-full h-5 w-5 ml-1 flex items-center justify-center bg-red-500 text-white">{{$c['unread_messages_count']}}</div>
                    @endif
                </div>
            @empty
                <div class='text-sm text-semibold'>No matching conversations found</div>
            @endforelse

            <h3 class=' font-bold py-2 mt-3'>Users</h3>
            @forelse($foundUsers as $u)
                <div wire:click="createConversation({{ $u['id'] }})" class="flex items-center cursor-pointer relative">
                    @if($this->isOnline($u['activity']))
                        <div class="absolute items-center justify-center w-4 h-4 text-xs font-bold text-white bg-green-500 border-2 border-white rounded-full top-1 left-9"></div>
                    @endif
                    <img src="{{ $this->getAvatarUrl($u['name']) }}" alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
                    {!! $this->emphasize($u['name'], $search) !!}
                </div>
            @empty
                <div class='text-sm text-semibold'>No matching users found</div>
            @endforelse
        @endif

    </div>

</div>


