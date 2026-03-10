

<?php
header('Content-type: application/json');

$db = new inquizitiveDB();
$userUUID = $_SESSION['userUUID'];
$subjectUUID = $_POST['subjectUUID'];
$result = $db-Query("CALL GetTotalScorePerSubjectByUserUUID(?,?)" , [$userUUID,$subjectUUID]);
$dataArray = [['Price','Size']];
if(mysqli_num_rows($result) > 0 )
{
    array_push($dataArray,$row['totalScore'])
}

echo json_encode([['Price', 'Size'],
  [50,7],[60,8],[70,8],[80,9],[90,9],
  [100,9],[110,10],[120,11],
  [130,14],[140,14],[150,15]]);
  ?>

