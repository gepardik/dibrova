<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Album ID is required']);
    exit;
}

$lang = $_GET['lang'] ?? 'ru';
$allowed_langs = ['ru', 'et', 'en', 'uk'];
if (!in_array($lang, $allowed_langs)) {
    $lang = 'ru';
}

try {
    $conn = getDBConnection();
    
    // Получаем информацию об альбоме
    $albumStmt = $conn->prepare('
        SELECT 
            id,
            title_' . $lang . ' as title,
            cover_path,
            created_at
        FROM albums 
        WHERE id = ?
    ');
    $albumStmt->execute([$_GET['id']]);
    $album = $albumStmt->fetch(PDO::FETCH_ASSOC);

    if (!$album) {
        http_response_code(404);
        echo json_encode(['error' => 'Album not found']);
        exit;
    }

    // Получаем фотографии альбома
    $photosStmt = $conn->prepare('
        SELECT 
            id,
            original_path,
            medium_path,
            thumbnail_path
        FROM photos 
        WHERE album_id = ?
        ORDER BY position ASC, created_at DESC
    ');
    $photosStmt->execute([$_GET['id']]);
    $album['photos'] = $photosStmt->fetchAll(PDO::FETCH_ASSOC);

    // Получаем видео альбома
    $videosStmt = $conn->prepare('
        SELECT 
            id,
            youtube_id,
            title_' . $lang . ' as title
        FROM videos 
        WHERE album_id = ?
        ORDER BY position ASC, created_at DESC
    ');
    $videosStmt->execute([$_GET['id']]);
    $album['videos'] = $videosStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($album);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 