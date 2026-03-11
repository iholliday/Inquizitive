<?php 

// If the page is accessed directly through the URL bar, block access. Only allows access if loaded via AJAX.
require_once ("./php/blockDirectAccess.php");

// Required files.
require_once ("_connect.php");
require_once __DIR__ . "/../vendor/autoload.php";
use RobThree\Auth\TwoFactorAuth;
header('Content-Type: application/json');

// Check to see if session has started, if not, start one.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check to see temporary MFA session found.
if (!isset($_SESSION['mfaUser'])) {
    echo json_encode(['status' => 'error', 'message' => 'No MFA session found.']);
    exit;
}

// Get code submitted by user.
if (isset($_POST['code'])) 
{
    $code = trim($_POST['code']);
} 
else 
{
    $code = '';
}
if (empty($code)) 
{
    echo json_encode(['status' => 'error', 'message' => 'Please enter a code.']);
    exit;
}

// Get user data from the database.
$userUUID = $_SESSION['mfaUser'];
$db = new inquizitiveDB();
$stmt = $db->Query("CALL GetUserByUserUUID(?)", [$userUUID]);
$user = mysqli_fetch_assoc($stmt);

// If user not found, display error.
if (!$user) 
{
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    exit;
}

// Initialise TOTP.
$mfa = new TwoFactorAuth('Inquizitive');

// Flag to track MFA verification.
$mfaPassed = false;

// Verify TOTP/backup codes.
if (!empty($user['totpSecret']) && strlen($code) === 6 && $mfa->verifyCode($user['totpSecret'], $code)) {
    $mfaPassed = true;
    $message = "Welcome to the dashboard!";
}
elseif (!empty($user['backupCode']) && password_verify($code, $user['backupCode'])) 
{
    // Invalidate the backup code after use
    $db->Query("CALL SetBackupCode(?, ?)", [$userUUID, NULL]);
    $mfaPassed = true;
    $message = "Backup code used!\nWelcome to the dashboard!";
}

// If user gets code wrong, give error message.
if (!$mfaPassed) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid MFA code.']);
    exit;
}

// MFA passed → set full session
unset($_SESSION['mfaUser']);
$_SESSION['userUUID'] = $user['userUUID'];
$_SESSION['firstName'] = $user['firstName'];
$_SESSION['lastName'] = $user['lastName'];
$_SESSION['email'] = $user['email'];
$_SESSION['accessLevel'] = $user['accessLevel'];
$_SESSION['mfaEnabled'] = $user['mfaEnabled'];
$_SESSION['avatar'] = $user['avatar'];
$_SESSION['userCreationDate'] = $user['userCreationDate'];

// If password correct, reset attempts.
$_SESSION["spamChecker"] = 0;
$_SESSION["lastCheck"] = new DateTimeImmutable();

// Update last login time on database.
$currentTime = date("Y-m-d H:i:s");
$db->Query("CALL UpdateLastLogin(?, ?)", [$user['userUUID'], $currentTime]);

// Success response.
echo json_encode(['status' => 'success', 'message' => $message]);
exit;
?>