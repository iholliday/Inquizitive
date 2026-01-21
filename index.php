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
    $route->Route(['get'], '/subjectsPage', "./contents/includes/inc-SubjectsDashboardPage.php");



    $route->Route(['get'], '/logoutPage', "./php/logout.php");


    // Any code must be above this.
    echo $route->Dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>