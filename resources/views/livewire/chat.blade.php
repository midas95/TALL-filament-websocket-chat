
<div class='grow'  x-data="chat">
    @if ($conversation)
        <div class='flex items-center border-b-2 border-slate-500 relative'>
            <img src='{{ $conversation->interlocutor()->getAvatarUrl() }}' alt='avatar' class='m-2 w-10 h-10 rounded-3xl' />
            <span>{{ $conversation->interlocutor()->name }}</span>
            <span class='absolute text-xs bottom-0.5 left-12'>Last online {{$lastOnline}}</span>
        </div>
        <div class="h-[65vh] overflow-y-auto" x-ref="messages">
            <?php $uId = 0;
            $repeated = false; ?>
            @foreach ($messages as $message)
                <?php if ($uId != $message->user_id) {
                    $uId = $message->user_id;
                    $repeated = false;
                } else {
                    $repeated = true;
                }  ?>
                <x-chat.message :$message :$myUserId :$repeated :$conversation/>
            @endforeach
        </div>
        <x-chat.input :$typingData :$conversation :$answerMessage :$editMessageId/>
    @else

        <div>No active conversation.</div>

    @endif


    @push('scripts')
    <script>
        let isTyping = false
        let messageContent = ''
        function debounce(func, timeout = 300){
            let timer
            return (...args) => {
                clearTimeout(timer)
                timer = setTimeout(() => { func.apply(this, args); }, timeout)
            };
        }

        function stopTyping(){
            isTyping = false
            messageContent = ''
            @this.emit('broadcastTyping', false)
        }
        const typingDebounce = debounce(() => window.stopTyping(), 3000);

        function messagesRead(){
            @this.readMessage()
        }
        const readDebounce = debounce(() => window.messagesRead(), 3000);
        // document.addEventListener("DOMContentLoaded", () => {
        //     Livewire.hook('element.updating', (fromEl, toEl, component) => {
        //         console.log(component)
        //     })
        // })
        document.addEventListener('alpine:init', () => {

            Alpine.data('chat', () => ({
                alpineInit: @entangle('alpineInit'),
                init(){
                    this.alpineInit = true // trigger updateMessages when alpine is loaded
                    @this.on('updateMessages', (read = true) => {
                        this.$refs.messages.scrollTo(0, this.$refs.messages.scrollHeight);
                        if(read) {
                            readDebounce()
                        }
                    })

                    // Livewire.hook('element.initialized', (component) => {
                    //     console.log('initialized ')
                    // })


                },
                typing(e) {

                    if(messageContent != e.target.value){
                        messageContent = e.target.value
                        if(!isTyping && messageContent != ''){

                            isTyping = true
                            @this.emit('broadcastTyping', true)
                        }
                        typingDebounce()
                    }
                },
                sendMessage(){
                    @this.send()
                    // this.$refs.messageInput.blur()
                    stopTyping()
                },
                editLatestMessage(){
                    if(@this.content.trim() === ''){
                        @this.editMessage()
                    }
                },
                cancelEdit(){
                    @this.cancelEdit()
                },
                cancelAnswer(){
                    @this.resetAnswerMessage()
                },
                editAction(id){
                    @this.editMessage(id)
                    this.$refs.messageInput.focus()
                },
                answerAction(id){
                    @this.setAnswerMessage(id)
                    this.$refs.messageInput.focus()
                },
                markAction(id){
                    @this.markMessage(id)
                }
            }))

        })
    </script>
    @endpush

</div>




