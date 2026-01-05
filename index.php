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

    // Allows for images and stylesheets to load from public folder.
    $route->AddDir('/public', "./contents/public");

    // Routes required for all pages.
    $route->Route(['post'], '/auth', "./php/auth.php");


    // Any code must be above this.
    echo $route->Dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
?>