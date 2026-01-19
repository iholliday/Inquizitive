<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Inquizitive</title>
    <link rel="stylesheet" href="./css/global.css" />
    <link rel="stylesheet" href="./css/colours.css" />
    <link rel="stylesheet" href="./css/dashboardMain.css" />
    <script
  src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script>
</head>

<body>
    <div class="app">
        <header class="topbar">
            <div class="brand">
                <div class="logo"></div>
                <span class="brand-name">INQUIZITIVE</span>
            </div>

            <div class="actions">
                <!-- Login button can go here -->
            </div>
        </header>

        <!-- Sidebar + Main -->
        <div class="layout">
            <aside class="sidebar">
                <nav class="nav">
                    <div class="nav-item active link" id="dashboardPage">Dashboard</div>
                    <div class="nav-item link" id="quizPage">Quizzes</div>
                    <div class="nav-item link" id="subjectsPage">Subjects</div>
                    <div class="nav-item link" id="resultsPage">Results</div>
                </nav>

                <!-- Drop up -->
                <div class="profile-menu" id="profileMenu">
                <button class="profile-trigger" id="profileTrigger" type="button" aria-haspopup="true" aria-expanded="false">
                    <div class="profile">
                    <div class="avatar"></div>
                    <div class="profile-info">
                        <div class="name"><span><?php echo htmlspecialchars($_SESSION['firstName']);echo " ";echo htmlspecialchars($_SESSION['lastName']);?></span></div>
                        <div class="role">Role</span></div>
                    </div>
                    </div>
                </button>

                <div class="profile-actions" id="profileActions" role="menu" aria-hidden="true">
                    <button class="profile-btn link" type="button" role="menuitem" id="profilePage">My Profile</button>
                    <button class="profile-btn link" type="button" role="menuitem" id="settingsPage">Settings</button>
                    <button class="profile-btn link" type="button" role="menuitem" id="logoutPage">Log out</button>
                    <!-- We can add more buttons :) -->
                </div>
                </div>

                <script src="./js/dashboardMain.js"></script>

            </aside>

            <main class="main">
                <div id="content"></div>
            </main>
        </div>

    </div>
</body>

</html>