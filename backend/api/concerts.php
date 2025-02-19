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

        // Если указан ID концерта, возвращаем информацию о конкретном концерте
        if (isset($_GET['id'])) {
            $stmt = $conn->prepare("
                SELECT 
                    id,
                    title_{$lang} as title,
                    description_{$lang} as description,
                    DATE_FORMAT(date, '%d.%m.%Y') as date,
                    TIME_FORMAT(time, '%H:%i') as time,
                    venue_{$lang} as venue,
                    price,
                    ticket_link as ticketLink,
                    CONCAT('/', image_path) as image,
                    is_active
                FROM concerts 
                WHERE id = ? AND is_active = TRUE
            ");
            $stmt->execute([$_GET['id']]);
            $concert = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($concert) {
                // Преобразуем поля для соответствия ожидаемому формату
                $concert = array_combine(
                    array_map(function($key) {
                        switch($key) {
                            case 'ticket_link': return 'ticketLink';
                            case 'image_path': return 'image';
                            default: return $key;
                        }
                    }, array_keys($concert)),
                    array_values($concert)
                );
                echo json_encode($concert);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Concert not found']);
            }
            exit;
        }

        // Получаем текущую дату
        $currentDate = date('Y-m-d');

        // Получаем предстоящие концерты
        $upcomingStmt = $conn->prepare("
            SELECT 
                id,
                title_{$lang} as title,
                description_{$lang} as description,
                DATE_FORMAT(date, '%d.%m.%Y') as date,
                TIME_FORMAT(time, '%H:%i') as time,
                venue_{$lang} as venue,
                price,
                ticket_link as ticketLink,
                CONCAT('/', image_path) as image
            FROM concerts 
            WHERE date >= ? AND is_active = TRUE
            ORDER BY date ASC
        ");
        $upcomingStmt->execute([$currentDate]);
        $upcomingConcerts = $upcomingStmt->fetchAll(PDO::FETCH_ASSOC);

        // Преобразуем поля в массиве предстоящих концертов
        $upcomingConcerts = array_map(function($concert) {
            return array_combine(
                array_map(function($key) {
                    switch($key) {
                        case 'ticket_link': return 'ticketLink';
                        case 'image_path': return 'image';
                        default: return $key;
                    }
                }, array_keys($concert)),
                array_values($concert)
            );
        }, $upcomingConcerts);

        // Получаем прошедшие концерты
        $pastStmt = $conn->prepare("
            SELECT 
                id,
                title_{$lang} as title,
                description_{$lang} as description,
                DATE_FORMAT(date, '%d.%m.%Y') as date,
                TIME_FORMAT(time, '%H:%i') as time,
                venue_{$lang} as venue,
                price,
                ticket_link as ticketLink,
                CONCAT('/', image_path) as image
            FROM concerts 
            WHERE date < ? AND is_active = TRUE
            ORDER BY date DESC
        ");
        $pastStmt->execute([$currentDate]);
        $pastConcerts = $pastStmt->fetchAll(PDO::FETCH_ASSOC);

        // Преобразуем поля в массиве прошедших концертов
        $pastConcerts = array_map(function($concert) {
            return array_combine(
                array_map(function($key) {
                    switch($key) {
                        case 'ticket_link': return 'ticketLink';
                        case 'image_path': return 'image';
                        default: return $key;
                    }
                }, array_keys($concert)),
                array_values($concert)
            );
        }, $pastConcerts);

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