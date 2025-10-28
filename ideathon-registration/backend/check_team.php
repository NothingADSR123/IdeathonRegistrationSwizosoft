<?php
// backend/check_team.php
require_once 'db_connect.php';
header('Content-Type: text/plain; charset=utf-8');

$team = isset($_POST['team_name']) ? trim($_POST['team_name']) : '';

if ($team === '') {
    echo 'available';
    exit;
}

$stmt = $conn->prepare("SELECT id FROM registrations WHERE team_name = ? LIMIT 1");
$stmt->bind_param('s', $team);
$stmt->execute();
$res = $stmt->get_result();
echo ($res && $res->num_rows > 0) ? 'taken' : 'available';
$stmt->close();
