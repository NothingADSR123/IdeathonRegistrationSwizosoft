<?php
// backend/register.php
require_once 'db_connect.php';
header('Content-Type: application/json; charset=utf-8');

// helper
function json_exit($arr) {
    echo json_encode($arr);
    exit;
}

// only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_exit(['status'=>'error','message'=>'Invalid method']);
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
    json_exit(['status'=>'error','message'=>'Missing required fields']);
}

// check duplicates (team name OR email OR phone)
$dupStmt = $conn->prepare("SELECT id FROM registrations WHERE team_name = ? OR email = ? OR phone = ? LIMIT 1");
$dupStmt->bind_param('sss', $team_name, $email, $phone);
$dupStmt->execute();
$dupRes = $dupStmt->get_result();
if ($dupRes && $dupRes->num_rows > 0) {
    json_exit(['status'=>'error','message'=>'Team name, email, or phone already registered']);
}
$dupStmt->close();

// handle file upload
if (!isset($_FILES['payment_screenshot']) || $_FILES['payment_screenshot']['error'] !== UPLOAD_ERR_OK) {
    json_exit(['status'=>'error','message'=>'Payment screenshot required']);
}

$file = $_FILES['payment_screenshot'];

// size
if ($file['size'] > MAX_UPLOAD_BYTES) {
    json_exit(['status'=>'error','message'=>'File too large (max 2MB)']);
}

// extension
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
global $ALLOWED_EXT;
if (!in_array($ext, $ALLOWED_EXT)) {
    json_exit(['status'=>'error','message'=>'Invalid file type']);
}

// ensure it's an image
$img = @getimagesize($file['tmp_name']);
if ($img === false) {
    json_exit(['status'=>'error','message'=>'Uploaded file is not an image']);
}

// ensure upload dir exists
if (!is_dir(UPLOAD_DIR_PHYSICAL)) {
    mkdir(UPLOAD_DIR_PHYSICAL, 0755, true);
}

// random filename
$random = bin2hex(random_bytes(12)) . '.' . $ext;
$target = UPLOAD_DIR_PHYSICAL . $random;

if (!move_uploaded_file($file['tmp_name'], $target)) {
    json_exit(['status'=>'error','message'=>'Failed to save file']);
}
@chmod($target, 0644);

// store relative path
$relPath = UPLOAD_DIR_REL . $random;

// insert into DB
$ins = $conn->prepare("INSERT INTO registrations (team_name, leader_name, email, phone, university, state, region, member1_name, member2_name, payment_screenshot)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$ins->bind_param('ssssssssss', $team_name, $leader_name, $email, $phone, $university, $state, $region, $member1, $member2, $relPath);

if ($ins->execute()) {
    $insertId = $ins->insert_id;
    json_exit(['status'=>'success','message'=>'Registered','team'=>$team_name,'id'=>$insertId]);
} else {
    // cleanup file
    if (is_file($target)) @unlink($target);
    json_exit(['status'=>'error','message'=>'DB insert failed']);
}
