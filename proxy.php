<?php

// =============================
// BASIC SECURITY CONFIG
// =============================
$SECRET_KEY = "YOUR_SECRET_KEY_HERE";  // change this to a long random string

$ALLOWED_REFERRERS = [
    "yourdomain.com",
    "mrmlivofficial.netlify.app"
];

// =============================
// VALIDATE SECRET KEY
// =============================
if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    http_response_code(403);
    die("Invalid Key");
}

// =============================
// VALIDATE REFERRER
// =============================
if (!isset($_SERVER['HTTP_REFERER'])) {
    http_response_code(403);
    die("No Referrer");
}

$ref = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);

if (!in_array($ref, $ALLOWED_REFERRERS)) {
    http_response_code(403);
    die("Referrer Blocked");
}

// =============================
// VALIDATE URL PARAM
// =============================
if (!isset($_GET['u'])) {
    http_response_code(400);
    die("Missing URL");
}

$streamUrl = base64_decode($_GET['u']);

// =============================
// FETCH REAL STREAM
// =============================
$ch = curl_init($streamUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$data = curl_exec($ch);
curl_close($ch);

// =============================
// OUTPUT STREAM
// =============================
header("Content-Type: application/vnd.apple.mpegurl");
header("Access-Control-Allow-Origin: *");
echo $data;

?>
