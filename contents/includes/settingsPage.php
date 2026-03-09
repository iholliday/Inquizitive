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

$userUUID = htmlspecialchars($_SESSION['userUUID']);
$result = $db->Query("CALL GetUserByUserUUID(?)",[$userUUID]);
$row = mysqli_fetch_assoc($result);
$userIcon = htmlspecialchars($row['avatar']);

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
            <input type="hidden" name="action" value="updateEmail">

            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="fw-semibold">Email</div>
              <span class="text-muted small">Used for login</span>
            </div>

            <div class="input-group">
              <input
                type="email"
                class="form-control"
                name="email"
                value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
                placeholder="email@inquisitive.com..."
                required
              >
              <button class="btn styledBtn" type="submit">Save</button>
            </div>

            <div id="msgUpdateEmail" class="small mt-2"></div>
          </form>

          <!-- Password -->
          <form id="formUpdatePassword" class="mb-4">
            <input type="hidden" name="action" value="updatePassword">

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

            <button class="btn styledBtn" type="submit">Update password</button>
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
              src="<?php
                  if($userIcon !== "" && $userIcon !== " " && $userIcon !== NULL)
                  {
                      echo $userIcon;
                  }else
                  {
                      echo "https://proficon.appserver.uk/api/initials/" . htmlspecialchars($initials);
                  } 
              ?>"
              alt="Profile picture"
              class="avatar"
              style="width:72px;height:72px;border-radius:16px;object-fit:cover;"
              />
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="h5 mb-0"><?= htmlspecialchars($firstFormatted . " " . $lastFormatted) ?></div>
                <span class="badge text-bg-secondary"><?= htmlspecialchars($role) ?></span>
                  <span class="badge text-bg-success">Active</span>
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


<script>
$(document).ready(function () {

    $("#formUpdateEmail").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "./update-user",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#msgUpdateEmail")
                        .text(response.message)
                        .removeClass("text-danger")
                        .addClass("text-success");

                    Swal.fire({
                        icon: "success",
                        title: "Email updated",
                        text: response.message,
                        confirmButtonText: "OK"
                    });

                    if (response.email) {
                        $("#formUpdateEmail input[name='email']").val(response.email);
                    }
                } else {
                    $("#msgUpdateEmail")
                        .text(response.message)
                        .removeClass("text-success")
                        .addClass("text-danger");

                    Swal.fire({
                        icon: "error",
                        title: "Update failed",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);

                $("#msgUpdateEmail")
                    .text("Something went wrong while updating email.")
                    .removeClass("text-success")
                    .addClass("text-danger");

                Swal.fire({
                    icon: "error",
                    title: "Server error",
                    text: xhr.responseText || "Something went wrong while updating email.",
                    confirmButtonText: "OK"
                });
            }
        });
    });

    $("#formUpdatePassword").on("submit", function (e) {
        e.preventDefault();

        $.ajax({
            url: "./update-user",
            type: "POST",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#msgUpdatePassword")
                        .text(response.message)
                        .removeClass("text-danger")
                        .addClass("text-success");

                    $("#formUpdatePassword")[0].reset();

                    Swal.fire({
                        icon: "success",
                        title: "Password updated",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                } else {
                    $("#msgUpdatePassword")
                        .text(response.message)
                        .removeClass("text-success")
                        .addClass("text-danger");

                    Swal.fire({
                        icon: "error",
                        title: "Update failed",
                        text: response.message,
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);

                $("#msgUpdatePassword")
                    .text("Something went wrong while updating password.")
                    .removeClass("text-success")
                    .addClass("text-danger");

                Swal.fire({
                    icon: "error",
                    title: "Server error",
                    text: xhr.responseText || "Something went wrong while updating password.",
                    confirmButtonText: "OK"
                });
            }
        });
    });

});
</script>