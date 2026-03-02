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

$db = new inquizitiveDB();
$conn = $db->connect;

$search = "";
$status = "all";

$stmt = $conn->prepare("CALL GetQuizzes(?,?)");
$stmt->bind_param("ss", $search, $status);
$stmt->execute();

$quizzes = [];

if ($res1 = $stmt->get_result()){
  while ($row = $res1->fetch_assoc()){
    $quizzes[] = $row;
  
  }
$res1->free();
}
$stmt->close();
?>

<div id="lecturerDashboard" class="container-fluid py-4">

  <!-- Header -->
  <div class="sdm-header mb-4">
    <div class="sdm-header__left">
      <h3 class="sdm-title">Test Management</h3>
      <p class="sdm-subtitle mb-0">Create, view, and manage tests</p>
    </div>
  </div>

  <!-- Main -->
  <div class="row g-4 sdm-eq">

    <!-- Left: Add Lecturer -->
    <div class="col-12 col-lg-4 sdm-eq__col">
      <div class="sdm-card sdm-panel h-100 sdm-eq__card">
        <div class="sdm-panel__head">
          <div>
            <h5 class="mb-0">Add Quiz</h5>
            <div class="text-muted small">Create a new quiz</div>
          </div>
          <span class="badge sdm-badge">Lecturer</span>
        </div>

        <div class="sdm-panel__body">
          <form id="sdmAddStudentForm" class="row g-3" autocomplete="off" method="post">

             <div class="col-12">
              <label class="form-label" for="tmQuizName">Quiz Name</label>
              <input id="tmQuizName" type="text" class="form-control" name="quizName" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="tmSubject">Subject</label>
              <input id="tmSubject" type="text" class="form-control" name="subject" required>
            </div>

            
            <div class="col-12 d-grid mt-1">
              <button type="submit" class="btn" id="tmCreateBtn">
                Create Quiz
              </button>
            </div> 

             <div class="col-12">
              <div class="sdm-note">
                Questions can be added to the quiz after creation.
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
            <h5 class="mb-0">Tests</h5>
            <div class="text-muted small">Search and manage existing tests</div>
          </div>
        </div>

        <!-- Scroll region -->
               <!-- Scroll region -->
        <div class="tm-panel__scroll flex-grow-1">
          <div class="tm-tableWrap">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Quiz</th>
                  <th>Subject</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>

              <tbody id="tmSubjectsTbody">
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

                      <td class="text-muted"><?= htmlspecialchars($subjectTitle) ?></td>

                      <td>
                        <?php if ($subjectIsDisabled): ?>
                          <span class="badge tm-badge-danger">Disabled</span>
                        <?php else: ?>
                          <span class="badge tm-badge-success">Active</span>
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
          $(".main").html(response);

        }
      })
    })
  })

</script>