@props(['isTypingInterlocutor' => false, 'conversation','answeredMessage'=>null])
<div class='flex flex-col mt-8 relative  bg-slate-800'>
    @if ($isTypingInterlocutor)
        <div class='dot-typing absolute'></div>
        <span class='text-sky-500 absolute -top-7 left-14 text-sm'>{{$conversation->interlocutor()->name}} is typing</span>
    @endif
    @if($answeredMessage)
        <div class='grow grid grid-cols-12 relative bg-slate-600 mx-8 mt-2 border-solid border-l-4 border-emerald-700 rounded-lg'>
            <div class='col-span-11 ml-4 font-bold text-emerald-500'>
                <i>{{$answeredMessage->user->name}}</i>
            </div>
            <div class='row-span-2 justify-self-center self-center' onclick="cancelAnswer()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </div>
            <div class='col-span-11 ml-4'>
            {{$answeredMessage->content}}
            </div>
        </div>
    @endif
    <div class='flex items-center h-16 relative w-full'>
        <div class="upload-btn-wrapper flex items-center mx-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
            </svg>
            <input type="file" name="myfile"/>
        </div>
        <textarea class='rounded-md grow text-black h-12' wire:model='content' onkeydown="setTyping(event)"></textarea>
        <button id="send" class='rounded-md mx-3' wire:click='send'>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
            </svg>
        </button>
    </div>
</div>