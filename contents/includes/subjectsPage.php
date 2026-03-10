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

    <title>Subjects Page</title>
</head>
<body>
    <div id="subjects-page" class="container-fluid py-4">
        <div class="flex-col main-column">
            <h2>Your Subjects</h2>

            <div class="flex-row subject-row" style="min-height: 1000px">
                <div class="flex-col flex-grow-2">
                    <div class="card">How many Subjects
                        <div><?php echo htmlspecialchars(sizeof($dataArray)) ?></div>
                    </div>
                    <div class="card height-100">Table of Subjects
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
                                    <td><button data="<?=htmlspecialchars($item["authorName"]) ?>"><i class="material-symbols-outlined">
equalizer
</i></button></td>
                                </tr><?php
                            }
                            ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex-col flex-grow-1">
                    <div class="card height-100">Statistics</div>
                </div>
            </div>
            <div class="flex-row">

            </div>
        </div>
    </div>
    <script>


    //code here test
    </script>
</body>
</html>