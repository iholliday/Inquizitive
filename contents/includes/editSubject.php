<?php
$db = new inquizitiveDB();
$subjectUUID = htmlspecialchars(mysqli_real_escape_string($db->connect,$_POST['subjectUUID'])); 
  if ($result = $db->Query("CALL GetSubject(?)", [$subjectUUID])) {

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
              <label class="form-label txt-center" for="smSubjectUUID">Subject UUID</label>
              <input id="smSubjectUUID" type="text" class="form-control" name="subjectUUID" value ="<?= $subjectUUID?>" required>
            </div>

            <div class="col-12">
              <label class="form-label txt-center" for="smFirstName">Subject Name</label>
              <input id="smFirstName" type="text" class="form-control" name="subjectName" value ="<?= htmlspecialchars($row['subjectTitle'])?>" required>
            </div>

            <div class="col-12">
              <label class="form-label txt-center" for="smLastName">Subject Description</label>
              <input id="smLastName" type="text" class="form-control" name="subjectDescription" value ="<?= htmlspecialchars($row['subjectDescription'])?>"  required>
            </div>

            <div class="col-12">
              <label class="form-label txt-center" for="smAuthord">Author</label>
              <input id="smAuthord" type="text" class="form-control" name="author" value ="<?= htmlspecialchars($row['authorUUID'])?>"  disabled>
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
                        Edit Subject
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
                    url: './editSubject', 
                    type: 'POST', 
                    data: $("#smAddSubjectForm").serialize(),
                    success: function(responseOne) {
                        console.log('Success:', responseOne);
                    $.ajax({
                        url: './subject-management', 
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