<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $conn = getDBConnection();
        
        // Получаем язык из параметра запроса или используем русский по умолчанию
        $lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';
        $validLangs = ['ru', 'et', 'en', 'uk'];
        if (!in_array($lang, $validLangs)) {
            $lang = 'ru';
        }

        // Получаем текущую дату
        $currentDate = date('Y-m-d');

        // Получаем предстоящие концерты
        $upcomingStmt = $conn->prepare("
            SELECT 
                id,
                title_{$lang} as title,
                description_{$lang} as description,
                date,
                time,
                venue_{$lang} as venue,
                price,
                ticket_link,
                image_path
            FROM concerts 
            WHERE date >= ? AND is_active = TRUE
            ORDER BY date ASC
        ");
        $upcomingStmt->execute([$currentDate]);
        $upcomingConcerts = $upcomingStmt->fetchAll(PDO::FETCH_ASSOC);

        // Получаем прошедшие концерты
        $pastStmt = $conn->prepare("
            SELECT 
                id,
                title_{$lang} as title,
                description_{$lang} as description,
                date,
                time,
                venue_{$lang} as venue,
                price,
                ticket_link,
                image_path
            FROM concerts 
            WHERE date < ? AND is_active = TRUE
            ORDER BY date DESC
        ");
        $pastStmt->execute([$currentDate]);
        $pastConcerts = $pastStmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'upcoming' => $upcomingConcerts,
            'past' => $pastConcerts
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 