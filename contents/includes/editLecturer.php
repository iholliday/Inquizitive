<?php
$db = new inquizitiveDB();
$userUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['userUUID'])); 
  if ($result = $db->Query("CALL GetUserByUserUUID(?)", [$userUUID])) {

      $rowCount = mysqli_num_rows($result);
      if ($rowCount > 0) {
          $row = mysqli_fetch_assoc($result);
      }
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/editSubject.css" />
</head>
<body>
    <section id="edit-subject">
        <div id="edit-subject-wrapper" class="shadow">
          <form id="smAddSubjectForm" class="row g-3 shadow" autocomplete="off" method="post">

            <div class="col-12 hidden">
              <label class="form-label txt-center" for="smUserUUID">User UUID</label>
              <input id="smUserUUID" type="text" class="form-control" name="smUserUUID" value ="<?= $userUUID?>" required>
            </div>

            <div class="col-12">
              <label class="form-label txt-center" for="userEmail">User Email</label>
              <input id="userEmail" type="email" class="form-control" name="userEmail" value ="<?= htmlspecialchars($row['email'])?>" required>
            </div>
            <div class="col-12">
              <label class="form-label txt-center" for="smFirstName">First Name</label>
              <input id="smFirstName" type="text" class="form-control" name="firstName" value ="<?= htmlspecialchars($row['firstName'])?>"  required>
            </div>
            <div class="col-12">
              <label class="form-label txt-center" for="smLastName">Last Name</label>
              <input id="smLastName" type="text" class="form-control" name="lastName" value ="<?= htmlspecialchars($row['lastName'])?>"  required>
            </div>

            <div class="col-12">
              <label class="form-label txt-center" for="password">Password</label>
              <input id="password" type="password" class="form-control" name="password" value ="" placeholder="Leave empty to keep unchanged..." required>
            </div>
            <div class="col-12">
            <label class="form-label txt-center" for="accessLevel">Access Level</label>
            <select class="form-select form-control" aria-label="Default select example" name="accessLevel" id="accessLevel">
                <option selected value ="<?= htmlspecialchars($row['accessLevel'])?>"><?= htmlspecialchars($row['accessLevel'])?></option>
                <option value="USER">USER</option>
                <option value="LECTURER">LECTURER</option>
            </select>
            </div>
            <div class="col-12">
                <div class="form-check isDisabledWrapper">
                    <input class="form-check-input" name ="isDisabled" type="checkbox" value ="<?= htmlspecialchars($row['isDisabled'])?>"  id="isDisabled" <?php if(htmlspecialchars($row['isDisabled']) == 1){echo "checked";};?>>
                    <label class="form-check-label" for="isDisabled">
                        Disable Account?
                    </label>
                </div>
            </div>
            <div class="col-12">
                <div id="submitBtnWrapper">
                    <button type="button" class="btn btn-primary form-control" id="smEditBtn">
                        Edit Account
                    </button>
                </div>
            </div>


          </form>
        </div>
    </section>
    <script>
        $("#smEditBtn").ready(function(){
            $("#smEditBtn").click(function(e){
                    $.ajax({
                    url: './editLecturer', 
                    type: 'POST', 
                    data: $("#smAddSubjectForm").serialize(),
                    success: function(responseOne) {
                        console.log('Success:', responseOne);
                    $.ajax({
                        url: './lecturer-management', 
                        type: 'GET', 
                        success: function(response) {
                            //console.log('Success:', response);
                            $("#content").html(response);
                        },
                        error: function(xhr, status, error) {
                            //console.log('Error:', error);
                        }
                    });
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            })
        })
    </script>

</body>
</html>