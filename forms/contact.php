<?php
<?php
/**
 * Unified PHP Router for Contact Form, Resume Download, and other endpoints
 * Place this file at: d:\Recycle\forms\contact.php or d:\Recycle\api.php
 */

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight (CORS OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/forms', '', $path); // adjust if needed

// Router: match path to handler
switch ($path) {
    case '/contact':
    case '/Datasheet':
        handleContact($method);
        break;

    case '/download':
    case '/download/resume':
        handleDownload($method);
        break;

    case '/api/contact':
        handleContactJSON($method);
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}

exit;

/**
 * Handle traditional form POST (application/x-www-form-urlencoded)
 * Stores in CSV and optionally sends email
 */
function handleContact($method) {
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
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
        return;
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

    // Send email (optional)
    $to = 'your-email@example.com'; // CHANGE THIS
    $mail_subject = "Website Contact: " . $safe_subject;
    $mail_body = "Name: $safe_name\nEmail: $safe_email\n\nMessage:\n$safe_message";
    $headers = "From: noreply@example.com\r\nReply-To: $safe_email\r\nContent-Type: text/plain; charset=UTF-8\r\n";

    // Uncomment to enable email (requires mail server):
    // mail($to, $mail_subject, $mail_body, $headers);

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message was received.']);
}

/**
 * Handle JSON POST (application/json)
 * Same validation/storage as handleContact but accepts JSON body
 */
function handleContactJSON($method) {
    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    // Parse JSON body
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        return;
    }

    $name = trim($body['name'] ?? '');
    $email = trim($body['email'] ?? '');
    $subject = trim($body['subject'] ?? '');
    $message = trim($body['message'] ?? '');

    // Validate
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
    if (empty($subject)) $errors[] = 'Subject is required';
    if (empty($message)) $errors[] = 'Message is required';

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'errors' => $errors]);
        return;
    }

    // Sanitize and store (same as above)
    $safe_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safe_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $safe_subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    $safe_message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

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

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Message received!']);
}

/**
 * Handle resume download (stream remote PDF or local file)
 */
function handleDownload($method) {
    if ($method !== 'GET' && $method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    // Option A: Stream remote file (OneDrive, URL, etc.)
    $remoteUrl = 'https://1drv.ms/b/c/c365a2806ea20738/IQDeFD_SWbDjRKszsEuzCmTVAatFek3NxlkKv4MoccJSTms?e=VoJIck';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remoteUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)'
    ]);

    // Set headers to force download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Resume.pdf"');
    header('Cache-Control: public, must-revalidate');
    header('Pragma: public');

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        http_response_code(500);
        echo "Failed to fetch remote file (HTTP $httpCode)";
        return;
    }

    echo $result;
}

// Option B: Local file download (uncomment if using local file)
/*
function downloadLocalFile() {
    $filePath = __DIR__ . '/../assets/files/resume.pdf';
    if (!file_exists($filePath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'File not found']);
        return;
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Resume.pdf"');
    header('Content-Length: ' . filesize($filePath));

    readfile($filePath);
}
*/
?>