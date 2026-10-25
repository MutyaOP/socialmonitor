# SocialMonitor — система мониторинга социальных проблем по текстам

Тема СӨЖ №1: «Мәтіндер негізінде әлеуметтік мәселелерді мониторингтеу жүйесі».
Laravel 11 · MySQL · Blade + Tailwind (Breeze) · Chart.js · rule-based анализатор текста (казахский + русский)

## Установка

```bash
composer create-project laravel/laravel socialmonitor
cd socialmonitor
composer require laravel/breeze --dev
php artisan breeze:install blade

# Скопируйте содержимое архива В КОРЕНЬ проекта с заменой файлов
# (перезаписываются: routes/web.php, resources/views/dashboard.blade.php,
#  resources/views/layouts/navigation.blade.php, app/Models/User.php,
#  database/seeders/DatabaseSeeder.php)
```

`.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=socialmonitor
DB_USERNAME=root
DB_PASSWORD=
```

```bash
php artisan migrate:fresh --seed
npm install && npm run build
php artisan serve
```

Вход: `admin@socialmonitor.kz` / `password` (Admin), `operator@socialmonitor.kz` / `password` (Оператор).
Новые регистрации получают роль **operator**; роли меняет Admin в разделе «Қолданушылар».

## Что делает система

1. **Сбор текстов**: новости, соцсети, форумы, обращения граждан (раздел «Мәтіндер», CRUD + дереккөздер).
2. **Автоанализ при сохранении** (`app/Services/TextAnalyzer.php`):
   - **Классификация** по словарю ключевых основ (категории: безработица, ЖКХ, здравоохранение, образование,
     экология, транспорт, безопасность, коррупция, цены) с весами 1–3. Сопоставление по началу токена,
     поэтому «отоплен» ловит «отопление/отоплением». Хранится объяснение: какие слова сработали.
   - **Тональность** (-1…+1): лексикон полярности kk/ru, нормализация `tanh(Σ/4)`.
     Порог: ≤ -0.25 негатив, ≥ 0.25 позитив.
   - **Тяжесть/срочность 0–100** = негатив ×45 + маркеры срочности (до 3) ×35 + вес главной категории ×20.
     ≥60 высокая, ≥35 средняя.
   - **Ключевые слова** текста (топ-5 по частоте без стоп-слов).
3. **Мониторинг** (`MonitoringService`): число негативных текстов категории за 24 ч сравнивается со среднесуточным за
   предыдущие 7 дней. Если ≥ 3 и рост ≥ 1.5× — создаётся оповещение (средний/высокий уровень).
   Кнопка «Мониторингті іске қосу» на панели и в разделе «Ескертулер».
4. **Статистика и отчёты**: панель (динамика за 14 дней, категории, регионы), отчёт «категория × тональность» за
   период, выгрузка CSV (UTF-8 с BOM, открывается в Excel).
5. **Быстрый анализ**: страница «Жылдам талдау» — вставить любой текст и увидеть разбор без сохранения.
6. **Админ-настройка словаря**: категории и ключевые слова редактируются в интерфейсе; после правок кнопка
   «Барлығын қайта талдау» пересчитывает все тексты.

## Соответствие требованиям задания

| Требование | Реализация |
|---|---|
| MVC, Laravel 9/10/11 | `app/Models`, `app/Http/Controllers`, `resources/views` |
| MySQL/PostgreSQL | миграции без СУБД-специфичного SQL |
| Blade + Tailwind | Breeze + свои страницы |
| Auth | Laravel Breeze, поле `role` в `users` |
| Роли Admin / User | `RoleMiddleware` + группы маршрутов; Admin: CUD источников/категорий/слов, удаление текстов, пользователи; Оператор: тексты, анализ, оповещения, отчёты |
| CRUD | тексты, источники, категории (+ ключевые слова), пользователи (роль) |
| Migration / Seeder | 5 файлов миграций, `DatabaseSeeder` (≈100 текстов за 30 дней + всплеск по ЖКХ) |
| 4–6+ таблиц | users, categories, keywords, sources, posts, category_post, alerts |
| one-to-many | source→posts, category→keywords, category→alerts, user→posts |
| many-to-many | posts ⇄ categories (`category_post` со score, matched, is_primary) |
| Фильтрация / поиск | список текстов: текст, категория, источник, тональность, тяжесть, регион, статус, даты; поиск есть и в остальных списках |
| Статистика / графики | Chart.js: линия, doughnut, bar по регионам, stacked bar по категориям |
| ИИ-элемент | rule-based `TextAnalyzer` + `MonitoringService` (вариант из задания: «негативные слова → фильтр») |
| Form Request | `app/Http/Requests/*` |
| CSRF | `@csrf` во всех формах |
| Role-based access | `RoleMiddleware` |
| Git | `git init && git add . && git commit`, затем push на GitHub/GitLab |

## Идеи для защиты / развития

- Лексикон и словари вынесены в `config/socialmonitor.php` и БД — легко расширять без правки кода.
- Вместо правил можно подключить Python-сервис (Flask/FastAPI с моделью тональности): заменить вызов
  `TextAnalyzer::analyze()` на HTTP-запрос, остальная система не изменится.
- Ограничения: правила не понимают сарказм и отрицание («нет проблем»), морфология kk учитывается только через основы.
