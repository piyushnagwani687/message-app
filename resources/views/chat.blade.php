@extends('layouts.app')
@section('content')
<div class="container my-4">
    <div id="chat-app" class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Chat with {{ $receiver->name }}</h5>
        </div>
        <div id="chat-messages" class="card-body" style="max-height: 400px; overflow-y: auto;">
            <!-- Messages will be appended here dynamically -->
        </div>

        <div class="card-footer">
            <form id="chat-form" class="d-flex">
                @csrf
                <input type="text" id="message-input" class="form-control me-2" placeholder="Type a message" required>
                {{-- <input type="hidden" id="receiver-id" value="{{ $receiver->id }}"> --}}
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>
</div>

{{-- @push('scripts') --}}
<script>
    $(document).ready(function() {
        console.log(Echo);

        const receiverId = {{$receiver->id}}; // ensure receiverId is injected correctly
        const userId = {{$userId}}; // ensure userId is injected correctly

        // Fetch messages on page load
        fetchMessages();

        // Listen for new messages using Laravel Echo
        // pusher.subscribe('your-channel-name')
        window.Echo.private(`chat.1.2`)
            .listen('MessageSent', function(e) {
                // Append the new message received via Echo
                console.log('cxvxvxv');
                appendMessage(e.message, e.message.sender_id === parseInt(userId) ? 'You' : 'Other');
            })
            .error(function(error) {
            console.log('Error in Echo connection:', error);
            });

        // Handle form submission
        $('#chat-form').on('submit', function(event) {
            event.preventDefault();
            const message = $('#message-input').val();

            if (message.trim() !== '') {
                $.ajax({
                    url: '/messages',
                    method: 'POST',
                    data: {
                        message: message,
                        receiver_id: receiverId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        appendMessage(response.message, 'You');
                        $('#message-input').val(''); // Clear input field
                    },
                    error: function(xhr) {
                        console.error('Message send failed:', xhr.responseText);
                    }
                });
            }
        });

        // Function to fetch messages
        function fetchMessages() {
            $.ajax({
                url: '/messages',
                method: 'GET',
                data: { receiver_id: receiverId },
                success: function(messages) {
                    $('#chat-messages').empty(); // Clear previous messages
                    messages.forEach(function(message) {
                        appendMessage(message, message.sender_id === parseInt(userId) ? 'You' : 'Other');
                    });
                },
                error: function(xhr) {
                    console.error('Failed to fetch messages:', xhr.responseText);
                }
            });
        }

        // Function to append a message to the chat
        function appendMessage(message, sender) {
            $('#chat-messages').append(
                `<p><strong>${sender}:</strong> ${message.message}</p>`
            );
        }
    });
</script>
{{-- @endpush --}}
@endsection


