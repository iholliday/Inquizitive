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

// Check to see if temp MFA secret exists in the session.
if(!isset($_SESSION['mfaTempSecret']))
{
    echo json_encode(['status'=>'error','message'=>'No MFA found.']);
    exit;
}

// Get code submitted by user.
$code = trim($_POST['code']);
$secret = $_SESSION['mfaTempSecret'];
$mfa = new TwoFactorAuth('Inquizitive');

if(!$mfa->verifyCode($secret, $code))
{
    echo json_encode(['status'=>'error','message'=>'Invalid code.']);
    exit;
}

// Store verified secret in database
$db = new inquizitiveDB();
$db->Query("CALL SetTotpSecret(?,?)", [$_SESSION['userUUID'], $secret]);

// Remove temp secret and return success message.
unset($_SESSION['mfaTempSecret']);
$_SESSION['mfaEnabled'] = 1;
echo json_encode(['status'=>'success','message'=>'MFA successfully added!']);
exit; 