<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поиск попутчиков</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        
        <link rel="stylesheet" href="{{asset('assets/style.css')}}">
  </head>
  <body>

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
    </div>

    <div id="chat-section" class="d-none">
        <div id="chat" class="chat-box border rounded bg-white p-3 mb-3" style="height: 300px; overflow-y: auto;"></div>
        <form id="chat-form" class="d-flex">
            <input type="text" id="message" class="form-control me-2" placeholder="Введите сообщение...">
            <button class="btn btn-success">Отправить</button>
        </form>
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
      document.addEventListener('DOMContentLoaded', function () {
          fetch('/api/cities')
              .then(response => response.json())
              .then(data => {
                  const datalist = document.getElementById('citiesList');
                  datalist.innerHTML = '';
                  data.forEach(city => {
                      const option = document.createElement('option');
                      option.value = city.name;
                      datalist.appendChild(option);
                  });
              })
              .catch(error => {
                  console.error('Ошибка загрузки городов:', error);
              });
      });
  </script>
  
  </body>
  </html>
      