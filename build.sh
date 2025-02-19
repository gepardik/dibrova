#!/bin/bash

# Очищаем папку сборки
rm -rf dist

# Собираем фронтенд
npm run build

# Создаем структуру для деплоя
mkdir -p dist/api dist/admin dist/uploads

# Копируем бэкенд файлы
cp backend/config.php dist/
cp backend/api/* dist/api/
cp backend/admin/* dist/admin/

# Создаем .htaccess для маршрутизации React Router
echo "RewriteEngine On
RewriteBase /
RewriteRule ^index\.html$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-l
RewriteRule . /index.html [L]" > dist/.htaccess

# Создаем .htaccess для защиты админ-панели
echo "AuthType Basic
AuthName \"Restricted Area\"
AuthUserFile /path/to/.htpasswd
Require valid-user" > dist/admin/.htaccess

# Выводим инструкции
echo "Build completed! To deploy:"
echo "1. Upload the contents of the 'dist' folder to your hosting"
echo "2. Create a .htpasswd file for admin area protection"
echo "3. Update the database configuration in config.php"
echo "4. Make sure the 'uploads' directory is writable" 