<?php
require_once '../config.php';

// Проверяем авторизацию
if (!isAdminLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Проверяем ID альбома
if (!isset($_GET['id'])) {
    header('Location: albums.php');
    exit;
}

$album_id = $_GET['id'];

try {
    $conn = getDBConnection();
    
    // Получаем информацию об альбоме
    $stmt = $conn->prepare("SELECT * FROM albums WHERE id = ?");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($album) {
        // Начинаем транзакцию
        $conn->beginTransaction();
        
        // Получаем все фотографии альбома
        $stmt = $conn->prepare("SELECT * FROM photos WHERE album_id = ?");
        $stmt->execute([$album_id]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Удаляем файлы фотографий
        foreach ($photos as $photo) {
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
        }
        
        // Удаляем обложку альбома
        if ($album['cover_path'] && file_exists('../../' . $album['cover_path'])) {
            unlink('../../' . $album['cover_path']);
        }
        
        // Удаляем все фотографии из базы
        $stmt = $conn->prepare("DELETE FROM photos WHERE album_id = ?");
        $stmt->execute([$album_id]);
        
        // Удаляем все видео из базы
        $stmt = $conn->prepare("DELETE FROM videos WHERE album_id = ?");
        $stmt->execute([$album_id]);
        
        // Удаляем сам альбом
        $stmt = $conn->prepare("DELETE FROM albums WHERE id = ?");
        $stmt->execute([$album_id]);
        
        // Завершаем транзакцию
        $conn->commit();
    }
    
    header('Location: albums.php');
    exit;
} catch (PDOException $e) {
    // В случае ошибки откатываем транзакцию
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    die('Database error: ' . $e->getMessage());
} 