<?php
session_start();
define('DB_HOST', 'srv924.hstgr.io');     // Хост базы данных
define('DB_USER', 'u540617893_dibrova');              // Имя пользователя базы данных
define('DB_PASS', 'SolMinor123');              // Пароль базы данных
define('DB_NAME', 'u540617893_dibrova');              // Имя базы данных

// Создаем соединение с базой данных
function getDBConnection() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Функция для проверки авторизации администратора
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?> 