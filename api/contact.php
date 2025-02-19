<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['name']) || !isset($data['email']) || !isset($data['message'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    $name = htmlspecialchars($data['name']);
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars($data['message']);

    if (!$email) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email format']);
        exit;
    }

    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare('INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $message]);

        // Отправка уведомления на email администратора
        $to = 'admin@dibrova.ee';
        $subject = 'New Contact Form Submission';
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $emailBody = "New message from contact form:\n\n";
        $emailBody .= "Name: $name\n";
        $emailBody .= "Email: $email\n";
        $emailBody .= "Message:\n$message";

        mail($to, $subject, $emailBody, $headers);

        echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?> 