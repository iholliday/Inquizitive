<?php
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

/* Optional filters (safe defaults) */
$search = "";
$status = "all";

/* Call routine */
$searchEsc = mysqli_real_escape_string($conn, $search);
$statusEsc = mysqli_real_escape_string($conn, $status);

$sql = "CALL GetLecturersAndStats('$searchEsc', '$statusEsc')";

$lecturers = [];
$stats = ["total" => 0, "active" => 0, "disabled" => 0];

if (mysqli_multi_query($conn, $sql)) {

  /* RESULT SET 1: lecturers */
  $result = mysqli_store_result($conn);
  if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
      $lecturers[] = $row;
    }
    mysqli_free_result($result);
  }

  /* RESULT SET 2: stats */
  if (mysqli_more_results($conn)) {
    mysqli_next_result($conn);
    $result2 = mysqli_store_result($conn);
    if ($result2) {
      $stats = mysqli_fetch_assoc($result2) ?: $stats;
      mysqli_free_result($result2);
    }
  }

  /* Clear remaining results */
  while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
    $junk = mysqli_store_result($conn);
    if ($junk) mysqli_free_result($junk);
  }
}
?>

<div id="admLecturerManagement" class="container-fluid py-4">

  <!-- Header -->
  <div class="lm-header mb-4">
    <div class="lm-header__left">
      <h3 class="lm-title">Lecturer Management</h3>
      <p class="lm-subtitle mb-0">Create, view, and manage lecturer accounts</p>
    </div>

    <div class="lm-header__right">
      <button type="button" class="btn btn-sm btn-outline-secondary" id="lmExportBtn">Export</button>
    </div>
  </div>

  <!-- Stats -->
  <div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="lm-card lm-stat">
        <div class="lm-stat__label">Total Lecturers</div>
        <div class="lm-stat__value" id="lmStatTotal"><?= (int)$stats["total"] ?></div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="lm-card lm-stat">
        <div class="lm-stat__label">Active</div>
        <div class="lm-stat__value" id="lmStatActive"><?= (int)$stats["active"] ?></div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="lm-card lm-stat">
        <div class="lm-stat__label">Disabled</div>
        <div class="lm-stat__value" id="lmStatDisabled"><?= (int)$stats["disabled"] ?></div>
      </div>
    </div>
  </div>

  <!-- Main -->
  <div class="row g-4 lm-eq">

    <!-- Left: Add Lecturer -->
    <div class="col-12 col-lg-4 lm-eq__col">
      <div class="lm-card lm-panel h-100 lm-eq__card">
        <div class="lm-panel__head">
          <div>
            <h5 class="mb-0">Add Lecturer</h5>
            <div class="text-muted small">Create a new lecturer account</div>
          </div>
          <span class="badge lm-badge">Admin</span>
        </div>

        <div class="lm-panel__body">
          <form id="lmAddLecturerForm" class="row g-3" autocomplete="off" method="post">

            <div class="col-12 col-md-6">
              <label class="form-label" for="lmFirstName">First name</label>
              <input id="lmFirstName" type="text" class="form-control" name="firstName" required>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label" for="lmLastName">Last name</label>
              <input id="lmLastName" type="text" class="form-control" name="lastName" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="lmEmail">Email</label>
              <input id="lmEmail" type="email" class="form-control" name="email" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="lmPassword">Password</label>
              <input id="lmPassword" type="password" class="form-control" name="password" required>
            </div>

            <div class="col-12">
              <label class="form-label" for="lmConfirmPassword">Confirm password</label>
              <input id="lmConfirmPassword" type="password" class="form-control" name="confirmPassword" required>
            </div>

            <div class="col-12 d-grid mt-1">
              <button type="submit" class="btn btn-primary" id="lmCreateBtn">
                Create lecturer
              </button>
            </div>

            <div class="col-12">
              <div class="lm-note">
                A welcome email will be sent with login instructions.
              </div>
            </div>

          </form>
        </div>
      </div>
    </div>

    <!-- Right: Lecturers List -->
    <div class="col-12 col-lg-8 lm-eq__col">
      <div class="lm-card lm-panel h-100 lm-eq__card d-flex flex-column">

        <div class="lm-panel__head">
          <div>
            <h5 class="mb-0">Lecturers</h5>
            <div class="text-muted small">Search and manage existing accounts</div>
          </div>

          <div class="lm-controls">
            <input
              id="lmSearch"
              class="form-control form-control-sm"
              placeholder="Search name or email..."
            >

            <select id="lmStatus" class="form-select form-select-sm">
              <option value="all">All statuses</option>
              <option value="active">Active</option>
              <option value="disabled">Disabled</option>
            </select>

            <button class="btn btn-sm btn-outline-secondary" type="button" id="lmRefreshBtn">
              Refresh
            </button>
          </div>
        </div>

        <!-- Scroll region -->
        <div class="lm-panel__scroll flex-grow-1">
          <div class="lm-tableWrap">
            <table class="table table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Lecturer</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>

              <tbody id="lmLecturersTbody">
                <!-- JS/PHP will inject rows here -->
                <?php if (count($lecturers) === 0): ?>
                  <tr>
                    <td colspan="4" class="text-muted py-4 text-center">
                      No lecturers found.
                    </td>
                  </tr>
                <?php else: ?>
                  <?php foreach ($lecturers as $u): ?>
                    <?php
                      $first = $u["firstName"];
                      $last = $u["lastName"];
                      $email = $u["email"];
                      $uuid = $u["userUUID"];
                      $isDisabled = (int)$u["isDisabled"];

                      $initials = strtoupper($first[0] . $last[0]);
                    ?>
                    <tr data-useruuid="<?= htmlspecialchars($uuid) ?>">
                      <td>
                        <div class="lm-person">
                          <div class="lm-avatar"><?= htmlspecialchars($initials) ?></div>
                          <div class="lm-person__meta">
                            <div class="lm-person__name">
                              <?= htmlspecialchars("$first $last") ?>
                            </div>
                            <div class="lm-person__id">
                              ID: <?= htmlspecialchars($uuid) ?>
                            </div>
                          </div>
                        </div>
                      </td>

                      <td class="text-muted"><?= htmlspecialchars($email) ?></td>

                      <td>
                        <?php if ($isDisabled): ?>
                          <span class="badge lm-badge-danger">Disabled</span>
                        <?php else: ?>
                          <span class="badge lm-badge-success">Active</span>
                        <?php endif; ?>
                      </td>

                      <td class="text-end">
                        <div class="lm-actions">
                          <button class="btn btn-sm btn-outline-primary">Edit</button>
                          <button class="btn btn-sm btn-outline-warning">
                            <?= $isDisabled ? "Enable" : "Disable" ?>
                          </button>
                          <button class="btn btn-sm btn-outline-danger">Delete</button>
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
document.addEventListener("submit", async (e) => {
  if (e.target.id !== "lmAddLecturerForm") return;

  e.preventDefault();

  const form = e.target;
  const fd = new FormData(form);

  // Disable button while submitting
  const btn = document.getElementById("lmCreateBtn");
  if (btn) btn.disabled = true;

  try {
    const res = await fetch("/inquizitive/php/addLecturer.php", {
      method: "POST",
      body: fd,
      headers: { "X-Requested-With": "XMLHttpRequest" }
    });

    const data = await res.json();

    if (!data.ok) {
      await Swal.fire({
        icon: "error",
        title: "Could not create lecturer",
        text: data.message || "Please try again."
      });
      return;
    }

    await Swal.fire({
      icon: "success",
      title: "Lecturer created",
      text: data.message || "Success!"
    });

    form.reset();

    if (typeof loadLecturers === "function") {
      loadLecturers();
    } else {
      location.reload();
    }

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
