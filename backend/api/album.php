<?php
require_once '../config.php';

// Получаем параметры
$lang = $_GET['lang'] ?? DEFAULT_LANGUAGE;
$album_id = $_GET['id'] ?? null;

if (!$album_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Album ID is required']);
    exit;
}

try {
    $conn = getDBConnection();
    
    // Получаем информацию об альбоме
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            (SELECT COUNT(*) FROM photos WHERE album_id = a.id) as photos_count,
            (SELECT COUNT(*) FROM videos WHERE album_id = a.id) as videos_count
        FROM albums a
        WHERE a.id = ?
    ");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$album) {
        http_response_code(404);
        echo json_encode(['error' => 'Album not found']);
        exit;
    }
    
    // Получаем фотографии альбома
    $stmt = $conn->prepare("SELECT * FROM photos WHERE album_id = ? ORDER BY position");
    $stmt->execute([$album_id]);
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Получаем видео альбома
    $stmt = $conn->prepare("SELECT * FROM videos WHERE album_id = ? ORDER BY position");
    $stmt->execute([$album_id]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Формируем ответ
    $response = [
        'id' => $album['id'],
        'title' => $album['title_' . $lang],
        'cover_path' => $album['cover_path'],
        'photos_count' => $album['photos_count'],
        'videos_count' => $album['videos_count'],
        'created_at' => $album['created_at'],
        'photos' => array_map(function($photo) {
            return [
                'id' => $photo['id'],
                'original_path' => $photo['original_path'],
                'medium_path' => $photo['medium_path'],
                'thumbnail_path' => $photo['thumbnail_path'],
                'position' => $photo['position']
            ];
        }, $photos),
        'videos' => array_map(function($video) use ($lang) {
            return [
                'id' => $video['id'],
                'youtube_id' => $video['youtube_id'],
                'title' => $video['title_' . $lang],
                'position' => $video['position']
            ];
        }, $videos)
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 