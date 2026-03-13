<?php 
require_once __DIR__ . "/_connect.php";
$db = new inquizitiveDB();
$conn = $db->connect;
header("Content-Type: application/json; charset=utf-8");

if (!isset($_POST["quizName"], $_POST["subjectUUID"])){
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Missing quizName or subjectUUID"]);
    exit;
}

$quizName = trim($_POST["quizName"]);
$subjectUUID = trim($_POST["subjectUUID"]);

if ($quizName === ""){
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Quiz name cannot be empty"]);
    exit;
}

if ($subjectUUID === ""){
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Subject cannot be empty"]);
    exit;
}

$quizNameEsc = mysqli_real_escape_string($conn, $quizName);
$subjectEsc = mysqli_real_escape_string($conn, $subjectUUID);


$res = $db->Query("CALL CreateQuiz(?, ?);", [$quizNameEsc, $subjectEsc]);

if (!$res){
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Database error running CreateQuiz"]);
    exit;
}

$row = mysqli_fetch_assoc($res);
$newQuizUUID = $row["quizUUID"] ?? null;

mysqli_free_result($res);
while (mysqli_more_results($conn) && mysqli_next_result($conn)){
    $junk = mysqli_store_result($conn);
    if ($junk) mysqli_free_result($junk);
}
if (!$newQuizUUID){
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Quiz created but UUID not returned"]);
    exit;
}

echo json_encode([
    "ok"=> true, 
    "quizUUID"=> $newQuizUUID
]);