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
  
  <div class="search-card text-center">
      <form id="search-form" class="row g-3">
          <div class="col-12">
              <p class="mb-2">Онлайн: <span id="current-online">0</span></p>
          </div>
  
          <!-- Город -->
          <div class="col-12">
              <label for="cityInput" class="form-label">Город</label>
              <input type="text" class="form-control" id="cityInput" list="citiesList" placeholder="Введите город">
              <datalist id="citiesList"></datalist>
          </div>
  
          <!-- Возраст -->
          <div class="col-12">
              <label for="ageInput" class="form-label">Возраст</label>
              <input type="number" class="form-control" id="ageInput" placeholder="Введите ваш возраст" min="1" max="120">
          </div>
  
          <!-- Кнопка -->
          <div class="col-12">
              <button type="submit" class="btn btn-primary w-100">🔍 Найти собеседника</button>
          </div>
      </form>
  </div>
  
  <!-- Скрипты -->
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
      