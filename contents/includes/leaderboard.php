<?php

  /*$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

  $isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

  if (!$isAjax && !$isEmbeddedInDashboard) {
    $DASH_INCLUDE = __FILE__;
  }

  require __DIR__ . '/../dashboardNavigation.php';
  exit;*/

  // ^^^^ UNCOMMENT BEFORE MERGING ==============================================================================================================

  //Required includes.
  require_once ("./php/blockDirectAccess.php");
  require_once ("./php/_connect.php");


  // Check to see if session has started, if not, start one.
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }

  $db = new InquizitiveDB();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div id="leaderboard" class="container-fluid py-4">

  <!-- Header -->
  <div class="tm-header mb-4">
    <div>
      <h3 class="tm-title">Student Leaderboard</h3>
    </div>
  </div>
  <div class="row g-4">

    <!-- Left: Subject Selector -->
    <div class="col-12 col-lg-4">
      <div class="tm-card tm-panel h-100">
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

              while($row = $stmt->fetch_assoc()){
                echo "<option value='{$row['subjectUUID']}'>{$row['subjectTitle']}</option>";
              }
            ?>
          </select>
        </div>
      </div>
    </div>


    <!-- Right: Leaderboard -->
    <div class="col-12 col-lg-8">
      <div class="tm-card tm-panel h-100 d-flex flex-column">
        <div class="tm-panel__head">
          <div>
            <h5 class="mb-0">Top Students</h5>
            <div class="text-muted small">Highest scoring students</div>
          </div>
        </div>
        <div class="tm-panel__body">
          <div id="leaderboardResults">
            <p class="text-muted text-center">
              Select a subject to view leaderboard.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="./js/leaderboard.js"></script>