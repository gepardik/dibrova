<?php
require_once '../config.php';

try {
    $conn = getDBConnection();
    
    // Проверяем, есть ли уже администраторы
    $stmt = $conn->query("SELECT COUNT(*) FROM admins");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Создаем первого администратора
        $username = 'admin';
        $password = 'admin123'; // Измените этот пароль
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $password_hash]);
        
        echo "Администратор успешно создан!\n";
        echo "Логин: admin\n";
        echo "Пароль: admin123\n";
    } else {
        echo "Администраторы уже существуют в базе данных.\n";
    }
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
?> 