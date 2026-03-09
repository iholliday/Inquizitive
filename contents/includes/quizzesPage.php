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

require_once("./php/_connect.php");
$db = new inquizitiveDB();
$conn = $db->connect;

// You will probably already have this in session
// change this to however you store the logged in user UUID
$userUUID = $_SESSION['userUUID'] ?? null;
?>

<style>

</style>

<section id="quizzesPage" class="container py-4">

  <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h2 class="mb-1">Quizzes</h2>
      <p class="text-muted mb-0">Take a new quiz or view quizzes you have already completed.</p>
    </div>

    <div class="tab-switcher" role="tablist" aria-label="Quiz sections">
      <button type="button" class="btn btn-outline-primary active quiz-tab-btn" data-target="availablePanel">
        Quizzes
      </button>
      <button type="button" class="btn btn-outline-primary quiz-tab-btn" data-target="completedPanel">
        Completed
      </button>
    </div>
  </div>

  <div class="quiz-panels-wrapper">

    <!-- AVAILABLE QUIZZES -->
    <div id="availablePanel" class="quiz-panel is-active">
      <div class="mb-3">
        <h5 class="mb-1">Available quizzes</h5>
        <p class="section-subtitle">Pick a quiz to begin.</p>
      </div>

      <div class="row g-3">
        <?php
          if ($result = $db->Query("CALL GetQuizzesBySubjectLink(?);", [$userUUID])) {
            if (mysqli_num_rows($result) > 0) {
              while($row = mysqli_fetch_assoc($result)) {

                $quizTitle = !empty($row['quizName']) ? $row['quizName'] : "Quiz";
                  $subjectName = !empty($row['subjectTitle']) ? $row['subjectTitle'] : 'No Subject';

                echo '
                  <div class="col-12 col-md-6 col-lg-4 quiz-card" data-title="'.htmlspecialchars(strtolower($quizTitle)).'" data-uuid="'.htmlspecialchars(strtolower($row["quizUUID"])).'">
                    <div class="card shadow-sm border-0 h-100 quizCard">
                      <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                          <div>
                            <h5 class="card-title mb-1">'.htmlspecialchars($quizTitle).'</h5>
                            <div class="small text-muted">Subject: <span class="font-monospace">'.htmlspecialchars($subjectName).'</span></div>
                          </div>
                          <span class="badge rounded-pill text-bg-light border">Easy</span>
                        </div>

                        <div class="mt-3 text-muted small">
                          '.(int)$row['questionCount'].' questions • Multiple choice
                        </div>

                        <div class="mt-auto pt-3 d-grid">
                          <button id="' . htmlspecialchars($row['quizUUID']) . '" class="take-quiz-btn btn btn-primary">
                            Take quiz
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ';
              }
            } else {
              echo '<div class="col-12"><div class="alert alert-warning mb-0">No quizzes found.</div></div>';
            }

            mysqli_free_result($result);
            while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
              $extra = mysqli_store_result($conn);
              if ($extra) mysqli_free_result($extra);
            }
          } else {
            echo '<div class="col-12"><div class="alert alert-danger mb-0">Failed to load quizzes.</div></div>';
          }
        ?>
      </div>
    </div>

    <!-- COMPLETED QUIZZES -->
    <div id="completedPanel" class="quiz-panel is-hidden">
      <div class="mb-3">
        <h5 class="mb-1">Completed quizzes</h5>
        <p class="section-subtitle">View quizzes you have already taken.</p>
      </div>

      <div class="row g-3">
        <?php
          if ($userUUID) {

            if ($completedResult = $db->Query("CALL GetCompletedQuizzesByUserUUID(?);", [$userUUID])) {
              if (mysqli_num_rows($completedResult) > 0) {
                while($row = mysqli_fetch_assoc($completedResult)) {

                  $quizTitle = !empty($row['quizName']) ? $row['quizName'] : 'Completed Quiz';
                  $score = isset($row['score']) ? $row['score'] . '%' : 'Completed';
                  $completedDate = !empty($row['completionDate']) ? $row['completionDate'] : 'Unknown date';
                  $dateFormatted = date("d/m/Y", strtotime($completedDate));

                  $subjectName = !empty($row['subjectTitle']) ? $row['subjectTitle'] : 'No Subject';


                  echo '
                    <div class="col-12 col-md-6 col-lg-4">
                      <div class="card shadow-sm border-0 h-100 quizCard">
                        <div class="card-body d-flex flex-column">
                          <div class="d-flex align-items-start justify-content-between gap-2">
                            <div>
                              <h5 class="card-title mb-1">'.htmlspecialchars($quizTitle).'</h5>
                              <div class="small text-muted">Subject: <span class="font-monospace">'.htmlspecialchars($subjectName).'</span></div>
                            </div>
                            <span class="result-pill">'.htmlspecialchars($score).'</span>
                          </div>

                          <div class="mt-3 text-muted small">
                            Completed on: '.htmlspecialchars($dateFormatted).'
                          </div>

                          <div class="mt-auto pt-3 d-grid">
                            <button
                              class="review-quiz-btn btn btn-outline-primary"
                              data-quiz-id="'.htmlspecialchars($row['quizUUID']).'">
                              View attempt
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  ';
                }
              } else {
                echo '<div class="col-12"><div class="alert alert-info mb-0">You have not completed any quizzes yet.</div></div>';
              }

              mysqli_free_result($completedResult);
              while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
                $extra = mysqli_store_result($conn);
                if ($extra) mysqli_free_result($extra);
              }
            } else {
              echo '<div class="col-12"><div class="alert alert-danger mb-0">Failed to load completed quizzes.</div></div>';
            }
          } else {
            echo '<div class="col-12"><div class="alert alert-warning mb-0">User not found in session.</div></div>';
          }
        ?>
      </div>
    </div>

  </div>
</section>

<script>
  // FUNCTION TO SWAP TO COMPLETED
$(document).ready(function () {
  $('.quiz-tab-btn').on('click', function () {
    const targetId = $(this).data('target');
    const $target = $('#' + targetId);
    const $current = $('.quiz-panel.is-active');

    if ($target[0] === $current[0]) return;

    $('.quiz-tab-btn').removeClass('active');
    $(this).addClass('active');

    $current.removeClass('is-active').addClass('to-left');

    setTimeout(function () {
      $current.removeClass('to-left').addClass('is-hidden');
      $target.removeClass('is-hidden').addClass('is-active');
    }, 140);
  });

  // TAKE QUIZ BUTTON
  $(document).on('click', '.take-quiz-btn', function () {
    $.ajax({
      url: './testing',
      type: 'POST',
      data: {
        quizID: $(this).attr('id').toString(),
        key2: 'test'
      },
      success: function (response) {
        $('#content').html(response);
      },
      error: function (xhr, status, error) {
        console.log('Error:', error);
      }
    });
  });

  // review completed quiz
  $(document).on('click', '.review-quiz-btn', function () {
    $.ajax({
      url: './reviewQuizAttempt',
      type: 'POST',
      data: {
        quizID: $(this).data('quiz-id').toString()
      },
      success: function (response) {
        $('#content').html(response);
      },
      error: function (xhr, status, error) {
        console.log('Error:', error);
      }
    });
  });
});
</script>