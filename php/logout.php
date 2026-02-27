<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Start the session if it's not already started.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Destroy the session.
    session_unset();
    session_destroy(); 

    // Send a JSON response and exit statement.
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
    exit();
?>