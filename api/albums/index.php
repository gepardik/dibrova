<?php
require_once '../../config.php';

header('Content-Type: application/json');

try {
    $conn = getDBConnection();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
    $offset = ($page - 1) * $limit;

    // Get total count
    $countStmt = $conn->query("SELECT COUNT(*) FROM albums");
    $total = $countStmt->fetchColumn();

    // Get albums with photos and videos count
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            (SELECT COUNT(*) FROM photos WHERE album_id = a.id) as photos_count,
            (SELECT COUNT(*) FROM videos WHERE album_id = a.id) as videos_count
        FROM albums a
        ORDER BY a.created_at DESC
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'albums' => $albums,
        'total' => $total,
        'hasMore' => ($offset + $limit) < $total
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} 