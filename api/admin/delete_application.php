<?php
require_once '../../includes/db.php';
require_once '../../includes/functions.php';

start_secure_session();

if (!is_admin_logged_in()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
        exit;
    }

    if ($id) {
        try {
            // Get resume path first to delete the file
            $stmt = $pdo->prepare("SELECT resume_path FROM job_applications WHERE id = ?");
            $stmt->execute([$id]);
            $application = $stmt->fetch();

            if ($application) {
                // Delete file if it exists
                $filePath = '../../' . $application['resume_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $stmt = $pdo->prepare("DELETE FROM job_applications WHERE id = ?");
                if ($stmt->execute([$id])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Database error']);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Application not found']);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    }
}
?>
