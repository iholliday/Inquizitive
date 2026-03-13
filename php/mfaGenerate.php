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

// Check to see session found.
if (!isset($_SESSION['userUUID'])) 
{
    echo json_encode(['status' => 'error', 'message' => 'No user session found.']);
    exit;
}

$userUUID = $_SESSION['userUUID'];
$password = $_POST['password'] ?? '';

// If password isn't inputted, return error.
if (!$password) 
{
    echo json_encode(['status' => 'error', 'message' => 'Password required.']);
    exit;
}

// Fetch hashed password from DB and check against it.
$db = new inquizitiveDB();
$stmt = $db->Query("CALL GetPasswordByUUID(?)", [$userUUID]);
if (!$stmt) 
{
    echo json_encode(['status' => 'error', 'message' => 'Database query failed.']);
    exit;
}

// Password incorrect.
$user = mysqli_fetch_assoc($stmt);
if (!$user || !password_verify($password, $user['password'])) 
{
    echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
    exit;
}

// Generating secret.
$mfa = new TwoFactorAuth('Inquizitive');
$secret = $mfa->createSecret();

// Storing secret in session temporarily.
$_SESSION['mfaTempSecret'] = $secret;

// Generating QR image.
$label = 'Inquizitve: '. $_SESSION['email'];
$qr = $mfa->getQRCodeImageAsDataUri($label, $secret);

echo json_encode(['status' => 'success', 'qr' => $qr, 'secret' => $secret]);
exit;
?>