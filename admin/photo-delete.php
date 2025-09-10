<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Проверяем параметры
if (!isset($_GET['id']) || !isset($_GET['album_id'])) {
    header('Location: albums.php');
    exit;
}

$photo_id = $_GET['id'];
$album_id = $_GET['album_id'];

try {
    $conn = getDBConnection();
    
    // Получаем информацию о фото
    $stmt = $conn->prepare("SELECT * FROM photos WHERE id = ? AND album_id = ?");
    $stmt->execute([$photo_id, $album_id]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($photo) {
        // Удаляем файлы
        $paths = [
            '../../' . $photo['original_path'],
            '../../' . $photo['medium_path'],
            '../../' . $photo['thumbnail_path']
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
        
        // Удаляем запись из базы
        $stmt = $conn->prepare("DELETE FROM photos WHERE id = ?");
        $stmt->execute([$photo_id]);
    }
    
    header('Location: album-photos.php?id=' . $album_id);
    exit;
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
} 