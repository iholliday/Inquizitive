<!DOCTYPE html>
<html lang="en">

<?php
if (!defined('IN_DASHBOARD_SHELL')) {
    define('IN_DASHBOARD_SHELL', true);
}


$userIcon ="";
require_once("./php/_connect.php");
$db = new inquizitiveDB();
$userUUID = htmlspecialchars($_SESSION['userUUID']);

$result = $db->Query("CALL GetUserByUserUUID(?)",[$userUUID]);
$row = mysqli_fetch_assoc($result);
$userIcon = htmlspecialchars($row['avatar']);



$first = $_SESSION['firstName'];
$last = $_SESSION["lastName"];
$initials = mb_strtoupper($first[0] . $last[0]);
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Inquizitive</title>
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/global.css" />
    <link rel="stylesheet" href="./css/colours.css" />
    <link rel="stylesheet" href="./css/dashboardMain.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

</head>

<body>
    <div id ="h" tabindex="0" class="click hidden"></div>
    <div class="app">
        <header class="topbar">
            <div class="brand">
                <div class="logo"></div>
                <span class="brand-name">INQUIZITIVE</span>
            </div>

            <div class="actions">
                <button class="icon-btn burger-btn" id="burgerToggle" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </header>

        <!-- Sidebar + Main -->
        <div class="layout">
            <aside class="sidebar">
                <nav class="nav">
                    <a class="nav-item active link" id="landing">Dashboard</a>
                    <a class="nav-item link" id="quizzes">Quizzes</a>
                    <a class="nav-item link" id="subjects">Subjects</a>
                    <a class="nav-item link" id="leaderboard">Leaderboard</a>
                    <?php 
                        if (isset($_SESSION['accessLevel']) && in_array($_SESSION['accessLevel'], ["LECTURER", "ADMIN"])) {
                        ?>
                            <div class="lecturer-nav">
                                <hr class="nav-divider">
                                <a class="nav-item link" id="student-management">Student Management</a>
                                <a class="nav-item link" id="test-management">Quiz Management</a>
                            </div>
                        <?php
                        }
                    ?>

                    <?php 
                        if (isset($_SESSION['accessLevel']) && $_SESSION['accessLevel'] === "ADMIN") {
                        ?>
                            <div class="admin-nav">
                                <hr class="nav-divider">
                                <a class="nav-item link" id="lecturer-management">Lecturer Management</a>
                                <a class="nav-item link" id="subject-management">Subject Management</a>
                            </div>
                        <?php
                        }
                    ?>
                </nav>

                <!-- Drop up -->
                <div class="profile-menu" id="profileMenu">
                <hr class="dropup-divider">
                <button class="profile-trigger" id="profileTrigger" type="button" aria-haspopup="true" aria-expanded="false">
                    <div class="profile">
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
                        />
                    <div class="profile-info">
                        <div class="name"><span><?php echo htmlspecialchars($_SESSION['firstName']);echo " ";echo htmlspecialchars($_SESSION['lastName']);?></span></div>
                        <div class="role"><?php echo htmlspecialchars($_SESSION['accessLevel'])?></span></div>
                    </div>
                    </div>
                </button>

                <div class="profile-actions" id="profileActions" role="menu" aria-hidden="true">
                    <button class="profile-btn link" type="button" role="menuitem" id="settings">Settings / Profile</button>
                    <button class="profile-btn link" type="button" role="menuitem" id="shop">Shop</button>
                    <button class="profile-btn" type="button" role="menuitem" id="logout">Log out</button>
                    <!-- We can add more buttons :) -->
                </div>
                </div>

                <script src="./js/dashboardMain.js"></script>

            </aside>

            <main class="main">
                <div id="content">
                    <?php
                    if (!isset($DASH_INCLUDE)) {
                        $DASH_INCLUDE = __DIR__ . "/includes/dashboardMain.php";
                    }

                    // Loads the landing page ont the dashboard
                    if (file_exists($DASH_INCLUDE)) {
                        include $DASH_INCLUDE;
                        
                    } else {
                        echo "<div style='padding:16px;'>Default dashboard page could not be loaded.</div>";
                    }
                    ?>
                </div>
            </main>
        </div>

    </div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <script>
    $(function () {
        const activeId = sessionStorage.getItem("currentPage");
        if (activeId) {
        $(".nav-item").removeClass("active");
        $("#" + activeId).addClass("active");
        }
    });
    </script>


</body>

</html>