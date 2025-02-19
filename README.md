# DIBROVA Website

Веб-сайт музыкального ансамбля DIBROVA.

## Технологии

- Frontend: React, SCSS, Webpack
- Backend: PHP, MySQL
- Мультиязычность: i18next
- Админ-панель для управления контентом

## Требования

- Node.js 18+
- PHP 7.4+
- MySQL 5.7+
- Apache с mod_rewrite

## Установка для разработки

1. Клонируйте репозиторий:
```bash
git clone [repository-url]
cd dibrova-website
```

2. Установите зависимости:
```bash
npm install
```

3. Создайте базу данных и импортируйте структуру:
```bash
mysql -u your_user -p your_database < backend/database.sql
```

4. Скопируйте и настройте конфигурацию:
```bash
cp backend/config.example.php backend/config.php
# Отредактируйте config.php, указав параметры подключения к базе данных
```

5. Запустите сервер разработки:
```bash
npm start
```

## Деплой на хостинг

1. Соберите проект:
```bash
./build.sh
```

2. Загрузите содержимое папки `dist` на хостинг.

3. На хостинге:
   - Создайте базу данных и импортируйте `database.sql`
   - Настройте `config.php` с параметрами базы данных
   - Создайте `.htpasswd` для защиты админ-панели:
     ```bash
     htpasswd -c /path/to/.htpasswd admin
     ```
   - Обновите путь к `.htpasswd` в `admin/.htaccess`
   - Убедитесь, что папка `uploads` доступна для записи:
     ```bash
     chmod 755 uploads
     ```

4. Настройте домен для указания на папку `dist`.

## Админ-панель

Доступ к админ-панели:
- URL: `/admin`
- Защищена Basic Authentication
- Позволяет управлять:
  - Концертами (добавление, редактирование, активация/деактивация)
  - Сообщениями от посетителей

## Структура проекта

```
├── src/                  # Frontend исходный код
│   ├── components/      # React компоненты
│   ├── pages/          # Страницы сайта
│   └── styles/         # SCSS стили
├── backend/             # PHP backend
│   ├── api/            # API endpoints
│   ├── admin/          # Админ-панель
│   └── uploads/        # Загруженные файлы
├── public/              # Статические файлы
└── dist/                # Собранный проект
```

## Мультиязычность

Сайт поддерживает 4 языка:
- Русский (по умолчанию)
- Эстонский
- Английский
- Украинский

Переводы находятся в `src/i18n.js`. 