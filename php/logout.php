<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Start the session if it's not already started.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Set response to JSON.
    header('Content-Type: application/json');

    // Destroy the session.
    session_unset();
    session_destroy(); 

    // Send a JSON response and exit statement.
    echo json_encode(['status' => 'success']);
    exit();
?>