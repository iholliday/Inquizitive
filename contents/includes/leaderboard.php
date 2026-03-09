<?php

  // If user isn't logged in, redirect to landing page. Not AJAX as this check should happen befoe page loads.
  if (!isset($_SESSION['userUUID']))
  {
     header("Location:./");
     exit();
  }
  
  // ============================ START OF ADAM CODE ============================
  
  // Check to see if request is AJAX.
  $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

  // Check to see if file is included from dashboard shell.
  $isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

  // If the request isn't AJAX/already embedded in dashboard, redirect through dashboard navigation.
  if (!$isAjax && !$isEmbeddedInDashboard) {
    $DASH_INCLUDE = __FILE__;
    require __DIR__ . '/../dashboardNavigation.php';
    exit;
  }

  // ============================ END OF ADAM CODE ============================

  //Required includes
  require_once __DIR__ . "/../../php/_connect.php";

  // Check to see if session has started, if not, start one.
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }

  $db = new InquizitiveDB();

  // UUID for currency
  $currencyUUID = 'ec0ad14f-12c5-11f1-98eb-bc2411ac3867';

  // Get top 10 users by quantity
  $stmt = $db->Query("CALL GetTopUsersByCurrency(?)", [$currencyUUID]);

  $topIQ = [];
  while ($row = $stmt->fetch_assoc()) 
  {
      $topIQ[] = $row;
  }
?>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<!-- Utilises styling from lecturer dashboard pages to reduce duplicated code -->
<div id="lecturerDashboard" class="container-fluid py-4">

  <div id = "leaderboard">
    <!-- Header -->
    <div class="tm-header mb-4">
      <div>
        <h3 class="tm-title">Student Leaderboard</h3>
        <p class="tm-subtitle mb-0">View top students by subject and total IQ points</p>
      </div>
    </div>
    <div class="row g-4 tm-eq">

    <!-- Subject Selector -->
    <div class="col-12 col-lg-4 tm-eq__col">
      <div class="tm-card tm-panel h-100 tm-eq__card leaderboard-container">
        <div class="tm-panel__head">
          <div>
            <h5 class="mb-0">Select Subject</h5>
            <div class="text-muted small">Choose a subject to view leaderboard</div>
          </div>
        </div>
        <div class="tm-panel__body">
          <label class="form-label">Subject</label>
          <select id="lbSubjectSelect" class="form-select">
            <option value="">Select Subject</option>
            <?php
            $stmt = $db->Query("CALL GetAllSubjects()");
            while($row = $stmt->fetch_assoc())
            {
              echo "<option value='{$row['subjectUUID']}'>{$row['subjectTitle']}</option>";
            }
            ?>
          </select>
        </div>
      </div>
    </div>


    <!-- Subject Leaderboard -->
    <div class="col-12 col-lg-8 tm-eq__col">
      <div class="tm-card tm-panel h-100 tm-eq__card d-flex flex-column leaderboard-container">
        <div class="tm-panel__head top-5-results">
          <div>
            <h5 class="mb-0">Top 5 Students</h5>
            <div class="text-muted small">Highest scoring students by subject</div>
          </div>
        </div>
          <div class="tm-tableWrap">
            <div id="leaderboardResults" class="p-4 text-center text-muted">
              <div class="placeholder-text">Select a subject to view leaderboard.</div>
              <div class="leaderboard-table"></div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Top IQ Points Leaderboard -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="tm-card tm-panel">
          <div class="tm-panel__head">
            <div>
              <h5 class="mb-0">Top 10 IQ Points</h5>
              <div class="text-muted small">Students with the highest IQ point totals</div>
            </div>
          </div>
          <div class="tm-panel__scroll">
            <div class="tm-tableWrap">
              <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                  <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>IQ Points</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (count($topIQ) === 0): ?>
                    <tr>
                      <td colspan="4" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($topIQ as $index => $user): ?>
                    <tr>
                      <td>        
                        <strong>
                          <?php
                            // Top 3 get trophies instead of numbers.
                            if ($index === 0) echo '<span class="material-icons trophy-gold">emoji_events</span>'; 
                            elseif ($index === 1) echo '<span class="material-icons trophy-silver">emoji_events</span>'; 
                            elseif ($index === 2) echo '<span class="material-icons trophy-bronze">emoji_events</span>'; 
                            else echo $index + 1;
                          ?>
                        </strong>
                      </td>
                      <td class="py-2"><?= htmlspecialchars($user['firstName']) ?></td>
                      <td class="py-2"><?= htmlspecialchars($user['lastName']) ?></td>
                      <td class="py-2"><?= htmlspecialchars($user['score']) ?></td>
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
</div>
<script src="./js/leaderboard.js"></script>