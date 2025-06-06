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
            <h2>My chats</h2>
            <div id="chat-list"></div>
        </div>
        <div class="chat-window">
            <div id="chat-window">
                <h3>Select chat</h3>
                <div id="messages-container"></div>
            </div>
            <form id="message-form" class="message-form">
                <textarea id="message-input" placeholder="Enter message..." required></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>

    <script src="{{asset('assets/chats.js')}}"></script>
</html>