<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config.php';

$lang = $_GET['lang'] ?? 'ru';
$allowed_langs = ['ru', 'et', 'en', 'uk'];
if (!in_array($lang, $allowed_langs)) {
    $lang = 'ru';
}

try {
    $conn = getDBConnection();
    $stmt = $conn->prepare('
        SELECT 
            a.id,
            a.title_' . $lang . ' as title,
            a.cover_path,
            a.created_at,
            (SELECT COUNT(*) FROM photos WHERE album_id = a.id) as photos_count,
            (SELECT COUNT(*) FROM videos WHERE album_id = a.id) as videos_count
        FROM albums a
        ORDER BY a.created_at DESC
    ');
    $stmt->execute();
    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'albums' => $albums,
        'hasMore' => false // Since we're not implementing pagination yet
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?> 