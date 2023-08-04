@props(['avatar'=> null, 'text'=>''])
 
<div class='flex m-2 justify-end'>
  <p class='w-3/4 bg-red-400 m-2 p-2 rounded-md text-white'> 
  {{$text}}
 </p>
 <img src='{{url($avatar)}}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
 </div>