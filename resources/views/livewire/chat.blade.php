<div class="w-3/4 max-w-screen-xl mx-auto p-5">
   @foreach ($chat_list as $message )
      @if ($message->user_id == $userID)
         <x-chat.chat-mylog :text='$message->content' :avatar='$targetAvatarPath'></x-chat.chat-mylog>    
      @else
         <x-chat.chat-otherlog :text='$message->content' :avatar='$userAvatarPath'></x-chat.chat-otherlog>
      @endif        
   @endforeach     
   <x-chat.chat-input></x-chat.chat-input>  
</div>