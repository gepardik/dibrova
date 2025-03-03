<?php
require_once '../../config.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Album ID is required']);
    exit;
}

try {
    $conn = getDBConnection();
    $albumId = (int)$_GET['id'];

    // Get videos for the album
    $stmt = $conn->prepare("
        SELECT *
        FROM videos
        WHERE album_id = :album_id
        ORDER BY position ASC, created_at DESC
    ");

    $stmt->bindParam(':album_id', $albumId, PDO::PARAM_INT);
    $stmt->execute();

    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($videos);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 