<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Анонимный чат</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{asset('assets/style.css')}}">
</head>
<body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @include('navbar')
    <div id="search-section">
        <form id="search-form" class="row g-3 text-center">
            <div class="col-12">
                <p class="mb-2">Онлайн: <span id="current-online">0</span></p>
            </div>
            <div class="col-12">
                <label for="cityInput" class="form-label">Город</label>
                <input type="text" id="cityInput" class="form-control" placeholder="Введите город">
            </div>
            <div class="col-12">
                <label for="ageInput" class="form-label">Возраст</label>
                <input type="number" id="ageInput" class="form-control" placeholder="Введите возраст">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#searchingModal">Поиск</button>
            </div>
        </form>
        <textarea id="messageeeee"></textarea>
        <button onclick="sendmessageeeee()">Send</button>
    </div>

    <div id="chat-section" class="d-none">
        <div id="chat" class="chat-box border rounded bg-white p-3 mb-3" style="height: 500px; overflow-y: auto;"></div>
        
        <form id="message-form" class="d-flex mb-3">
            <input type="text" id="message" class="form-control me-2" placeholder="Введите сообщение..." autocomplete="off">
            <button type="submit" class="btn btn-success">Отправить</button>
        </form>
    
        <div class="d-flex gap-2">
            <button id="end-chat" class="btn btn-danger w-50">Завершить чат</button>
            <button id="back-to-main" class="btn btn-primary w-50 d-none">Назад</button>
            <button id="find-new" class="btn btn-primary w-50 d-none" data-bs-toggle="modal" data-bs-target="#searchingModal">Найти собеседника</button>
            <button id="create-persistent-chat" class="btn btn-warning w-100">Создать постоянный чат</button>
        </div>
    </div>    
    
    <div class="modal fade" id="searchingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="spinner-border text-primary mb-3" style="margin: auto;" role="status">
                    <span class="visually-hidden">Загрузка...</span>
                </div>
                <h5 class="modal-title mb-2">Поиск собеседника...</h5>
                <p class="mb-2">Пожалуйста, подождите, пока мы ищем вам пару</p>
                <button class="mb-0 btn btn-primary" id="cancel-search" data-bs-toggle="modal" data-bs-target="#searchingModal">Отменить поиск</button>
            </div>
        </div>
    </div>
    
    @include('scripts')
    @yield('scripts')
    <script>
        function sendmessageeeee() {
            console.log("cmwkgwekg")
            const message = document.getElementById("messageeeee").value;
            socket.emit("new_messagedasrwqrq", message);
        }
            // Получение сообщений от сервера
        socket.on("new_messagedasrwqrq", (data) => {
            console.log("New message:", data);
            alert(`New message: ${data.message}`);
        });
    </script>
</body>
</html>
