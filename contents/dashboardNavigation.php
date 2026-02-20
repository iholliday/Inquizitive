<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Inquizitive</title>
    <link rel="stylesheet" href="./css/global.css" />
    <link rel="stylesheet" href="./css/colours.css" />
    <link rel="stylesheet" href="./css/dashboardMain.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a class="nav-item link" id="results">Results</a>
                    <div class="lecturer-nav">
                        <hr class="nav-divider">
                        <a class="nav-item link" id="student-management">Student Management</a>
                        <a class="nav-item link" id="test-management">Test Management</a>
                    </div>
                    <div class="admin-nav">
                        <hr class="nav-divider">
                        <a class="nav-item link" id="lecturer-management">Lecturer Management</a>
                        <a class="nav-item link" id="subject-management">Subject Management</a>
                    </div>
                </nav>

                <!-- Drop up -->
                <div class="profile-menu" id="profileMenu">
                <hr class="dropup-divider">
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
                    <button class="profile-btn link" type="button" role="menuitem" id="profile">My Profile</button>
                    <button class="profile-btn link" type="button" role="menuitem" id="settings">Settings</button>
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