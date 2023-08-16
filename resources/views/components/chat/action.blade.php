@props(['editable'=>false, 'position'=>true, 'message'])

<div class="action invisible top @if($position) right-3 @else -right-36 @endif ">
    <ul class='py-4'>
        @if($editable)
            <li class='py-2 px-4 hover:bg-stone-700' onclick = {{'editAction('.$message->id.')'}}>Edit</li>
        @endif
       <li class='py-2 px-4 hover:bg-stone-700'>Answer</li>
       <li class='py-2 px-4 hover:bg-stone-700'>Make as Important</li>
    </ul>
</div>
