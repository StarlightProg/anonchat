<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Anonymous chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{asset('assets/chats.css')}}">
</head>
<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @include('navbar')

    <div class="main-container">
        <div class="chat-sidebar">
            <h2>Мои чаты</h2>
            <div id="chat-list"></div>
        </div>
        <div class="chat-window" id="chat-window">
            <h3>Выберите чат из списка</h3>
        </div>
    </div>

    <script>
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
                div.textContent = `Чат №${chat.chat_name}`;
                div.onclick = () => loadChat(chat);
                list.appendChild(div);
            });
        }

        function loadChat(chat) {
            const windowDiv = document.getElementById('chat-window');
            windowDiv.innerHTML = `<h3>Чат №${chat.chat_name}</h3><pre>${JSON.stringify(chat, null, 2)}</pre>`;
            $.get('/api/chat/${chat.chat_name}')
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

        fetchChats();
    </script>
</html>