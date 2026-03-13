<?php

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    http_response_code(403);
    exit;
}

header('Content-Type: application/json');

$userUUID = $_POST['userUUID'] ?? '';
$subjectUUID = $_POST['subjectUUID'] ?? '';

if ($userUUID === '' || $subjectUUID === '') {
    echo json_encode([
        "ok" => false,
        "message" => "Missing required fields."
    ]);
    exit;
}

$db = new inquizitiveDB();
$conn = $db->connect;

$db->Query("CALL AddStudentSubject(?, ?);", [$userUUID, $subjectUUID]);

while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
    $extra = mysqli_store_result($conn);
    if ($extra) mysqli_free_result($extra);
}

echo json_encode([
    "ok" => true,
    "message" => "Subject added successfully."
]);