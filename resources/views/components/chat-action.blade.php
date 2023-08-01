@props(['action', 'items'=>[]])

@if ($action)
<div class='show'>
@else 
<div class='hidden'>
@endif
<div class=''>
  @foreach ($item as $key => $items )
  <p> $item </p>
  @endforeach
</div>