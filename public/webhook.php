<?php
// Define a secret token to validate the request (optional)
$secret = 'your_secret_key';

// Get request headers
$headers = getallheaders();
$hubSignature = $headers['X-Hub-Signature-256'] ?? '';

// Verify secret token if it’s set (recommended for security)
$payload = file_get_contents('php://input');
$expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

if (hash_equals($expectedSignature, $hubSignature)) {
    // Execute the deployment script
    shell_exec('/var/www/OnlySchool/deploy.sh');
    http_response_code(200);
    echo "Webhook received and deployment script executed.";
} else {
    http_response_code(403);
    echo "Invalid signature.";
}
?>
