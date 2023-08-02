@props(['action', 'items'=>[]])

<div class=' @if ($action) show @else hidden @endif'>
  <div class='m-2'>
  @foreach ($item as $key => $items )
  <p> $item </p>
  @endforeach
</div>