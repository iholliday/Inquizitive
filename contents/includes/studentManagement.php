<?php

// include __DIR__ . "/../../php/blockDirectAccess.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
  $DASH_INCLUDE = __FILE__;
  require __DIR__ . '/../dashboardNavigation.php';
  exit;
}

require_once __DIR__ . "/../../php/_connect.php";
?>


<div id="lecturerDashboard" class="container-fluid py-4">

  <!-- Header -->
  <div class="sdm-header mb-4">
    <div class="sdm-header__left">
      <h3 class="sdm-title">Student Management</h3>
      <p class="sdm-subtitle mb-0">Create, view, and manage student accounts</p>
    </div>
  </div>

  <!-- Main -->
  <div class="row g-4 sdm-eq">

    <!-- Left: Add Lecturer -->
    <div class="col-12 col-lg-4 sdm-eq__col">
      <div class="sdm-card sdm-panel h-100 sdm-eq__card">
        <div class="sdm-panel__head">
          <div>
            <h5 class="mb-0">Add Student</h5>
            <div class="text-muted small">Create a new student account</div>
          </div>
          <span class="badge sdm-badge">Lecturer</span>
        </div>

        <div class="sdm-panel__body">
          <form id="sdmAddStudentForm" class="row g-3" autocomplete="off" method="post">

            <div class="col-12 col-md-6">
              <label class="form-label" for="sdmFirstName">First name</label>
              <input id="sdmFirstName" type="text" class="form-control" name="firstName" required>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label" for="sdmLastName">Last name</label>
              <input id="sdmLastName" type="text" class="form-control" name="lastName" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="sdmEmail">Email</label>
              <input id="sdmEmail" type="email" class="form-control" name="email" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="sdmPassword">Password</label>
              <input id="sdmPassword" type="password" class="form-control" name="password" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="sdmConfirmPassword">Confirm password</label>
              <input id="sdmConfirmPassword" type="password" class="form-control" name="confirmPassword" required>
            </div>

            <div class="col-12 d-grid mt-1">
              <button type="submit" class="btn" id="sdmCreateBtn">
                Create Student
              </button>
            </div>

            <div class="col-12">
              <div class="sdm-note">
                A welcome email will be sent with login instructions.
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>

    <!-- Right: Student List -->
    <div class="col-12 col-lg-8 sdm-eq__col">
      <div class="sdm-card sdm-panel h-100 sdm-eq__card d-flex flex-column">

        <div class="sdm-panel__head">
          <div>
            <h5 class="mb-0">Students</h5>
            <div class="text-muted small">Search and manage existing student accounts</div>
          </div>
        </div>

     <!-- Scroll region -->
        <div class="sm-panel__scroll flex-grow-1">
          <div class="sm-tableWrap">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>First Name</th>
                  <th>Last Name</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
</div>
  <tbody id="smSubjectsTbody">
                <!-- JS/PHP will inject rows here -->
                <?php if (count($quizzes) === 0): ?>
                  <tr>
                    <td colspan="4" class="text-muted py-4 text-center">
                      No quizzes found.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($quizzes as $u): ?>
                    <?php
                      $quizUUID = $u["quizUUID"];
                      $quizName = $u["quizName"];
                      $subjectTitle = $u["subjectTitle"];
                      $subjectUUID = $u["subjectUUID"];
                      $subjectIsDisabled = (int)$u["subjectIsDisabled"];
                    ?>
                    <tr>
                      <td>
                        <a class="tm-quizLink" id="<?= urlencode($quizUUID) ?>"
                          data-quizuuid="<?= htmlspecialchars($quizUUID) ?>">
                          <div class="tm-subject__name"><?= htmlspecialchars($quizName) ?></div>
                          <div class="tm-subject__id">ID: <?= htmlspecialchars($quizUUID) ?></div>
                        </a>
                      </td>

                      <td class="text-muted"><?= htmlspecialchars($studentTitle) ?></td>

                      <td>
                        <?php if ($studentIsDisabled): ?>
                          <span class="badge sm-badge-danger">Disabled</span>
                        <?php else: ?>
                          <span class="badge sm-badge-success">Active</span>
                        <?php endif; ?>
                      </td>

                      <td class="text-end">
                        <div class="tm-actions">
                          <!-- Edit Button -->
                          <button class="btn btn-tm btn-outline-primary">Edit</button>
                          <!-- Disable/Enable Button -->
                          <button class="btn btn-tm btn-outline-warning tmToggleDisableBtn" data-subjectuuid="<?= htmlspecialchars($subjectUUID) ?>" data-disabled="<?= $subjectIsDisabled?>">
                            <?= $subjectIsDisabled ? "Enable" : "Disable" ?>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>

              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script>
  $(".tm-quizLink").ready(function(){
    $(".tm-quizLink").click(function(){
      $.ajax({
        url: "./test-management/editor",
        type: "POST",
        data: {quizGrab:$(this).attr("id").toString()}, 

        success: function(response){
          $("#content").html(response);

        }
      })
    })
  })

$("#tmAddQuizForm").on("submit", function(e){
  e.preventDefault(); // VERY IMPORTANT

  const formData = $(this).serialize();

  $.ajax({
    url: "./create-test",
    method: "POST",
    dataType: "json",
    data: formData, // ✅ send serialized form
    success: function(res){
      if(res.ok){
        Swal.fire("Created!", "Quiz has been created.", "success");
        location.reload();
      } else {
        Swal.fire("Error", res.error || "Failed to create quiz.", "error");
      }
    },
    error: function(xhr){
      Swal.fire("Error", xhr.responseText || "Failed to create quiz.", "error");
    }
  });
});
  

</script>