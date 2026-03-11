<?php
require_once __DIR__ . "/_connect.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    http_response_code(403);
    exit;
}

header('Content-Type: application/json');

$userUUID = trim($_POST['userUUID'] ?? '');

if ($userUUID === '') {
    echo json_encode([
        "ok" => false,
        "message" => "Missing userUUID."
    ]);
    exit;
}

$db = new inquizitiveDB();
$conn = $db->connect;

$subjects = [];

if ($result = $db->Query("CALL GetStudentSubjects(?);", [$userUUID])) {
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row;
    }
    mysqli_free_result($result);
}

while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
    $extra = mysqli_store_result($conn);
    if ($extra) mysqli_free_result($extra);
}

echo json_encode([
    "ok" => true,
    "subjects" => $subjects
]);