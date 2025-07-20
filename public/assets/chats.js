let socket = io('http://localhost:8006', {
    transports: ["websocket"]
});

let currentChatId;
const token = localStorage.getItem('auth_token');

if (!token) {
    window.location.href = '/';
}

$.ajaxSetup({
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function (xhr) {
        if (token) {
            console.log("token " + token);
            xhr.setRequestHeader('Authorization', `Bearer ${token}`);
        }
    },
    error: function (error) {
        console.error('Ошибка AJAX:', error);
        const errorMessage = error.responseJSON?.message || 'Ошибка сервера';
        const errorDiv = $('.error-message:visible');
        if (errorDiv.length) errorDiv.text(errorMessage).show();
    }
});

console.log("token2 " + token);

async function fetchChats() {
    $.get('/api/chat/list')
        .done(function (data) {
            if (data.success == false) {
                console.error("Not authorized");
                window.location.href = '/';
            }

            console.log(data);
            renderChatList(data.result.groups);
        })
        .fail(function(error) {
            console.log(error);
        })
}

function renderChatList(chats) {
    const list = document.getElementById('chat-list');
    list.innerHTML = '';

    chats.forEach(chat => {
        const div = document.createElement('div');
        div.className = 'chat-item';

        const chatTitle = document.createElement('div');
        chatTitle.textContent = `Чат №${chat.chat_name}`;

        const lastMessage = document.createElement('div');
        if (chat.last_message) {
            lastMessage.textContent = chat.last_message.message;
        } else {
            lastMessage.textContent = '';
        }

        lastMessage.style.fontSize = '0.9em';
        lastMessage.style.color = '#555';

        div.appendChild(chatTitle);
        div.appendChild(lastMessage);

        div.onclick = () => loadChat(chat.group_id);
        list.appendChild(div);
    });
}

function loadChat(chat_id) {
    const windowDiv = document.getElementById('chat-window');
    $.get(`/api/chat/${chat_id}`)
        .done(function (data) {
            if (data.success == false) {
                console.error("Not authorized");
                window.location.href = '/';
                return;
            }

            currentChatId = chat_id;

            const messages = data.result.messages || [];

            const messagesDiv = document.createElement('div');
            messagesDiv.className = 'messages-container';

            for (let i = messages.length - 1; i >= 0; i--) {
                const msg = messages[i];
                const messageElem = document.createElement('div');
                messageElem.className = 'message';

                messageElem.innerHTML = `<strong>${msg.client.name}:</strong> ${msg.message}`;

                messagesDiv.appendChild(messageElem);
            }

            const existingHeader = windowDiv.querySelector('h3');
            windowDiv.innerHTML = '';
            windowDiv.appendChild(existingHeader);
            windowDiv.appendChild(messagesDiv);
        })
        .fail(function(error) {
            console.log(error);
        });
}

fetchChats();

$(document).ready(function() {
    const messagesContainer = document.querySelector('.messages-container');

    socket.emit("chat_user_connected", {client_token: token});

    $('#message-form').on('submit', function(e) {
        e.preventDefault();

        const messageInput = $('#message-input');
        const messageText = messageInput.val().trim();
        console.log(currentChatId);
        console.log("message-input " + messageInput);
        console.log("message-text " + messageText);
        if (messageText === '') return;
        if (typeof(currentChatId) == "undefined") return;
        console.log(currentChatId);

        const pathParts = window.location.pathname.split('/');
        let chatId = pathParts[pathParts.length - 1];
        
        if (chatId && chatId !== 'chats') {
            loadChat(chatId);
        }

        socket.emit("chatMessage", {roomId: currentChatId, message: messageText, client_token: token});

        // $.ajax({
        //     url: '/api/chat/send_message',
        //     type: 'POST',
        //     data: JSON.stringify({
        //         group_id: currentChatId,
        //         message: messageText
        //     }),
        //     success: function(data) {
        //         console.log("datqtqwtwq");
        //         if (data.success) {
        //             let messageElemm = document.createElement('div');
        //             messageElemm.className = 'message';

        //             messageElemm.innerHTML = `<strong>${data.result.message.client.name}:</strong> ${data.result.message.message}`;

        //             messagesContainer.appendChild(messageElemm);

        //             $('#messageInput').val('');
        //         } else {
        //             alert('Error sending message');
        //         }
        //     }
        // });
    });

    socket.on('chatMessage', (message) => {
        console.log("fdsfsdfdsg");
        console.log("yreergeryh " + message.message);
        let messagessContainer = document.querySelector('.messages-container');
        if (currentChatId != message.group_id) {
            return;
        }
        let messageElemm = document.createElement('div');
        messageElemm.className = 'message';

        messageElemm.innerHTML = `<strong>${message.client.name}:</strong> ${message.message}`;

        messagessContainer.appendChild(messageElemm);

        $('#messageInput').val('');
    });
});

