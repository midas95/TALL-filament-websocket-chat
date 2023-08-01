<div>
   @foreach ($message as $key => $chat_list )
       @if ($message->user_id == $userID)
           <x-chat-mylog :text='$message->content'/>
        @else(
            <x-chat-otherlog :text='$message->content'/>
        )
        <x-chat-input>
       @endif
   @endforeach
</div>
