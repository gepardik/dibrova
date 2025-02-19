<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('../config.php');

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    try {
        $conn = getDBConnection();
        
        // Получаем текущий статус
        $stmt = $conn->prepare('SELECT is_active FROM concerts WHERE id = ?');
        $stmt->execute([$_GET['id']]);
        $concert = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($concert) {
            // Меняем статус на противоположный
            $newStatus = $concert['is_active'] ? 0 : 1;
            
            $stmt = $conn->prepare('UPDATE concerts SET is_active = ? WHERE id = ?');
            $stmt->execute([$newStatus, $_GET['id']]);
        }
    } catch (PDOException $e) {
        // В случае ошибки просто перенаправляем обратно
    }
}

header('Location: concerts.php');
exit; 