<?php
    // Everything in this file is loaded no matter the site.

    // Requires.
    require_once __DIR__ . "/jRoute/_load.php";
    require_once ("php/_connect.php");

    // Establishing routing variables ($routerOptions order: debugMode, urlPrefix, cspLevel, forceTrailingSlash).
    $routerOptions = new RouterOptions(false, '/Inquizitive', 'none', false);
    $route = new jRoute($routerOptions);

    // Default route.
    $route->Route(['get'], '/', "./contents/login.php"); 

    // Allow serving static CSS and JS directories directly.
    $route->AddDir('/css', "./css/");
    $route->AddDir('/js', "./js/");

    // Routes required for all pages.
    $route->Route(['post'], '/auth', "./php/auth.php");

    $route->Route(['get'], '/dashboard', "./contents/dashboardNavigation.php");

    $route->Route(['get'], '/landing', "./contents/includes/dashboardMain.php");
    $route->Route(['get'], '/quizzes', "./contents/includes/quizzesPage.php");
    $route->Route(['get'], '/subjects', "./contents/includes/subjectsPage.php");
    $route->Route(['get'], '/results', "./contents/includes/resultsPage.php");
    $route->Route(['get'], '/student-management', "./contents/includes/studentManagement.php");
    $route->Route(['get'], '/test-management', "./contents/includes/testManagement.php");
    $route->Route(['get'], '/lecturer-management', "./contents/includes/admLecturerManagement.php");
    $route->Route(['get'], '/settings', "./contents/includes/settingsPage.php");
    $route->Route(['get'], '/profile', "./contents/includes/profilePage.php");



    $route->Route(['get'], '/logoutPage', "./php/logout.php");


    // Any code must be above this.
    echo $route->Dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>