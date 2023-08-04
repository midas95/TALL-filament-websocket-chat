@props(['targetID'=>0])
<div class='flex'>

<div class="upload-btn-wrapper m-2">
  <i class="bi bi-paperclip"></i>
  <input type="file" name="myfile"/>
</div>
  <input class='rounded-md grow text-black' id='chat-input' name="send" wire:model='content' wire:keydown.enter='send'>
  <button id="send" class='ml-2 mr-0 w-12 rounded-md bg-red-400' wire:click='send'> <i class="bi bi-chevron-right"></i></button>

</div>



