<?php
echo var_dump($_POST);

$db = new inquizitiveDB();
$subjectUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['subjectUUID'])); 
$subjectTitle = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['subjectName'])); 
$subjectDescription = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['subjectDescription'])); 
$isDisabled =0;
if(isset($_POST['isDisabled']))
{
    $isDisabled = 1; 

}
  if ($result = $db->Query("CALL EditSubject(?,?,?,?)", [$subjectUUID,$subjectTitle,$subjectDescription,$isDisabled])) {

      $rowCount = mysqli_num_rows($result);
      if ($rowCount > 0) {
          $row = mysqli_fetch_assoc($result);
      }
  }
?>