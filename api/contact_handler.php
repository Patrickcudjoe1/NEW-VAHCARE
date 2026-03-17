<?php
header('Content-Type: application/json');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if (!$name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Name and Email are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, phone, service_interest, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $service, $message]);

        echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent.']);
    } catch (\PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
