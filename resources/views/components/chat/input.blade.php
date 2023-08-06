
<div class='flex mt-10'>
    <div class="upload-btn-wrapper m-2">
        <i class="bi bi-paperclip"></i>
        <input type="file" name="myfile"/>
    </div>
    <textarea class='rounded-md grow text-black' wire:model='content'></textarea>
    <button id="send" class='ml-2 mr-0 rounded-md bg-red-400 px-5' wire:click='send'> <i class="bi bi-chevron-right">Send Message</i></button>
</div>



