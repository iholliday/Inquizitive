<?php
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $isEmbeddedInDashboard = defined('IN_DASHBOARD_SHELL');

    if (!$isAjax && !$isEmbeddedInDashboard) {
    $DASH_INCLUDE = __FILE__;
    require __DIR__ . '/../dashboardNavigation.php';
    exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/subjects.css"/>
    <title>Subjects Page</title>
</head>
<body>
    <div id="subjects-page" class="container-fluid py-4">
        <div class="flex-col main-column">
            <h2>Your Subjects</h2>

            <div class="flex-row subject-row" style="min-height: 1000px">
                <div class="flex-col flex-grow-2">
                    <div class="card">How many Subjects</div>
                    <div class="card height-100">Table of Subjects</div>
                </div>
                <div class="flex-col flex-grow-1">
                    <div class="card height-100">Statistics</div>
                </div>
            </div>
            <div class="flex-row">

            </div>
        </div>
    </div>
</body>
</html>