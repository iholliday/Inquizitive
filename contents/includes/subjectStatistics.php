

<?php
header('Content-type: application/json');

$db = new inquizitiveDB();
$userUUID = $_SESSION['userUUID'];
$subjectUUID = $_POST['subjectUUID'];
$result = $db->Query("CALL GetTotalScorePerSubjectByUserUUID(?,?)" , [$userUUID,$subjectUUID]);
$dataArray = [['Date','Score']];

if(mysqli_num_rows($result) > 0 )
{
    while($row= mysqli_fetch_assoc($result))
    {
        if($row['totalScore'] !== NULL)
        {
            $arr =[htmlspecialchars($row['completionDate']),(int)htmlspecialchars($row['totalScore'])];
            array_push($dataArray,$arr);
        }

    }

}else
{
    $arr =[date("h:i:s"),0];
    array_push($dataArray,$arr);
}
  
echo json_encode($dataArray);
  ?>

