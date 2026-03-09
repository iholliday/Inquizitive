<?php

// Required includes.
require_once ("./php/blockDirectAccess.php");
require_once ("_connect.php");

// Make new database instance and set response type to JSON.
header('Content-Type: application/json');
$db = new InquizitiveDB();

// If subjectUUID isn't set, send error.
if(!isset($_POST['subjectUUID']))
{
    echo json_encode(['status'=>'error','message'=>'No subject selected.']);
    exit;
}

// Query database to get leaderboard data.
$subjectUUID = $_POST['subjectUUID'];
$stmt = $db->Query("CALL GetLeaderboardForSubjectUUID(?)", [$subjectUUID]);
$data = [];
while($row = $stmt->fetch_assoc())
{
    $escaped = ["firstName" => htmlspecialchars($row['firstName']), "lastName" => htmlspecialchars($row['lastName']), "totalScore" => htmlspecialchars($row['totalScore'])];
    array_push($data,$escaped);
}

// Send success status & data via JSON.
echo json_encode(['status' => 'success','data' => $data]);
exit;