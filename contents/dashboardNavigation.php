<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Inquizitive</title>
    <link rel="stylesheet" href="../css/global.css" />
    <link rel="stylesheet" href="../css/colours.css" />
    <link rel="stylesheet" href="../css/dashboardMain.css" />
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
                    <a class="nav-item active" href="#">Dashboard</a>
                    <a class="nav-item" href="#">Quizzes</a>
                    <a class="nav-item" href="#">Modules</a>
                    <a class="nav-item" href="#">Results</a>
                    <a class="nav-item" href="#">Settings</a>
                </nav>

                <!-- Drop up -->
                <div class="profile-menu" id="profileMenu">
                <button class="profile-trigger" id="profileTrigger" type="button" aria-haspopup="true" aria-expanded="false">
                    <div class="profile">
                    <div class="avatar"></div>
                    <div class="profile-info">
                        <div class="name">Name</div>
                        <div class="role">Role</div>
                    </div>
                    </div>
                </button>

                <div class="profile-actions" id="profileActions" role="menu" aria-hidden="true">
                    <button class="profile-btn" type="button" role="menuitem">My Profile</button>
                    <button class="profile-btn" type="button" role="menuitem">Log out</button>
                    <!-- We can add more buttons :) -->
                </div>
                </div>

                <script src="../js/dashboardMain.js"></script>


            </aside>

            <main class="main">
                <!-- EVERYTHING GOES IN MAIN!! -->
                 <h1>no</h1>
                 <p>hi</p>
            </main>
        </div>

    </div>
</body>

</html>