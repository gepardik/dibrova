<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../utils/response.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    sendResponse(400, ['error' => 'Album ID is required']);
}

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASSWORD
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $albumId = (int)$_GET['id'];

    // Get photos for the album
    $stmt = $db->prepare("
        SELECT *
        FROM photos
        WHERE album_id = :album_id
        ORDER BY position ASC, created_at DESC
    ");

    $stmt->bindParam(':album_id', $albumId, PDO::PARAM_INT);
    $stmt->execute();

    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    sendResponse(200, $photos);
} catch (PDOException $e) {
    sendResponse(500, ['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    sendResponse(500, ['error' => 'Server error: ' . $e->getMessage()]);
} 