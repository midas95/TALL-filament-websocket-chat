@props(['messages'])

<div 
    x-show="showCarousel"
    >
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-90"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-90"
            x-on:click.away="showCarousel=false"
            class="p-2 fixed w-full h-100 inset-0 z-50 overflow-hidden flex justify-center items-center bg-black bg-opacity-75"
        >
            <div
                @click.away=" showCarousel=false"
                class="flex flex-col max-w-3xl max-h-full overflow-auto"
            >
                <div class="z-50">
                    <button
                        @click="showCarousel=false"
                        class="float-right pt-2 pr-2 outline-none focus:outline-none"
                    >
                        <svg
                            class="fill-current text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            width="18"
                            height="18"
                            viewBox="0 0 18 18"
                        >
                            <path
                                d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"
                            ></path>
                        </svg>
                    </button>
                </div>
                <div class="p-2" wire:ignore>
                    <div id="main-carousel" class="splide m-4" aria-label="Beautiful Images">
                        <div class="splide__track">
                                <ul class="splide__list">
                                    @foreach ($messages as $message)
                                        @if($message->file)
                                            <li class="splide__slide">
                                                <img src="{{asset($message->file)}}" alt="Image">
                                            </li>
                                        @endif
                                    @endforeach 
                                </ul>
                        </div>
                    </div>
                    <div
                        id="thumbnail-carousel"
                        class="splide"
                        aria-label="The carousel with thumbnails. Selecting a thumbnail will change the Beautiful Gallery carousel."
                        >
                        <div class="splide__track">
                            <ul class="splide__list">
                                @foreach ($messages as $message)
                                    @if($message->file)
                                        <li class="splide__slide">
                                            <img src="{{asset($message->file)}}" alt="Image">
                                        </li>
                                    @endif
                                @endforeach 
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>