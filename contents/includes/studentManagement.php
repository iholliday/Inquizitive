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


$stmt = $conn->prepare("CALL GetStudents()");

$stmt->execute();

if ($res1 = $stmt->get_result()) {
  while ($row = $res1->fetch_assoc()) {
    $students[] = $row;
  }
  $res1->free();
}
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

    <!-- Left: Add Student -->
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
        <div class="sdm-panel__scroll flex-grow-1">
          <div class="sdm-tableWrap">
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
                <?php if (count($students) === 0): ?>
                  <tr>
                    <td colspan="4" class="text-muted py-4 text-center">
                      No students found.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($students as $s): ?>
                    <?php
                      $userUUID = $s["userUUID"];
                      $firstName = $s["firstName"];
                      $lastName = $s["lastName"];
                      $email = $s["email"];
                      $isDisabled = (int)$s["isDisabled"];
                    ?>
                    <tr>
                      <td>
                          <div class="sdm-subject__name"><?= htmlspecialchars($firstName) ?></div>
                          <div class="sdm-subject__id">ID: <?= htmlspecialchars($userUUID) ?></div>
                      </td>

                      <td class="text-muted"><?= htmlspecialchars($email) ?></td>

                      <td>
                        <?php if ($isDisabled): ?>
                          <span class="badge sdm-badge-danger">Disabled</span>
                        <?php else: ?>
                          <span class="badge sdm-badge-success">Active</span>
                        <?php endif; ?>
                      </td>

                      <td class="text-end">
                        <div class="sdm-actions">
                          <!-- Edit Button -->
                          <button class="btn btn-sdm btn-outline-primary">Edit</button>
                          <!-- Disable/Enable Button -->
                          <button class="btn btn-sdm btn-outline-warning smToggleDisableBtn" data-userUUID="<?= htmlspecialchars($userUUID) ?>" data-disabled="<?= $isDisabled?>">
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
  $(".sdm-quizLink").ready(function(){
    $(".sdm-quizLink").click(function(){
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

</script>

<script>
document.addEventListener("submit", async (e) => {
  if (e.target.id !== "sdmAddStudentForm") return;

  e.preventDefault();

  const form = e.target;
  const fd = new FormData(form);

  // Disable button while submitting
  const btn = document.getElementById("sdmCreateBtn");
  if (btn) btn.disabled = true;

  try {
    const res = await fetch("./add-student", {
      method: "POST",
      body: fd,
      headers: { "X-Requested-With": "XMLHttpRequest" }
    });

    const data = await res.json();

    if (!data.ok) {
      await Swal.fire({
        icon: "error",
        title: "Could not create student",
        text: data.message || "Please try again."
      });
      return;
    }

    await Swal.fire({
      icon: "success",
      title: "Student created",
      text: data.message || "Success!"
    });

    form.reset();

  } catch (err) {
    await Swal.fire({
      icon: "error",
      title: "Server error",
      text: "Something went wrong. Check console."
    });
    console.error(err);
  } finally {
    if (btn) btn.disabled = false;
  }
});
</script>

<!-- Disabling users -->
  <script>
    document.addEventListener("click", async (e) => {

    // Check if the clicked element is a disable/enable button
    const btn = e.target.closest(".smToggleDisableBtn");
    if (!btn) return;

    // Retrieve user UUID and current disabled state from data attribute
    const userUUID = btn.dataset.useruuid;
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

    // Stop execution if Admin cancels
    if (!confirm.isConfirmed) return;

    // Prevent duplicate requests
    btn.disabled = true;

    try {
      const fd = new FormData();
      fd.append("userUUID", userUUID);
      fd.append("isDisabled", String(newDisabled));

      // Sending AJAX request to update user status
      const res = await fetch("./set-user-disabled", {
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