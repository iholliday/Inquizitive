<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') 
    {
        http_response_code(403);
        echo "Direct access to this page is forbidden, please enter through the dashboard.";
        exit;
    }
?>