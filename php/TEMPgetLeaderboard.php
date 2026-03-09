<?php
require_once ("./blockDirectAccess.php");
require_once ("./_connect.php");

header('Content-Type: application/json');
$db = new InquizitiveDB();

// currency itemUUID
$currencyUUID = 'ec0ad14f-12c5-11f1-98eb-bc2411ac3867';

// Get top 5 users by currency quantity
$stmt = $db->Query("
    SELECT u.userUUID, u.firstName, u.lastName, 
           COALESCE(ui.quantity, 0) AS score
    FROM user u
    LEFT JOIN userInventory ui
      ON u.userUUID = ui.userUUID
      AND ui.itemUUID = '$currencyUUID'
    WHERE u.isDisabled = 0
    ORDER BY score DESC
    LIMIT 5
");

$data = [];
while($row = $stmt->fetch_assoc()){
    $data[] = $row;
}

echo json_encode(['status'=>'success','data'=>$data]);
exit;