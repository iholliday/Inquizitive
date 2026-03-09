
<?php

// include __DIR__ . "/../../php/blockDirectAccess.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
  $DASH_INCLUDE = __FILE__;
  require __DIR__ . '/../dashboardNavigation.php';
  exit;
}

?>


<style>
#quizzesPage .quizCard{
border-radius: 16px;
transition: transform .12s ease, box-shadow .12s ease;
}
#quizzesPage .quizCard:hover{
transform: translateY(-2px);
box-shadow: 0 12px 30px rgba(0,0,0,.10) !important;
}
#quizzesPage .btn-primary{
border-radius: 12px;
padding: 10px 14px;
font-weight: 600;
background-color:#524A71;
}
</style>


<section id="quizzesPage" class="container py-4">

  <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
    <div>
      <h2 class="mb-1">Quizzes</h2>
      <p class="text-muted mb-0">Pick a quiz to begin.</p>
    </div>

    <div class="input-group" style="max-width: 360px;">
	
    </div>
  </div>

  <div class="row g-3" id="quizGrid">
    <?php
      require_once("./php/_connect.php");
      $db = new inquizitiveDB();

      if ($result = $db->Query("CALL GetAllQuizzes();", [])) {
        if (mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {

            // If you have a quizName column, this will use it, otherwise fallback:
            $quizTitle = !empty($row['quizName']) ? $row['quizName'] : "Quiz";

            echo '
              <div class="col-12 col-md-6 col-lg-4 quiz-card" data-title="'.htmlspecialchars(strtolower($quizTitle)).'" data-uuid="'.htmlspecialchars(strtolower($row["quizUUID"])).'">
                <div class="card shadow-sm border-0 h-100 quizCard">
                  <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                      <div>
                        <h5 class="card-title mb-1">'.htmlspecialchars($quizTitle).'</h5>
                        <div class="small text-muted">Quiz ID: <span class="font-monospace">'.htmlspecialchars($row["quizUUID"]).'</span></div>
                      </div>
                      <span class="badge rounded-pill text-bg-light border">Easy</span>
                    </div>

                    <div class="mt-3 text-muted small">
                      10 questions • Multiple choice
                    </div>

                    <div class="mt-auto pt-3 d-grid">

					  <button id="' . htmlspecialchars($row['quizUUID']) . '" class ="take-quiz-btn btn btn-primary" >Take quiz</button>
                    </div>
                  </div>
                </div>
              </div>
            ';
          }
        } else {
          echo '<div class="col-12"><div class="alert alert-warning mb-0">No quizzes found.</div></div>';
        }
      } else {
        echo '<div class="col-12"><div class="alert alert-danger mb-0">Failed to load quizzes.</div></div>';
      }
    ?>
  </div>
</section>

    <script> // Move to scriptName.js
        $(".take-quiz-btn").ready(function(){
            $(".take-quiz-btn").click(function(){
                $.ajax({
                    url: './testing', 
                    type: 'POST', 
                    data: { quizID: $(this).attr("id").toString(), key2: 'test' },
                    success: function(response) {
                        //console.log('Success:', response);
                        $("#content").html(response);
                    },
                    error: function(xhr, status, error) {
                        //console.log('Error:', error);
                    }
                });
            })
        })

    </script>
