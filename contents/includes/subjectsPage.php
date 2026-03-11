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
    $result = $db->Query("CALL GetUserSubjectsByUUID(?)", [$userID]);
    $dataArray = [];

    if (mysqli_num_rows($result) > 0) {
        while (
            $row = mysqli_fetch_assoc($result)
        ){array_push($dataArray, $row);}
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/subjects.css"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=equalizer" />
    <script src="https://www.gstatic.com/charts/loader.js"></script>

    <title>Subjects Page</title>
</head>
<body>
    <div id="subjects-page" class="container-fluid py-4">
        <div class="flex-col main-column">
            <h2>Your Subjects</h2>

            <div class="flex-row subject-row" style="min-height: 1000px">
                <div class="flex-col flex-grow-2">
                    <div class="card"><h4>How many Subjects</h4>
                        <div><h2><?php echo htmlspecialchars(sizeof($dataArray)) ?></h2></div>
                    </div>
                    <div class="card height-fit-content"><h4>Table of Subjects</h4>
                        <div>
                            <table class="table table-hover">
                            <thead>
                                <tr>
                                <th scope="col">Title</th>
                                <th scope="col">Description</th>
                                <th scope="col">Author</th>
                                <th scope="col">View Stats</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($dataArray as $item) {
                                ?><tr>
                                    <th scope="row"><?=htmlspecialchars($item["subjectTitle"]) ?></th>
                                    <td><?=htmlspecialchars($item["subjectDescription"]) ?></td>
                                    <td><?=htmlspecialchars($item["authorName"]) ?></td>
                                    <td><button class = "view-data-chart" data="<?=htmlspecialchars($item["authorName"]) ?>"><i class="material-symbols-outlined">equalizer</i></button></td>
                                </tr><?php
                            }
                            //fdfhjdsfhsof
                            ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex-col flex-grow-1">
                    <div class="card height-fit-content">
                        <div>
                            <h4>Statistics</h4>
                            <p>Click on the statistics button on a subject to show it's statistics.</p>
                        </div>
                        <div id="statsChart"><div id="myChart" style="max-width:100%; "></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-row">

            </div>
        </div>
    </div>
    <script>


google.charts.load('current',{packages:['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart(arr) {

// Set Data
const data = google.visualization.arrayToDataTable(arr);

// Set Options
const options = {
  title: 'House Prices vs. Size',
  hAxis: {title: 'Square Meters'},
  vAxis: {title: 'Price in Millions'},
  legend: 'none'
};

// Draw
const chart = new google.visualization.LineChart(document.getElementById('myChart'));
chart.draw(data, options);

}
        $(".view-data-chart").ready(function(){
            $(".view-data-chart").click(function (){
                $.ajax({
                    url: "./subjectStatistics",
                    type:"POST",
                    data: {subjectUUID: $(this).attr("data").toString()},
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
        })

    </script>
</body>
</html>