<?php
<?php
// Set CORS headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight (CORS OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate
$errors = [];
if (empty($name)) $errors[] = 'Name is required';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
if (empty($subject)) $errors[] = 'Subject is required';
if (empty($message)) $errors[] = 'Message is required';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Sanitize
$safe_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
$safe_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$safe_subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$safe_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

// Store in CSV
$csvFile = __DIR__ . '/../submissions.csv';
if (!file_exists($csvFile)) {
    $fp = fopen($csvFile, 'w');
    fputcsv($fp, ['Timestamp', 'Name', 'Email', 'Subject', 'Message']);
    fclose($fp);
}
$fp = fopen($csvFile, 'a');
if ($fp) {
    fputcsv($fp, [date('Y-m-d H:i:s'), $safe_name, $safe_email, $safe_subject, $safe_message]);
    fclose($fp);
}

// Success response
http_response_code(200);
echo json_encode(['success' => true, 'message' => 'Thank you! Your message was received.']);
?>