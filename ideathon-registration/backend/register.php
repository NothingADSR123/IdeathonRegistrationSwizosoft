<?php
// backend/register.php
require_once 'db_connect.php';
header('Content-Type: application/json; charset=utf-8');




// helper
function json_exit($arr)
{
    echo json_encode($arr);
    exit;
}

// only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_exit(['status' => 'error', 'message' => 'Invalid method']);
}

// basic POST fields
$team_name   = isset($_POST['team_name']) ? trim($_POST['team_name']) : '';
$leader_name = isset($_POST['leader_name']) ? trim($_POST['leader_name']) : '';
$email       = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone       = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$university  = isset($_POST['university']) ? trim($_POST['university']) : '';
$state       = isset($_POST['state']) ? trim($_POST['state']) : '';
$region      = isset($_POST['region']) ? trim($_POST['region']) : '';
$member1     = isset($_POST['member1']) ? trim($_POST['member1']) : null;
$member2     = isset($_POST['member2']) ? trim($_POST['member2']) : null;

// minimal validation
if ($team_name === '' || $leader_name === '' || $email === '' || $phone === '') {
    json_exit(['status' => 'error', 'message' => 'Missing required fields']);
}

// check duplicates (team name OR email OR phone)
$dupStmt = $conn->prepare("SELECT id FROM registrations WHERE team_name = ? OR email = ? OR phone = ? LIMIT 1");
$dupStmt->bind_param('sss', $team_name, $email, $phone);
$dupStmt->execute();
$dupRes = $dupStmt->get_result();
if ($dupRes && $dupRes->num_rows > 0) {
    json_exit(['status' => 'error', 'message' => 'Team name, email, or phone already registered']);
}
$dupStmt->close();

// handle file upload
if (!isset($_FILES['payment_screenshot']) || $_FILES['payment_screenshot']['error'] !== UPLOAD_ERR_OK) {
    json_exit(['status' => 'error', 'message' => 'Payment screenshot required']);
}

$file = $_FILES['payment_screenshot'];

// size
if ($file['size'] > MAX_UPLOAD_BYTES) {
    json_exit(['status' => 'error', 'message' => 'File too large (max 2MB)']);
}

// extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
global $ALLOWED_EXT;
if (!in_array($ext, $ALLOWED_EXT)) {
    json_exit(['status' => 'error', 'message' => 'Invalid file type']);
}

// ensure it's an image
$img = @getimagesize($file['tmp_name']);
if ($img === false) {
    json_exit(['status' => 'error', 'message' => 'Uploaded file is not an image']);
}

// ensure upload dir exists
if (!is_dir(UPLOAD_DIR_PHYSICAL)) {
    mkdir(UPLOAD_DIR_PHYSICAL, 0755, true);
}

// Step 1: temporary random filename
$tempName = bin2hex(random_bytes(8)) . '.' . $ext;
$tempPath = UPLOAD_DIR_PHYSICAL . $tempName;

// move it first
if (!move_uploaded_file($file['tmp_name'], $tempPath)) {
    json_exit(['status' => 'error', 'message' => 'Failed to save file']);
}
@chmod($tempPath, 0644);

// Step 2: insert basic registration (temporary screenshot name)
$tempRelPath = UPLOAD_DIR_REL . $tempName;
$ins = $conn->prepare("INSERT INTO registrations 
    (team_name, leader_name, email, phone, university, state, region, member1_name, member2_name, payment_screenshot)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$ins->bind_param('ssssssssss', $team_name, $leader_name, $email, $phone, $university, $state, $region, $member1, $member2, $tempRelPath);

if (!$ins->execute()) {
    if (is_file($tempPath)) @unlink($tempPath);
    json_exit(['status' => 'error', 'message' => 'DB insert failed']);
}

// Step 3: get insert ID
$insertId = $ins->insert_id;
$ins->close();

// Step 4: create new unique name with team prefix + ID
$teamPrefix = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($team_name));
$uniqueSuffix = bin2hex(random_bytes(4));
$newFileName = "{$teamPrefix}_{$insertId}_{$uniqueSuffix}.{$ext}";
$newPhysicalPath = UPLOAD_DIR_PHYSICAL . $newFileName;

// rename file physically
rename($tempPath, $newPhysicalPath);
$newRelPath = UPLOAD_DIR_REL . $newFileName;

// Step 5: update DB record with new filename
$upd = $conn->prepare("UPDATE registrations SET payment_screenshot=? WHERE id=?");
$upd->bind_param('si', $newRelPath, $insertId);
$upd->execute();
$upd->close();

// Step 6: final response
json_exit(['status' => 'success', 'message' => 'Registered successfully', 'team' => $team_name, 'id' => $insertId]);
