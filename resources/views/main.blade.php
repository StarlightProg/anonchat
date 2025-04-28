<!DOCTYPE html>

<html lang="">
    <head>
        @vite(['resources/sass/app.scss', 'resources/js/app.js'])
        
        <!-- Meta tags, title, etc. -->

        <!-- Fonts -->
        <!-- CSS imports -->

        <!-- Custom Styles -->
        <style type="text/css">
             body, html {
                height: 100%;
            }
        </style>
    </head>
    <body>
        <div class="d-flex align-items-center justify-content-center h-100">
            <form class="row g-3 text-center">
                <div class="col-12">
                    <p class="mt-2 mb-0">Онлайн: {{$online}}</p>
                  </div>
              <!-- Выбор города -->
              <div class="col-12">
                <label for="citySelect" class="form-label">Город</label>
                <select id="citySelect" class="form-select">
                  <option selected>Выберите город</option>
                  <option value="Москва">Москва</option>
                  <option value="Санкт-Петербург">Санкт-Петербург</option>
                  <option value="Новосибирск">Новосибирск</option>
                  <!-- Добавьте другие города при необходимости -->
                </select>
              </div>
          
              <!-- Надпись "Онлайн: 1" -->
              
          
              <!-- Кнопка поиска -->
              <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Поиск</button>
              </div>
            </form>
          </div>
    </body>
</html>