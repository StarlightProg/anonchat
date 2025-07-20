$.ajaxSetup({
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function (xhr) {
        const token = localStorage.getItem('auth_token');
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

$(document).ready(function () {
    const token = localStorage.getItem('auth_token');
    if (token) getUser();
    
    // Обработка формы входа
    $('#login-form').submit(function (e) {
        e.preventDefault();
        const login = $('#login-field').val();
        const password = $('#password-field').val();
        const errorDiv = $('#login-error');
        
        $.post('/api/auth/login', JSON.stringify({ name: login, password: password }))
            .done(function (data) {
                if (data.success) {
                    localStorage.setItem('auth_token', data.result.token);
                    location.reload();
                }
            });
    });

    // Обработка формы регистрации
    $('#register-form').submit(function (e) {
        e.preventDefault();
        const login = $('#login-field-register').val();
        const password = $('#password-field-register').val();
        const confirmPassword = $('#password-confirmed-field').val();
        const errorDiv = $('#register-error');

        if (password !== confirmPassword) {
            errorDiv.text('Пароли не совпадают').show();
            return;
        }
        
        $.post('/api/auth/register', JSON.stringify({ name: login, password: password }))
            .done(function (data) {
                if (data.success) {
                    localStorage.setItem('auth_token', data.result.token);
                    location.reload();
                }
            });
    });
});

async function getUser() {
        console.log("getting user")
        const token = localStorage.getItem('auth_token');

        $.get('/api/user')
            .done(function (data) {
                localStorage.setItem('user_name', data.name);
                $('#auth-nav').html(
                    `<li class='nav-item'><a class='nav-link' href="/chats">Chats</a></li>` +
                    `<li class='nav-item'><span class='nav-link'>${data.name}</span></li>` +
                    `<li class='nav-item'><a href='#' class='nav-link' onclick='logout()'>Выход</a></li>`
                );
        });
}

async function logout() {
    $.post('/api/logout')
        .done(function (data) {
            $('#auth-nav').html(
                `<li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Вход</a>
                </li>` +
                `<li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Регистрация</a>
                </li>`
            );
    });
}