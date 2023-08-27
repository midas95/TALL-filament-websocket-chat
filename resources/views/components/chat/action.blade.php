@props(['editable'=>false, 'position'=>'right', 'message'])

<x-chat.icons name='chevron-down' class="z-10 absolute right-0 top-0 font-bold cursor-pointer h-6 w-6 p-1" click="open = !open" show="over" />
<div x-show="open" @click.outside="open = false" @click="open = !open" class="z-20 rounded-md w-44 absolute bg-gray-400 top-6 @if($position == 'right') -right-1 @else -right-36 @endif ">
    <ul class='py-2'>
        @if($editable)
            <li class='py-2 px-4 hover:bg-gray-600 cursor-pointer' @click="editAction({{$message->id}})">Edit</li>
        @endif
       <li class='py-2 px-4 hover:bg-gray-600 cursor-pointer' @click="answerAction({{$message->id}})">Answer</li>
       <li class='py-2 px-4 hover:bg-gray-600 cursor-pointer' @click="markAction({{$message->id}})">Make as Important</li>
    </ul>
</div>
