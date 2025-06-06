$.ajaxSetup({
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function (xhr) {
        const token = localStorage.getItem('auth_token');
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

$.get('/api/chat/list')
    .done(function (data) {
        if (data.success == false) {
            console.error("Not authorized");
            window.location.href = '/';
        }
    })

$(document).ready(function () {
    
});