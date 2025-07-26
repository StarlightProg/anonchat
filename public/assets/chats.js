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

async function fetchChats() {
    $.get('/api/chat/list')
        .done(function (data) {
            if (data.success == false) {
                console.error("Not authorized");
                window.location.href = '/';
            }
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
        div.id = "chat" + chat.group_id;

        const chatTitle = document.createElement('div');
        chatTitle.textContent = `${chat.chat_name}`;

        const lastMessage = document.createElement('div');
        lastMessage.className = 'lastMessage';

        if (chat.last_message) {
            lastMessage.innerHTML = `<strong>${chat.last_message.client.name}:</strong> ` + chat.last_message.message + " " + chat.last_message.time;
        } else {
            lastMessage.innerHTML = '';
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

                messageElem.innerHTML = `<strong>${msg.client.name}:</strong> ${msg.message} <i>${msg.time}</i>`;

                messagesDiv.appendChild(messageElem);
            }

            const existingHeader = windowDiv.querySelector('h3');
            windowDiv.innerHTML = '';
            windowDiv.appendChild(existingHeader);
            windowDiv.appendChild(messagesDiv);

            const newUrl = `/chats/${chat_id}`;

            history.pushState(null, '', newUrl); 
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

        if (messageText === '') return;
        if (typeof(currentChatId) == "undefined") return;

        const pathParts = window.location.pathname.split('/');
        let chatId = pathParts[pathParts.length - 1];
        
        if (chatId && chatId !== 'chats') {
            loadChat(chatId);
        }

        socket.emit("chatMessage", {roomId: currentChatId, message: messageText, client_token: token});
    });

    socket.on('chatMessage', (message) => {
        let messagessContainer = document.querySelector('.messages-container');

        $(`#chat${message.group_id} .lastMessage`).html(`<strong>${message.client.name}:</strong> ` + message.message + " " + message.time);

        if (currentChatId != message.group_id) {
            return;
        }
        let messageElemm = document.createElement('div');
        messageElemm.className = 'message';

        messageElemm.innerHTML = `<strong>${message.client.name}:</strong> ${message.message} <i>${message.time}</i>`;

        messagessContainer.appendChild(messageElemm);

        $('#messageInput').val('');
    });
});

