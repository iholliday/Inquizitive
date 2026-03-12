<?php
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

if (!$isAjax && !$isEmbeddedInDashboard) {
  $DASH_INCLUDE = __FILE__;
  require __DIR__ . '/../dashboardNavigation.php';
  exit;
}

    // Session data
    $userID = $_SESSION["userUUID"];
    $db = New inquizitiveDB();
	// Get subject data
    $result = $db->Query("CALL GetUserSubjectsByUUID(?)", [$userID]);
    $subjectData = [];
    // Protection against empty set
    if (mysqli_num_rows($result) > 0) {
        while (
            $row = mysqli_fetch_assoc($result)
        ){array_push($subjectData, $row);}
	}

	// Get quiz data
	$result = $db->Query("CALL GetQuizzesByUserUUID(?)", [$userID]);
    $quizData = [];
    // Protection against empty set
    if (mysqli_num_rows($result) > 0) {
        while (
            $row = mysqli_fetch_assoc($result)
        ){array_push($quizData, $row);}
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/dashboardMain.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=equalizer" />
    <script src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
    <div id="student-dashboard" class="container-fluid py-4">
        <div class="flex-col main-column">
            <h1>Welcome</h2>
			<div class="underline"></div>
            <!-- First row - quiz cards -->
			<div class="flex-col">
				<h2>Quizzes to complete</h2>
				<a href="">View quizzes</a>
				<div class="flex-row">
					<?php
                    // Output quiz cards along top row
					for ($i = 0; $i <= 6 && $i < count($quizData); $i++) {
					?><div class="card quiz-card">
						<div><h2><?=htmlspecialchars($quizData[$i]["subjectTitle"]) ?></h2></div>
						<div class="underline"></div>
						<div><?=htmlspecialchars($quizData[$i]["quizName"]) ?></div>
						<a href="quizzesPage.php" class="bottom right"><button>Take Quiz</button></a>
					</div>
					<?php
					}
					?>
				</div>
			</div>

			<div class="underline"></div>

            <!-- Second row - subjects and statistics -->
			<div class="flex-row md-collapse">
				<div class="card">
					<h2>Enrolled Subjects</h2>
					<a href="">View subjects</a>
					<div>
						<table class="table table-hover">
                            <thead>
                                <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Author</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            // Output subject table
                            foreach ($subjectData as $item) {
                                ?><tr>
                                    <th scope="row"><?=htmlspecialchars($item["subjectTitle"]) ?></th>
                                    <td><?=htmlspecialchars($item["authorName"]) ?></td>
                                </tr><?php
                            }
                            ?>
                            </tbody>
                        </table>

					</div>
				</div>
				<div class="card">
					<h2>Overall Score Over Time</h2>
					<div class="graph-container">
                		<div class="view-data-chart" data="<?=htmlspecialchars($item["subjectUUID"]) ?>"></div>
						<div id="statsChart"><div id="myChart"></div></div>
					</div>
				</div>
			</div>
        </div>
    </div>
    
<script>

// Initialise google charts
google.charts.load('current',{packages:['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart(arr) {

// Set Data
const data = google.visualization.arrayToDataTable(arr);

// Set Options
const options = {
  title: 'Score over Time Across All Subjects',
  hAxis: {title: 'Date'},
  vAxis: {title: 'Score'},
  legend: 'none'
};

// Draw
const chart = new google.visualization.LineChart(document.getElementById('myChart'));
chart.draw(data, options);

}

$(".student-dashboard").ready(function(){

    $.ajax({
        url: "./dashboardStatistics",
        type:"GET",
        success: function(response)
        {
            console.log(response)
            drawChart(response);
        },
        error: function(e)
        {

        }
    })

})




</script>
</body>