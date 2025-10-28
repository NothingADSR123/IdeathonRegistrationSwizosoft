<?php
// backend/db_connect.php
// Adjust credentials for your environment
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';        // <-- set your DB password
$DB_NAME = 'ideathon_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['status'=>'error','message'=>'DB connect error']));
}

/**
 * Define upload constants
 */
define('UPLOAD_DIR_PHYSICAL', __DIR__ . '/../assets/uploads/'); // filesystem path
define('UPLOAD_DIR_REL', 'assets/uploads/'); // path stored in DB and for linking
define('MAX_UPLOAD_BYTES', 2 * 1024 * 1024); // 2 MB
$ALLOWED_EXT = ['png','jpg','jpeg','gif','webp'];
?>
