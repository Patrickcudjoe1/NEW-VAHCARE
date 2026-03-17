<?php
header('Content-Type: application/json');
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
        exit;
    }
    $first_name = filter_input(INPUT_POST, 'first-name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last-name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $experience = filter_input(INPUT_POST, 'experience', FILTER_SANITIZE_STRING);
    $availability = filter_input(INPUT_POST, 'availability', FILTER_SANITIZE_STRING);
    $cover_letter = filter_input(INPUT_POST, 'cover-letter', FILTER_SANITIZE_STRING);

    if (!$first_name || !$last_name || !$email || !isset($_FILES['resume'])) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields and upload your resume.']);
        exit;
    }

    // Handle File Upload
    $upload_dir = '../uploads/resumes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_ext = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
    $allowed_exts = ['pdf', 'doc', 'docx'];

    if (!in_array(strtolower($file_ext), $allowed_exts)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDF and DOCX are allowed.']);
        exit;
    }

    $new_filename = uniqid('resume_', true) . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($_FILES['resume']['tmp_name'], $upload_path)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO job_applications (first_name, last_name, email, phone, experience, availability, cover_letter, resume_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $phone, $experience, $availability, $cover_letter, $upload_path]);

            // Send Email Notification
            $to = ADMIN_EMAIL;
            $subject = "New Job Application: " . $first_name . " " . $last_name;
            $body = "Name: $first_name $last_name\nEmail: $email\nPhone: $phone\nExperience: $experience\nAvailability: $availability\n\nCover Letter:\n$cover_letter";
            $headers = "From: " . $email;
            
            @mail($to, $subject, $body, $headers);

            echo json_encode(['success' => true, 'message' => 'Your application has been submitted successfully!']);
        } catch (\PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload resume.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
