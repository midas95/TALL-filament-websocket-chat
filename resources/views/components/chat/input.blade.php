@props(['typingData' => null, 'conversation', 'answerMessage'=>null, 'editMessageId' => null])
<div class='flex flex-col mt-8 relative'>
    @if ($typingData && $conversation->id === $typingData['conversationId'])
        <div class='dot-typing absolute'></div>
        <span class='text-sky-500 absolute -top-7 left-14 text-sm'>{{$typingData['user']['name']}} is typing</span>
    @endif
    @if($answerMessage)
        <div class='grow grid grid-cols-12 relative bg-slate-200 dark:bg-slate-800 mx-8 mt-2 border-solid border-l-4 border-emerald-700 rounded-lg'>
            <div class='col-span-11 ml-4 font-bold text-emerald-500'>
                <i>{{$answerMessage->user->name}}</i>
            </div>
            @if($editMessageId == 0)
            <div class='row-span-2 justify-self-center self-center cursor-pointer' @click="cancelAnswer()">
                <x-chat.icons name='x-circle' />
            </div>
            @endif
            <div class='col-span-11 ml-4'>
                {{$answerMessage->content}}
            </div>
        </div>
    @endif
    <div class='flex items-center h-16 relative w-full'>
        <div class="upload-btn-wrapper flex items-center mx-3">
            <x-chat.icons name='plus-lg' height='32' width='32' />
            <input type="file" name="myfile"/>
        </div>
        <textarea class='rounded-md grow text-black h-12'
          wire:model.debounce.300ms='content'
          x-ref="messageInput"
          @keyup="typing(event)"
          @keydown.ctrl.enter.prevent="sendMessage()"
          @keydown.up="editLatestMessage()"
          @keydown.escape="cancelEdit()"
        ></textarea>



        @if($editMessageId == 0)
        <button id="send" class='rounded-md mx-3' wire:click='send'>
            <x-chat.icons name='send' height='32' width='32' />
        </button>
        @else
            <div class='row-span-2 justify-self-center self-center cursor-pointer' @click="cancelEdit()">
                <x-chat.icons width="32" height="32" name='x-lg' />
            </div>
            <div class='row-span-2 justify-self-center self-center cursor-pointer' wire:click='send'>
                <x-chat.icons width="32" height="32" name='chevron-right' />
            </div>
        @endif
    </div>

</div>
