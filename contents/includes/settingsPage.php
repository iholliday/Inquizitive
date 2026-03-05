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

?>

<div id="adminDashboard" class="container-fluid py-4">
  <div class="row g-4 sm-eq">

    <!-- Left: Settings Section -->
    <div class="col-12 col-lg-6 sm-eq__col">
      <div class="sm-card sm-panel h-100 sm-eq__card">
        <div class="sm-panel__head">
          <div>
            <h5 class="mb-0">Settings</h5>
            <div class="text-muted small">Manage your account</div>
          </div>
        </div>

        <div class="sm-panel__body">


          <!-- Email -->
          <form id="formUpdateEmail" class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Email</div>
              <span class="text-muted small">Used for login</span>
            </div>

            <div class="input-group">
              <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" placeholder="email@inquisitive.com..." required>
              <button class="btn" id="updateBtn" type="submit">Save</button>
            </div>

            <div id="msgUpdateEmail" class="small mt-2"></div>
          </form>

          <!-- Password -->
          <form id="formUpdatePassword" class="mb-4">
            <div class="fw-semibold mb-2">Password</div>

            <div class="mb-2">
              <label class="form-label small text-muted mb-1">Current password</label>
              <input type="password" class="form-control" name="currentPassword" required>
            </div>

            <div class="mb-2">
              <label class="form-label small text-muted mb-1">New password</label>
              <input type="password" class="form-control" name="newPassword" minlength="8" required>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted mb-1">Confirm new password</label>
              <input type="password" class="form-control" name="confirmPassword" minlength="8" required>
            </div>

            <button class="btn" id="updateBtn" type="submit">Update password</button>
            <div id="msgUpdatePassword" class="small mt-2"></div>
          </form>

          <!-- Theme -->
          <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Theme</div>
              <span class="text-muted small">Appearance</span>
            </div>

            <div class="btn-group w-100" role="group" aria-label="Theme">
              <input type="radio" class="btn-check" name="theme" id="themeSystem" autocomplete="off" checked>
              <label class="btn btn-outline-secondary" for="themeSystem">System</label>

              <input type="radio" class="btn-check" name="theme" id="themeLight" autocomplete="off">
              <label class="btn btn-outline-secondary" for="themeLight">Light</label>

              <input type="radio" class="btn-check" name="theme" id="themeDark" autocomplete="off">
              <label class="btn btn-outline-secondary" for="themeDark">Dark</label>
            </div>

            <div class="text-muted small mt-2">Saved on this device.</div>
          </div>

        </div>
      </div>
    </div>

    <!-- Right: Profile section -->
    <div class="col-12 col-lg-6 sm-eq__col">
      <div class="sm-card sm-panel h-100 sm-eq__card d-flex flex-column">

        <div class="sm-panel__head">
          <div>
            <h5 class="mb-0">Profile</h5>
            <div class="text-muted small">View your account</div>
          </div>
        </div>

        <div class="sm-panel__body flex-grow-1">

          <div class="d-flex align-items-center gap-3 mb-4">

            <?php 
                $first = $_SESSION['firstName'];
                $last = $_SESSION["lastName"];
                $firstFormatted = ucfirst($first);
                $lastFormatted = ucfirst($last);
            
                $role = $_SESSION['accessLevel'];
                $email = $_SESSION['email'];
                $userUUID = $_SESSION['userUUID'];
                $userCreationDate = $_SESSION['userCreationDate'];
                $userCreationDateFormatted = date("d-m-Y", strtotime($userCreationDate));
                $initials = mb_strtoupper($first[0] . $last[0]);
            ?>
            <img
              src="https://proficon.appserver.uk/api/initials/<?= htmlspecialchars($initials) ?>.svg"alt="<?= htmlspecialchars($initials) ?>"
              alt="Profile picture"
              style="width:72px;height:72px;border-radius:16px;object-fit:cover;"
            />
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="h5 mb-0"><?= htmlspecialchars($firstFormatted . " " . $lastFormatted) ?></div>
                <span class="badge text-bg-secondary"><?= htmlspecialchars($role) ?></span>

                <?php if (!empty($user['isDisabled'])): ?>
                  <span class="badge text-bg-danger">Disabled</span>
                <?php else: ?>
                  <span class="badge text-bg-success">Active</span>
                <?php endif; ?>
              </div>
              <div class="text-muted small"><?= htmlspecialchars($email) ?></div>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="text-muted small">User UUID</div>
              <div class="fw-semibold"><?= htmlspecialchars($userUUID) ?></div>
            </div>

            <div class="col-12 col-md-6">
              <div class="text-muted small">Joined</div>
              <div class="fw-semibold">
                <?= htmlspecialchars($userCreationDateFormatted); ?>
              </div>
            </div>
          </div>

          <hr class="my-4">

            <div class="row mt-4 g-3">

            <div class="col">
                <div class="stat-card">
                <div class="stat-number">14</div>
                <div class="stat-label">Quizzes Taken</div>
                </div>
            </div>

            <div class="col">
                <div class="stat-card">
                <div class="stat-number">82%</div>
                <div class="stat-label">Average Score</div>
                </div>
            </div>

            <div class="col">
                <div class="stat-card">
                <div class="stat-number">3</div>
                <div class="stat-label">Subjects</div>
                </div>
            </div>

            </div>

        </div>

      </div>
    </div>

  </div>
</div>
