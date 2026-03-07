<?php 
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Require the connection file.
    require_once ("_connect.php");

    // Set response to JSON.
    header('Content-Type: application/json');

    // Make sure user is logged in
    if (!isset($_SESSION['userUUID'])) 
    {
        echo json_encode(['status' => 'error', 'message' => 'No active session found.']);
        exit;
    }

    // Check to see if session has started, if not, start one.
    if (session_status() === PHP_SESSION_NONE) 
    {
        session_start();
    }
    
    // Setting characters to chose from and max index for array.
    $chars = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
    $code = "";
    $maxIndex = strlen($chars) - 1;
    $userUUID = $_SESSION['userUUID'];

    // Loops 16 times to generate each character.
    for ($iCount = 0; $iCount < 16; $iCount++)
    {
        $code .= $chars[random_int(0, $maxIndex)];
    }

    // Hash code prior to inserting into DB.
    $hashedCode = password_hash($code, PASSWORD_BCRYPT);

    // Store hashed code in database.
    $db = new InquizitiveDB();
    $db->Query("CALL SetBackupCode(?, ?)", [$userUUID, $hashedCode]);

    // Return plaintext code for user to see.
    echo json_encode(['status' => 'success', 'backupCode' => $code]);
    exit;
