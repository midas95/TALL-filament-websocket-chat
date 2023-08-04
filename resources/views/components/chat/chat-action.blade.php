@props(['action', 'items'=>[]])

<div class="{{ $action ? 'show' : 'hidden' }}">
  <div class='m-2'>
  @foreach ($item as $key => $items )
  <p> $item </p>
  @endforeach
</div>