<?php
require_once("./_connect.php");
$db = new inquizitiveDB();
$conn = $db->connect;

header("Content-Type: application/json");

$mode = isset($_POST["mode"]) && $_POST["mode"] === "edit" ? "edit" : "add";

$required = ["quizUUID","questionText","answerA","answerB","answerC","answerD","difficultyPoints","correctAnswer"];
foreach ($required as $r) {
  if (!isset($_POST[$r])) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => "Missing $r"]);
    exit;
  }
}

$quizUUID = mysqli_real_escape_string($conn, $_POST["quizUUID"]);
$questionUUID = isset($_POST["questionUUID"]) ? mysqli_real_escape_string($conn, $_POST["questionUUID"]) : "";

$questionText = trim($_POST["questionText"]);
$answerA = trim($_POST["answerA"]);
$answerB = trim($_POST["answerB"]);
$answerC = trim($_POST["answerC"]);
$answerD = trim($_POST["answerD"]);
$correctAnswer = trim($_POST["correctAnswer"]);
$difficultyPoints = (int)$_POST["difficultyPoints"];
if ($difficultyPoints < 1) $difficultyPoints = 1;

if ($questionText === "" || $answerA === "" || $answerB === "" || $answerC === "" || $answerD === "") {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Empty fields not allowed"]);
  exit;
}

$answers = [trim($answerA), trim($answerB), trim($answerC), trim($answerD)];
if (!in_array(trim($correctAnswer), $answers, true)) {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Correct answer must match one of A-D exactly"]);
  exit;
}

if ($mode === "add") {
  $res = $db->Query(
    "CALL AddQuestion(?, ?, ?, ?, ?, ?, ?, ?);",
    [$quizUUID, $questionText, $correctAnswer, $answerA, $answerB, $answerC, $answerD, $difficultyPoints]
  );

  if (!$res) {
    http_response_code(500);
    echo json_encode(["ok" => false, "error" => "Insert failed"]);
    exit;
  }

  $row = mysqli_fetch_assoc($res);
  echo json_encode(["ok" => true, "mode" => "add", "questionUUID" => $row["questionUUID"] ?? null]);
  exit;
}

// edit
if ($questionUUID === "") {
  http_response_code(400);
  echo json_encode(["ok" => false, "error" => "Missing questionUUID for edit"]);
  exit;
}

$res = $db->Query(
  "CALL EditQuestion(?, ?, ?, ?, ?, ?, ?, ?, ?);",
  [$quizUUID, $questionUUID, $questionText, $correctAnswer, $answerA, $answerB, $answerC, $answerD, $difficultyPoints]
);

if (!$res) {
  http_response_code(500);
  echo json_encode(["ok" => false, "error" => "Update failed"]);
  exit;
}

$row = mysqli_fetch_assoc($res);
$rowsUpdated = (int)($row["rowsUpdated"] ?? 0);

echo json_encode(["ok" => true, "mode" => "edit", "rowsUpdated" => $rowsUpdated]);