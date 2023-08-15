@props(['message', 'myUserId', 'repeated' , 'conversation'])

@if($conversation['type'] == 'private')
    @if($message['user_id'] == $myUserId)
        <div class='flex {{!$repeated?'mt-6':'mt-1 mr-14'}} justify-end '>
            <div class='bg-emerald-800 text-white border-gray-300 p-2 rounded-md {{!$repeated?'rounded-tr-none':''}} relative'>
                @if(!$repeated)
                <span class="absolute text-emerald-800 top-0" style="right: -8px;" >
                    <svg viewBox="0 0 8 13" height="13" width="8" preserveAspectRatio="xMidYMid meet" class="" version="1.1" x="0px" y="0px" enable-background="new 0 0 8 13" xml:space="preserve"><path opacity="0.13" d="M5.188,1H0v11.193l6.467-8.625 C7.526,2.156,6.958,1,5.188,1z"></path><path fill="currentColor" d="M5.188,0H0v11.193l6.467-8.625C7.526,1.156,6.958,0,5.188,0z"></path></svg>
                </span>
                @endif
                <div class="pr-12">
                    {!! nl2br($message['content']) !!}
                </div>
                <div class="text-xs text-right absolute bottom-0 right-4 pr-2 pb-1">{{$message['created_at']->format('H:i')}}</div>
                @if($message['seen'])
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all bottom-0 absolute right-0 mr-1" viewBox="0 0 16 16">
                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z"/>
                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z"/>
                    </svg>   
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2 bottom-0 absolute right-0 mr-1" viewBox="0 0 16 16">
                        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                    </svg> 
                @endif
            </div>
            @if(!$repeated)<img src='{{$message['user']->getAvatarUrl()}}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />@endif
        </div>
    @else
        <div class='flex {{!$repeated?'mt-6':'mt-1 ml-14'}} justify-start'>
            @if(!$repeated)<img src='{{$message['user']->getAvatarUrl()}}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />@endif
            <div class='bg-black/50 p-2 rounded-md text-white {{!$repeated?'rounded-tl-none':''}} relative'>
                @if(!$repeated)
                <span class="absolute text-black/50 top-0" style="left: -8px;" >
                    <svg viewBox="0 0 8 13" height="13" width="8" preserveAspectRatio="xMidYMid meet" class="" version="1.1" x="0px" y="0px" enable-background="new 0 0 8 13" xml:space="preserve"><path opacity="0.13" fill="#0000000" d="M1.533,3.568L8,12.193V1H2.812 C1.042,1,0.474,2.156,1.533,3.568z"></path><path fill="currentColor" d="M1.533,2.568L8,11.193V0L2.812,0C1.042,0,0.474,1.156,1.533,2.568z"></path></svg>
                </span>
                @endif
                <div class="pr-10">
                    {!! nl2br($message['content']) !!}
                </div>
                <div class="text-xs text-right absolute bottom-0 right-0 pr-2 pb-1">{{$message['created_at']->format('H:i')}}</div>
            </div>
        </div>
    @endif
@endif
