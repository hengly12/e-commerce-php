$(document).ready(function() {
    // Function to show loader
    function showLoader() {
        $('#loader').show();
    }

    // Function to hide loader with a delay
    function hideLoader() {
        setTimeout(function() {
            $('#loader').fadeOut(1); // Delay for 1 millisecond
        }, 100); // 100 milliseconds delay
    }

    // Function to send message and receive response
    $('#sendMessage').click(function() {
        var message = $('#userInput').val().trim();
        if (message !== '') {
            var userMessage = '<div class="chat-bubble user">' + message + '</div>';
            $('#chatMessages').append(userMessage);
            $('#userInput').val('');

            // Show loader while waiting for bot response
            showLoader(); 

            // Send message to chatbot backend
            $.post('chatbot/chatbot.php', { message: message }, function(response) {
                var botMessage = '<div class="chat-bubble bot">' + response + '</div>';
                $('#chatMessages').append(botMessage);

                // Hide loader when bot response arrives
                hideLoader();
            });
        }
    });

    // Toggle chat screen visibility and change chat button icon
    $('#chatButton').click(function() {
        $('.chat-screen').toggle(); // Toggle chat screen visibility

        // Toggle chat button icon
        var chatIcon = $('#chatButton img');
        if (chatIcon.attr('src') === 'assets/images/chaticon.svg') {
            chatIcon.attr('src', 'assets/images/closeicon.svg');
        } else {
            chatIcon.attr('src', 'assets/images/chaticon.svg');
        }
    });

    // Show chat screen when start chat button is clicked
    $('#start-chat-btn').click(function(){
        // Hide chat-mail and show chat-body and chat-input
        $('.chat-mail').hide();
        $('.chat-body').removeClass('hide');
        $('.chat-input').removeClass('hide');
        
        // Get current date and time
        var now = new Date();
        var day = now.toLocaleDateString('en-US', { weekday: 'long' });
        var time = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });

        // Update chat-start with current time
        $('.chat-start').text(day + ', ' + time);

        // Show chat screen
        $('.chat-screen').show();
    });

    // Toggle chat screen visibility when close or open icon is clicked
    $('#closeChat, #openChat').click(function(){
        $('.chat-screen').toggle();

        // Toggle icon between close and message-square
        if ($('.chat-screen').is(':visible')) {
            $('#closeChat').replaceWith('<svg id="closeChat" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x animate"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>');
        } else {
            $('#closeChat').replaceWith('<svg id="closeChat" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>');
        }
    });
});
