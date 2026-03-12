<?php 
// Block direct access, only loads via AJAX.
require_once ("./php/blockDirectAccess.php");

// Required files.
require_once ("_connect.php");
header('Content-Type: application/json');

// Start session if not already started.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check user is logged in and has MFA enabled.
if (!isset($_SESSION['userUUID']) || empty($_SESSION['mfaEnabled']) || $_SESSION['mfaEnabled'] != 1) 
{
    echo json_encode(['status'=>'error','message'=>'You do not meet the MFA removal requirements.']);
    exit;
}

// Get password from AJAX.
$password = trim($_POST['password'] ?? '');
if (empty($password)) {
    echo json_encode(['status'=>'error','message'=>'Password is required.']);
    exit;
}

// Create DB instance.
$db = new inquizitiveDB();

// Verify password.
$userUUID = $_SESSION['userUUID'];
$stmt = $db->Query("CALL GetPasswordByUUID(?)", [$userUUID]);
if (!$stmt) 
{
    echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
    exit;
}

$user = mysqli_fetch_assoc($stmt);
if (!$user || !password_verify($password, $user['password'])) 
{
    echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
    exit;
}

// Disables MFA, removes backup code, removes TOTP secret.
$db->Query("CALL RemoveMfa(?)", [$userUUID]);

// Update session.
$_SESSION['mfaEnabled'] = 0;

// Return success message.
echo json_encode(['status'=>'success','message'=>'MFA successfully removed.']);
exit;