<?php
    // Everything in this file is loaded no matter the site.

    // Requires.
    require_once __DIR__ . "/jRoute/_load.php";
    require_once ("php/_connect.php");

    // Establishing routing variables ($routerOptions order: debugMode, urlPrefix, cspLevel, forceTrailingSlash).
    $routerOptions = new RouterOptions(false, '/Inquizitive', 'none', false);
    $route = new jRoute($routerOptions);

    // Setting site to UK timezone.
    date_default_timezone_set('Europe/London');

    // Default route.
    $route->Route(['get'], '/', "./contents/login.php"); 

    // Allow serving static CSS and JS directories directly.
    $route->AddDir('/css', "./css/");
    $route->AddDir('/js', "./js/");
    $route->AddDir('/img', "./contents/images/");

    // Routes required for all pages.
    $route->Route(['post'], '/auth', "./php/auth.php");
    $route->Route(['post'], '/add-lecturer', "./php/addLecturer.php");
    $route->Route(['post'], '/delete-user', "./php/deleteUser.php");
    $route->Route(['post'], '/set-user-disabled', "./php/setUserDisabled.php");
    $route->Route(['post'], '/create-subject', "./php/addSubject.php");
    $route->Route(['post'], '/set-subject-disabled', "./php/setSubjectDisabled.php");

    $route->Route(['post'], '/createAccount', "./php/createAccount.php");
    $route->Route(['get'], '/dashboard', "./contents/dashboardNavigation.php");
    //$route->Route(['get'], '/subjectsPage', "./contents/includes/inc-SubjectsDashboardPage.php");
    $route->Route(['get'], '/signup', "./contents/signup.php");
    $route->Route(['get'], '/login', "./contents/login.php");
    $route->Route(['get'], '/dashboard', "./contents/dashboardNavigation.php");
    $route->Route(['get'], '/landing', "./contents/includes/dashboardMain.php");
    $route->Route(['get'], '/quizzes', "./contents/includes/quizzesPage.php");
    $route->Route(['get'], '/subjects', "./contents/includes/subjectsPage.php");
    $route->Route(['get'], '/results', "./contents/includes/resultsPage.php");
    $route->Route(['get'], '/student-management', "./contents/includes/studentManagement.php");
    $route->Route(['get'], '/test-management', "./contents/includes/testManagement.php");
    $route->Route(['post'], '/test-management/editor', "./contents/includes/quizManagement.php");
    $route->Route(['get'], '/lecturer-management', "./contents/includes/admLecturerManagement.php");
    $route->Route(['get'], '/subject-management', "./contents/includes/admSubjectManagement.php");
    $route->Route(['get'], '/settings', "./contents/includes/settingsPage.php");
    $route->Route(['get'], '/profile', "./contents/includes/profilePage.php");

    $route->Route(['post'], '/testing', "./contents/includes/testingPage.php");

    $route->Route(['get'], '/shop', "./contents/includes/shopPage.php");
    $route->Route(['get'], '/shop-inventory', "./contents/includes/inventoryItemsPage.php");
    $route->Route(['get'], '/shop-items', "./contents/includes/shopItemsPage.php");
    $route->Route(['get'], '/shop-gacha', "./contents/includes/gachaItemsPage.php");
    $route->Route(['post'], '/shop-buy-item', "./contents/includes/shopBuyItem.php");
    $route->Route(['post'], '/editSubject', "./php/editSubject.php");
    $route->Route(['post'], '/edit-subject', "./contents/includes/editSubject.php");


    $route->Route(['get'], '/last-page', "./contents/includes/pageSession.php");
    $route->Route(['get'], '/logout', "./php/logout.php");




    $route->Route(['post'], '/quiz-result', "./contents/includes/quizResult.php");


    // Any code must be above this.
    echo $route->Dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>