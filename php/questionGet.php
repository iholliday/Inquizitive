<?php
require_once("./_connect.php");
$db = new inquizitiveDB();
$conn = $db->connect;

header("Content-Type: application/json");

if (!isset($_POST["quizUUID"], $_POST["questionUUID"])) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Missing params"]);
  exit;
}

$quizUUID = mysqli_real_escape_string($conn, $_POST["quizUUID"]);
$questionUUID = mysqli_real_escape_string($conn, $_POST["questionUUID"]);

$res = $db->Query("CALL GetQuestionByUUID(?, ?);", [$quizUUID, $questionUUID]);
if (!$res) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "DB error"]);
  exit;
}

$row = mysqli_fetch_assoc($res);
echo json_encode(["ok" => true, "data" => $row]);