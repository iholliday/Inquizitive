<?php

// include __DIR__ . "/../../php/blockDirectAccess.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

if (!$isAjax && !$isEmbeddedInDashboard) {
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

$subjects = [];
if($subRes = $db->Query("CALL GetAllSubjects();", [])){
  while ($row = mysqli_fetch_assoc($subRes)){
    $subjects[] = $row;
  }

  mysqli_free_result($subRes);
  while(mysqli_more_results($conn) && mysqli_next_result($conn)){
    $junk = mysqli_store_result($conn);
    if($junk) mysqli_free_result($junk);
  }
}


?>

<div id="lecturerDashboard" class="container-fluid py-4">

  <!-- Header -->
  <div class="sdm-header mb-4">
    <div class="sdm-header__left">
      <h3 class="sdm-title">Quiz Management</h3>
      <p class="sdm-subtitle mb-0">Create, view, and manage quizzes</p>
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
          <form id="tmAddQuizForm" class="row g-3" autocomplete="off" method="post">

             <div class="col-12">
              <label class="form-label" for="tmQuizName">Quiz Name</label>
              <input id="tmQuizName" type="text" class="form-control" name="quizName" required>
            </div>

          <div class="col-12">
            <select class="form-select" name="subjectUUID" id="subjectUUID">
              <option value="">Select a subject...</option>
              <?php foreach ($subjects as $s): ?>
                <option value="<?= htmlspecialchars($s['subjectUUID'], ENT_QUOTES, 'UTF-8') ?>">
                  <?= htmlspecialchars($s['subjectTitle'], ENT_QUOTES, 'UTF-8') ?>
                </option>
                <?php endforeach; ?>
            </select>
          </div>
              
              <!-- <label class="form-label" for="tmSubject">Subject</label>
              <input id="tmSubject" type="text" class="form-control" name="subject" required> -->
            
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
            <div class="text-muted small">Search and manage existing quizzes</div>
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
                      $isDisabled = $u["isDisabled"];
                      $subjectTitle = $u["subjectTitle"];
                      $subjectUUID = $u["subjectUUID"];
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
                        <?php if ($isDisabled): ?>
                          <span class="badge tm-badge-danger">Disabled</span>
                        <?php else: ?>
                          <span class="badge tm-badge-success">Active</span>
                        <?php endif; ?>
                      </td>

                      <td class="text-end">
                        <div class="tm-actions">
                          <!-- Edit Button -->
                          <button 
                            class="btn btn-tm btn-outline-primary tmEditQuizBtn"
                            data-quizuuid="<?= htmlspecialchars($quizUUID) ?>"
                            data-quizname="<?= htmlspecialchars($quizName) ?>"
                            data-subjectuuid="<?= htmlspecialchars($subjectUUID) ?>">
                            Edit
                          </button>
                          <!-- Disable/Enable Button -->
                          <button class="btn btn-tm btn-outline-warning tmToggleDisableBtn" data-quizuuid="<?= htmlspecialchars($quizUUID) ?>" data-disabled="<?= $isDisabled?>">
                            <?= $isDisabled ? "Enable" : "Disable" ?>
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
const tmSubjects = <?= json_encode($subjects) ?>;

document.addEventListener("click", async (e) => {

  const btn = e.target.closest(".tmEditQuizBtn");
  if (!btn) return;

  const quizUUID = btn.dataset.quizuuid;
  const quizName = btn.dataset.quizname;
  const subjectUUID = btn.dataset.subjectuuid;

  // Build subject dropdown
  let subjectOptions = `<option value="">Select Subject</option>`;
  tmSubjects.forEach(s => {
    subjectOptions += `
      <option value="${s.subjectUUID}" 
        ${s.subjectUUID === subjectUUID ? "selected" : ""}>
        ${s.subjectTitle}
      </option>`;
  });

  const { value: formValues } = await Swal.fire({
    title: "Edit Quiz",

    html: `
      <div class="text-start">

        <div class="mb-3">
          <label class="form-label fw-semibold">Quiz Name</label>
          <input 
            id="swalQuizName" 
            class="form-control" 
            value="${quizName}"
            placeholder="Enter quiz name">
        </div>

        <div class="mb-2">
          <label class="form-label fw-semibold">Subject</label>
          <select id="swalSubjectUUID" class="form-select">
            ${subjectOptions}
          </select>
        </div>

      </div>
    `,
    focusConfirm: false,
    showCancelButton: true,
    confirmButtonText: "Save Changes",

    preConfirm: () => {

      const quizName = document.getElementById("swalQuizName").value.trim();
      const subjectUUID = document.getElementById("swalSubjectUUID").value;

      if (!quizName || !subjectUUID) {
        Swal.showValidationMessage("All fields are required");
        return false;
      }

      return {
        quizUUID: quizUUID,
        quizName: quizName,
        subjectUUID: subjectUUID
      };
    }
  });

  if (!formValues) return;

  try {

    const fd = new FormData();
    fd.append("quizUUID", formValues.quizUUID);
    fd.append("quizName", formValues.quizName);
    fd.append("subjectUUID", formValues.subjectUUID);

    const res = await fetch("./edit-quiz", {
      method: "POST",
      body: fd,
      headers: { "X-Requested-With": "XMLHttpRequest" }
    });

    const data = await res.json();

    if (!data.ok) {
      await Swal.fire("Error", data.message || "Failed to update quiz", "error");
      return;
    }

    await Swal.fire("Updated!", "Quiz updated successfully.", "success");

    location.reload();

  } catch (err) {
    console.error(err);
    Swal.fire("Error", "Server error occurred.", "error");
  }

});

</script>

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
    data: formData,
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

$(document).on("click", "#backBtn", function(){

  $.ajax({
    url: "./test-management",
    type: "POST",
    success: function(response){
      $("#content").html(response);
    }
  });

});
  

</script>

<script>
    document.addEventListener("click", async (e) => {

    // Check if the clicked element is a disable/enable button
    const btn = e.target.closest(".tmToggleDisableBtn");
    if (!btn) return;

    // Retrieve user UUID and current disabled state from data attribute
    const quizuuid = btn.dataset.quizuuid;
    const currentlyDisabled = btn.dataset.disabled === "1";
    
    // Determine new state
    const newDisabled = currentlyDisabled ? 0 : 1;

    // Confirmation modal before changing anything
    const confirm = await Swal.fire({
      icon: "warning",
      title: newDisabled ? "Disable user?" : "Enable user?",
      text: newDisabled
        ? "This will prevent the user from accessing the system."
        : "This will allow the user to access the system again.",
      showCancelButton: true,
      confirmButtonText: newDisabled ? "Disable" : "Enable"
    });

    if (!confirm.isConfirmed) return;

    // Prevent duplicate requests
    btn.disabled = true;

    try {
      const fd = new FormData();
      fd.append("quizuuid", quizuuid);
      fd.append("isDisabled", String(newDisabled));

      // Sending AJAX request to update quiz status
      const res = await fetch("./set-quiz-disabled", {
        method: "POST",
        body: fd,
        headers: { "X-Requested-With": "XMLHttpRequest" }
      });

      const data = await res.json();

      if (!data.ok) {
        await Swal.fire({ icon: "error", title: "Update failed", text: data.message || "Error" });
        return;
      }

      // Update button label + state
      btn.dataset.disabled = String(newDisabled);
      btn.textContent = newDisabled ? "Enable" : "Disable";

      // Update status badge visually in the table row
      const row = btn.closest("tr");
      const badge = row.querySelector("td:nth-child(3) .badge");
      if (badge) {
        badge.className = "badge " + (newDisabled ? "sdm-badge-danger" : "sdm-badge-success");
        badge.textContent = newDisabled ? "Disabled" : "Active";
      }

      await Swal.fire({ icon: "success", title: "Updated", text: data.message || "Done." });

    } catch (err) {
      console.error(err);
      await Swal.fire({ icon: "error", title: "Server error", text: "Something went wrong." });
    } finally {
      // Re-enable button regardless of success/failure
      btn.disabled = false;
    }
  });

  </script>

  