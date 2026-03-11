<?php
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

if (!$isAjax && !$isEmbeddedInDashboard) {
  $DASH_INCLUDE = __FILE__;
  require __DIR__ . '/../dashboardNavigation.php';
  exit;
}

    $userID = $_SESSION["userUUID"];
    $db = New inquizitiveDB();
	// Get subject data
    $result = $db->Query("CALL GetUserSubjectsByUUID(?)", [$userID]);
    $subjectData = [];

    if (mysqli_num_rows($result) > 0) {
        while (
            $row = mysqli_fetch_assoc($result)
        ){array_push($subjectData, $row);}
	}

	// Get quiz data
	$result = $db->Query("CALL GetQuizzesByUserUUID(?)", [$userID]);
    $quizData = [];
	
    if (mysqli_num_rows($result) > 0) {
        while (
            $row = mysqli_fetch_assoc($result)
        ){array_push($quizData, $row);}
    }

	print_r($quizData);

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
            <h1>Welcome <!--<?=$dataArray["firstName"] ?>--></h2>
			<div class="underline"></div>
			<div class="flex-col">
				<h3>Tests to complete</h2>
				<div class="flex-row">
					<div class="card quiz-card">
						<div><h2><?=$dataArray["subjectTitle"] ?></h2></div>
						<div class="underline"></div>
						<div><?=$dataArray["subjectDescription"] ?></div>
						<button class="bottom right">Take Quiz</button>
					</div>
				</div>
			</div>
			<div class="flex-row"></div>
        </div>
    </div>
</body>