@props(['message', 'myUserId', 'repeated' , 'conversation'])

@if($conversation['type'] == 'private')
    @if($message['user_id'] == $myUserId)
        <div class='flex {{!$repeated?'mt-6':'mt-1 mr-14'}} justify-end '>
            @php
                $mark = explode(',', $message->mark);
                $key = array_search(strval($myUserId), $mark);
            @endphp
            @if($key !== false)
                <x-chat.icons name='exclamation-lg' height='40' width="40" class="my-auto text-red-800" />
            @endif
            <div x-data="{open: false, over: false}"  @mouseover="over = true" @mouseover.away="over = false" class='bg-emerald-800 text-white border-gray-300 p-1 pb-6 rounded-md {{!$repeated?'rounded-tr-none':''}} relative {{$message->edited?'min-w-[120px]':''}}'>
                <x-chat.action :editable='true' :$message/>
                @if(!$repeated)
                <span class="absolute text-emerald-800 top-0" style="right: -8px;" >
                    <x-chat.icons name="msg-corner-l" height='13' width="8" viewBox="0 0 8 13" />
                </span>
                @endif
                <div class=" message relative p1-2">
                    @if ($message->file)
                        <img src="{{ asset($message->file) }}" height='250' width='250'/>
                    @endif
                    @if($message->answered_message_id)
                        <div class='pl-1 mb-1 bg-emerald-900 rounded-md relative w-full pr-4'>
                            <div  class=' pl-1  border-solid border-l-4 border-emerald-700'>
                                <p class='font-bold text-emerald-500'><i>{{$message->answeredMessage->user->name}}</i></p>
                                {{$message->answeredMessage->content}}
                            </div>
                        </div>
                    @endif
                    <div class='content min-w-[90px] pr-5'>

                        {!! nl2br($message['content']) !!}
                    </div>
                </div>
                <div class="text-xs text-right absolute bottom-0 right-4 pr-2 pb-1">
                    @if($message->edited)
                        <span class='pr-2'>edited</span>
                    @endif
                    {{$message['updated_at']->format('H:i')}}
                </div>
                @if($message['seen'])
                    <x-chat.icons name='check2-all' class="bottom-0 absolute right-0 mr-1" />
                @else
                    <x-chat.icons name='check2' class="bottom-0 absolute right-0 mr-1" />
                @endif
            </div>
            @if(!$repeated)<img src='{{$message['user']->getAvatarUrl()}}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />@endif
        </div>
    @else
        <div class='flex {{!$repeated?'mt-6':'mt-1 ml-14'}} justify-start'>
            @if(!$repeated)<img src='{{$message['user']->getAvatarUrl()}}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />@endif
            <div x-data="{open: false, over: false}"  @mouseover="over = true" @mouseover.away="over = false" class='bg-black/50 p-1 pb-6 rounded-md text-white {{!$repeated?'rounded-tl-none':''}} relative'>
                <x-chat.action :editable='false' position='left' :$message/>
                @if(!$repeated)
                <span class="absolute text-black/50 top-0" style="left: -8px;" >
                    <x-chat.icons name="msg-corner-r" height='13' width="8" viewBox="0 0 8 13" />
                </span>
                @endif
                <div class="relative pr-2">
                     @if($message->answered_message_id)
                        <div class='pl-1 mb-1 bg-emerald-950 rounded-md relative w-full pr-4'>
                            <div  class=' pl-1  border-solid border-l-4 border-emerald-700'>
                                <p class='font-bold text-emerald-500'><i>{{$message->answeredMessage->user->name}}</i></p>
                                {{$message->answeredMessage->content}}
                            </div>
                        </div>
                    @endif
                    <div class='content min-w-[90px] pr-5'>

                        {!! nl2br($message['content']) !!}
                    </div>
                </div>
                <div class="text-xs text-right absolute bottom-0 right-0 pr-2 pb-1">{{$message['updated_at']->format('H:i')}}</div>
            </div>
            @php
                $mark = explode(',', $message->mark);
                $key = array_search(strval($myUserId), $mark);
            @endphp
            @if($key !== false)
                <x-chat.icons name='exclamation-lg' height='40' width="40" class="my-auto text-red-800" />
            @endif
        </div>

    @endif
@endif
