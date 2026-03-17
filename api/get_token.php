<?php
header('Content-Type: application/json');
require_once '../includes/functions.php';

echo json_encode(['token' => generate_csrf_token()]);
?>
