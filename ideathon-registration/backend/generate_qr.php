<?php
// backend/generate_qr.php
// usage: generate_qr.php?team=Team+Name
if (!isset($_GET['team'])) {
    http_response_code(400);
    echo 'Missing team';
    exit;
}
$team = trim($_GET['team']);
$teamEnc = rawurlencode($team);

// you can include more data (id, contact) if you like
$qrUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . $teamEnc . "&chld=L|1";

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['qr' => $qrUrl]);
