<?php

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    http_response_code(403);
    exit;
}

header('Content-Type: application/json');

$userUUID = trim($_POST['userUUID'] ?? '');
$firstName = trim($_POST['firstName'] ?? '');
$lastName  = trim($_POST['lastName'] ?? '');
$email     = trim($_POST['email'] ?? '');

if ($userUUID === '' || $firstName === '' || $lastName === '' || $email === '') {
    echo json_encode([
        "ok" => false,
        "message" => "All fields are required."
    ]);
    exit;
}

$db = new inquizitiveDB();
$conn = $db->connect;

$db->Query("CALL EditStudent(?, ?, ?, ?);", [$userUUID, $firstName, $lastName, $email]);

while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
    $extra = mysqli_store_result($conn);
    if ($extra) mysqli_free_result($extra);
}

echo json_encode([
    "ok" => true,
    "message" => "Student updated successfully."
]);