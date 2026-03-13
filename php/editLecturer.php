<?php
echo var_dump($_POST);

$db = new inquizitiveDB();
$userUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['smUserUUID'])); 
$firstName = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['firstName'])); 
$lastName = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['lastName'])); 
$email = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['userEmail'])); 
$accessLevel = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['accessLevel'])); 
$isDisabled =0;
if(isset($_POST['isDisabled']))
{
    $isDisabled = 1; 

}

if(isset($_POST['password']) && $_POST['password'] !=="" && $_POST['password'] !== " ")
{
    echo"password set";
$password = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['password'])); 
$hash = password_hash($password, PASSWORD_BCRYPT);
  if ($result = $db->Query("CALL UpdateUserByUserUUID(?,?,?,?,?,?,?)", [$userUUID,$email,$firstName,$lastName,$hash,$accessLevel,$isDisabled])) {

      $rowCount = mysqli_num_rows($result);
      if ($rowCount > 0) {
          $row = mysqli_fetch_assoc($result);
      }
  }
}else
{
    echo"password not set";
      if ($result = $db->Query("CALL UpdateUserByUserUUIDNoPassword(?,?,?,?,?,?)", [$userUUID,$email,$firstName,$lastName,$accessLevel,$isDisabled])) {

      $rowCount = mysqli_num_rows($result);
      if ($rowCount > 0) {
          $row = mysqli_fetch_assoc($result);
      }
  }
}


?>