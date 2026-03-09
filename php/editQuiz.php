<?php

require_once "./php/_connect.php";

$db = new inquizitiveDB();
$conn = $db->connect;

$quizUUID = $_POST['quizUUID'] ?? '';
$quizName = $_POST['quizName'] ?? '';
$subjectUUID = $_POST['subjectUUID'] ?? '';

$stmt = $conn->prepare("CALL EditQuiz(?,?,?)");
$stmt->bind_param("sss", $quizUUID, $quizName, $subjectUUID);

if($stmt->execute()){
    echo json_encode(["ok"=>true]);
}else{
    echo json_encode(["ok"=>false,"message"=>"Database error"]);
}

$stmt->close();