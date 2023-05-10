# API методы для сохранения событий и получения статистики

## Метод 1. Сохранение событий

### POST /stat_api/api/save.php

Сохраняет информацию о событии в БД:
 - тип события;
 - статус пользователя;
 - IP пользователя;
 - дата события

Параметры:
 - `event_type`: название события;
 - `user_status`: дата события в формате `YYYY-MM-DD`

## Метод 2. Фильтрация и подсчет статистики

### GET /stat_api/api/view.php

Параметры:
 - `event_type`: название события;
 - `event_date`: дата события;
 - `count_by`: тип счетчика