<?php
header('Content-Type: application/json; charset=utf-8');

if(isset($_POST['avatar']) && isset($_SESSION['userUUID']))
{
    $db = new inquizitiveDB();
    $userUUID = $_SESSION['userUUID'];
    $avatar = $_POST['avatar'];
    $result = $db->Query("CALL UpdateAvatarByUUID(?,?)",[$userUUID,$avatar]);
    echo json_encode(["status"=>"success","message"=>"Avatar changed successfully"]);


}else
{
    echo json_encode(["status"=>"error","message"=>"Data not properly given"]);

}
?>