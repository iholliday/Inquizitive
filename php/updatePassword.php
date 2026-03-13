<?php
    // If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
    require_once ("./php/blockDirectAccess.php");

    // Required connection file.
    require_once("_connect.php");
    header('Content-Type: application/json');

    // If no session, start one.
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Getting POST data.
    $token = $_POST['token'] ?? '';
    $password = $_POST['txtPassword'] ?? '';
    $confirmPassword = $_POST['txtConfirmPassword'] ?? '';

    // If either token or password are missing, return error.
    if (!$token || !$password) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
        exit;
    }

    // Server-side check if passwords match.
    if ($password !== $confirmPassword) 
    {
        echo json_encode(['status' => 'error','message' => 'Passwords do not match.']);
        exit;
    }

    // Defining password rules.
    $minLength   = 8;
    $uppercase   = preg_match('@[A-Z]@', $password);
    $lowercase   = preg_match('@[a-z]@', $password);
    $number      = preg_match('@[0-9]@', $password);
    $specialChar = preg_match('@[^\w]@', $password);

    // If password is illegal, send error.
    if(strlen($password) < $minLength || !$uppercase || !$lowercase || !$number || !$specialChar) 
    {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.']);
        exit;
    }

    // Hash password.
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Create DB instance.
    $db = new inquizitiveDB();

    // Deleting expired reset tokens.
    $stmt = $db->Query("CALL DeleteExpiredPasswordResets");

    // Gathering all valid reset tokens.
    $stmt = $db->Query("CALL GetAllPasswordResets");
    $resetRecords = [];
    while ($row = mysqli_fetch_assoc($stmt)) 
    {
        $resetRecords[] = $row;
    }

    // Check token against hashed tokens.
    $found = null;
    foreach ($resetRecords as $record) 
    {
        if (password_verify($token, $record['resetToken'])) 
        {
            $found = $record;
            break;
        }
    }

    // If not found, return error message.
    if (!$found) 
    {
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired token.']);
        exit;
    }

    // Checking token isn't expired.
    if (new DateTime() > new DateTime($found['expiryDate'])) 
    {
        echo json_encode(['status' => 'error', 'message' => 'Token has expired.']);
        exit;
    }

    // Update password.
    $userUUID = $found['userUUID'];
    $db->Query("CALL UpdateUserPassword(?, ?)", [$userUUID, $passwordHash]);

    // Delete used token from DB.
    $resetUUID = $found['resetUUID'];
    $db->Query("CALL DeletePasswordReset(?)", [$resetUUID]);

    echo json_encode(['status' => 'success', 'message' => 'Password updated successfully.']);
    exit;
