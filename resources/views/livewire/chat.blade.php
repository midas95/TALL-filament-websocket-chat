<div class="w-3/4 max-w-screen-xl mx-auto p-5">
   @foreach ($chat_list as $chat )
      @if ($chat->user_id == $message['user_id'])
         <x-chat.chat-mylog :text='$chat->content' :avatar='$targetAvatarPath'></x-chat.chat-mylog>    
      @else
         <x-chat.chat-otherlog :text='$chat->content' :avatar='$userAvatarPath'></x-chat.chat-otherlog>
      @endif        
   @endforeach     
   <x-chat.chat-input :targetID='$conversation["participant_b_id"]'></x-chat.chat-input>  
</div>