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

/* Optional filters */
$search = "";
$status = "all";

$stmt = $conn->prepare("CALL GetSubjectsAndStats(?, ?)");
$stmt->bind_param("ss", $search, $status);
$stmt->execute();

$subjects = [];
$stats = ["total" => 0, "active" => 0, "disabled" => 0];

/* RESULT SET 1: subjects */
if ($res1 = $stmt->get_result()) {
  while ($row = $res1->fetch_assoc()) {
    $subjects[] = $row;
  }
  $res1->free();
}

/* RESULT SET 2: stats */
if ($stmt->more_results() && $stmt->next_result()) {
  if ($res2 = $stmt->get_result()) {
    $stats = $res2->fetch_assoc() ?: $stats;
    $res2->free();
  }
}

/* Flush anything else */
while ($stmt->more_results() && $stmt->next_result()) {
  if ($junk = $stmt->get_result()) $junk->free();
}

$stmt->close();


?>

<div id="adminDashboard" class="container-fluid py-4">

  <!-- Header -->
  <div class="sm-header mb-4">
    <div class="sm-header__left">
      <h3 class="sm-title">Subject Management</h3>
      <p class="sm-subtitle mb-0">Create, view, and manage subjects</p>
    </div>

    <div class="sm-header__right">
      <button type="button" class="btn btn-sm btn-outline-secondary" id="smExportBtn">Export</button>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="sm-card sm-stat">
        <div class="sm-stat__label">Total Subjects</div>
        <div class="sm-stat__value" id="smStatTotal"><?= (int)$stats["total"] ?></div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="sm-card sm-stat">
        <div class="sm-stat__label">Active Subjects</div>
        <div class="sm-stat__value" id="smStatActive"><?= (int)$stats["active"] ?></div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="sm-card sm-stat">
        <div class="sm-stat__label">Disabled Subjects</div>
        <div class="sm-stat__value" id="smStatDisabled"><?= (int)$stats["disabled"] ?></div>
      </div>
    </div>
  </div>

  <!-- Main -->
  <div class="row g-4 sm-eq">

    <!-- Left: Add Subject -->
    <div class="col-12 col-lg-4 sm-eq__col">
      <div class="sm-card sm-panel h-100 sm-eq__card">
        <div class="sm-panel__head">
          <div>
            <h5 class="mb-0">Add Subject</h5>
            <div class="text-muted small">Create a new subject</div>
          </div>
          <span class="badge sm-badge">Subject</span>
        </div>

        <div class="sm-panel__body">
          <form id="smAddSubjectForm" class="row g-3" autocomplete="off" method="post">

            <div class="col-12">
              <label class="form-label" for="smFirstName">Subject Name</label>
              <input id="smFirstName" type="text" class="form-control" name="subjectName" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="smLastName">Subject Description</label>
              <input id="smLastName" type="text" class="form-control" name="subjectDescription" required>
            </div>

            <div class="col-12 d-grid mt-1">
              <button type="submit" class="btn btn-primary" id="smCreateBtn">
                Create Subject
              </button>
            </div>


          </form>
        </div>
      </div>
    </div>

    <!-- Right: Subjects List -->
    <div class="col-12 col-lg-8 sm-eq__col">
      <div class="sm-card sm-panel h-100 sm-eq__card d-flex flex-column">

        <div class="sm-panel__head">
          <div>
            <h5 class="mb-0">Subjects</h5>
            <div class="text-muted small">Search and manage existing subjects</div>
          </div>

          <div class="sm-controls">
            <input
              id="smSearch"
              class="form-control form-control-sm"
              placeholder="Search subject name..."
            >

            <select id="lsStatus" class="form-select form-select-sm">
              <option value="all">All statuses</option>
              <option value="active">Active</option>
              <option value="disabled">Disabled</option>
            </select>

            <button class="btn btn-sm btn-outline-secondary" type="button" id="smRefreshBtn">
              Refresh
            </button>
          </div>
        </div>

        <!-- Scroll region -->
        <div class="sm-panel__scroll flex-grow-1">
          <div class="sm-tableWrap">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Subject</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>

              <tbody id="smSubjectsTbody">
                <!-- JS/PHP will inject rows here -->
                <?php if (count($subjects) === 0): ?>
                  <tr>
                    <td colspan="4" class="text-muted py-4 text-center">
                      No Subjects found.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($subjects as $u): ?>
                    <?php
                      $subjectUUID = $u["subjectUUID"];
                      $subjectTitle = $u["subjectTitle"];
                      $subjectDescription = $u["subjectDescription"];
                      $isDisabled = (int)$u["isDisabled"];
                    ?>
                    <tr>
                      <td>
                            <div class="sm-subject__name">
                              <?= htmlspecialchars($subjectTitle) ?>
                            </div>
                            <div class="sm-subject__id">
                              ID: <?= htmlspecialchars($subjectUUID) ?>
                            </div>
                          </div>
                        </div>
                      </td>

                      <td class="text-muted"><?= htmlspecialchars($subjectDescription) ?></td>

                      <td>
                        <?php if ($isDisabled): ?>
                          <span class="badge sm-badge-danger">Disabled</span>
                        <?php else: ?>
                          <span class="badge sm-badge-success">Active</span>
                        <?php endif; ?>
                      </td>

                      <td class="text-end">
                        <div class="sm-actions">
                          <!-- Edit Button -->
                          <button class="btn btn-sm btn-outline-primary">Edit</button>
                          <!-- Disable/Enable Button -->
                          <button class="btn btn-sm btn-outline-warning smToggleDisableBtn" data-subjectuuid="<?= htmlspecialchars($subjectUUID) ?>" data-disabled="<?= $isDisabled?>">
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

<!-- SUBJECT MANAGEMENT: CREATE SUBJECT -->
<script>
document.addEventListener("submit", async (e) => {
  if (e.target.id !== "smAddSubjectForm") return;

  e.preventDefault();

  const form = e.target;
  const fd = new FormData(form);

  const btn = document.getElementById("smCreateBtn");
  if (btn) btn.disabled = true;

  try {
    const res = await fetch("./create-subject", {
      method: "POST",
      body: fd,
      headers: { "X-Requested-With": "XMLHttpRequest" }
    });

    const data = await res.json();

    if (!data.ok) {
      await Swal.fire({
        icon: "error",
        title: "Could not create subject",
        text: data.message || "Please try again."
      });
      return;
    }

    await Swal.fire({
      icon: "success",
      title: "Subject created",
      text: data.message || "Success!"
    });

    form.reset();

    if (typeof loadSubjects === "function") {
      loadSubjects();
    } else {
      location.reload();
    }

  } catch (err) {
    console.error(err);
    await Swal.fire({
      icon: "error",
      title: "Server error",
      text: "Something went wrong. Check console."
    });
  } finally {
    if (btn) btn.disabled = false;
  }
});
</script>

<!-- SUBJECT MANAGEMENT: ENABLE / DISABLE SUBJECT -->
<script>
document.addEventListener("click", async (e) => {
  const btn = e.target.closest(".smToggleDisableBtn");
  if (!btn) return;

  const subjectUUID = btn.dataset.subjectuuid;
  const currentlyDisabled = btn.dataset.disabled === "1";
  const newDisabled = currentlyDisabled ? 0 : 1;

  const confirm = await Swal.fire({
    icon: "warning",
    title: newDisabled ? "Disable subject?" : "Enable subject?",
    text: newDisabled
      ? "This will hide/disable the subject for normal use."
      : "This will make the subject active again.",
    showCancelButton: true,
    confirmButtonText: newDisabled ? "Disable" : "Enable"
  });

  if (!confirm.isConfirmed) return;

  btn.disabled = true;

  try {
    const fd = new FormData();
    fd.append("subjectUUID", subjectUUID);
    fd.append("isDisabled", String(newDisabled));

    const res = await fetch("./set-subject-disabled", {
      method: "POST",
      body: fd,
      headers: { "X-Requested-With": "XMLHttpRequest" }
    });

    const data = await res.json();

    if (!data.ok) {
      await Swal.fire({
        icon: "error",
        title: "Update failed",
        text: data.message || "Error"
      });
      return;
    }

    btn.dataset.disabled = String(newDisabled);
    btn.textContent = newDisabled ? "Enable" : "Disable";

    const row = btn.closest("tr");
    const badge = row.querySelector("td:nth-child(3) .badge");
    if (badge) {
      badge.className = "badge " + (newDisabled ? "sm-badge-danger" : "sm-badge-success");
      badge.textContent = newDisabled ? "Disabled" : "Active";
    }

    await Swal.fire({
      icon: "success",
      title: "Updated",
      text: data.message || "Done."
    });

    if (typeof loadSubjects === "function") loadSubjects();

  } catch (err) {
    console.error(err);
    await Swal.fire({
      icon: "error",
      title: "Server error",
      text: "Something went wrong."
    });
  } finally {
    btn.disabled = false;
  }
});
</script>