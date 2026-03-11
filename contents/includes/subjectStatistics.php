

<?php
header('Content-type: application/json');

$db = new inquizitiveDB();
$userUUID = $_SESSION['userUUID'];
$subjectUUID = $_POST['subjectUUID'];
$result = $db->Query("CALL GetTotalScorePerSubjectByUserUUID(?,?)" , [$userUUID,$subjectUUID]);
$dataArray = [['Date','Price']];

if(mysqli_num_rows($result) > 0 )
{
    $arr =[$row['completionDate'],$row['score']];
    array_push($dataArray,$arr);
}else
{
    $arr =[date("h:i:s"),0];
    array_push($dataArray,$arr);
}
  
echo json_encode($dataArray);
  ?>

