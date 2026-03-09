<?php

// Required includes.
require_once ("./blockDirectAccess.php");
require_once ("./_connect.php");

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
$stmt = $db->Query("CALL GetLeaderboard(?)", [$subjectUUID]);
$data = [];
while($row = $stmt->fetch_assoc())
{
    $data[] = $row;
}

// Send success status & data via JSON.
echo json_encode(['status' => 'success','data' => $data]);
exit;