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

$video_id = $_GET['id'];
$album_id = $_GET['album_id'];

try {
    $conn = getDBConnection();
    
    // Удаляем видео
    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ? AND album_id = ?");
    $stmt->execute([$video_id, $album_id]);
    
    header('Location: album-videos.php?id=' . $album_id);
    exit;
} catch (PDOException $e) {
    die('Database error: ' . $e->getMessage());
} 