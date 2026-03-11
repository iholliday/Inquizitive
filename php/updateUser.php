<?php
require_once __DIR__ . "/_connect.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
    exit;
}

if (!isset($_SESSION['userUUID'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You are not logged in'
    ]);
    exit;
}

$action = $_POST['action'] ?? '';
$userUUID = $_SESSION['userUUID'];

$db = new inquizitiveDB();
$conn = $db->connect;

function clearStoredResults($conn) {
    while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
        $result = mysqli_store_result($conn);
        if ($result) {
            mysqli_free_result($result);
        }
    }
}

try {
    if ($action === 'updateEmail') {
        $email = trim($_POST['email'] ?? '');

        if ($email === '') {
            throw new Exception('Email is required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address');
        }

        $checkStmt = $conn->prepare("SELECT userUUID FROM `user` WHERE email = ? AND userUUID != ?");
        $checkStmt->bind_param("ss", $email, $userUUID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $checkStmt->close();
            throw new Exception('That email is already in use');
        }

        $checkStmt->close();

        $stmt = $conn->prepare("CALL UpdateUser(?, ?, ?)");
        $nullPassword = null;
        $stmt->bind_param("sss", $userUUID, $email, $nullPassword);

        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception('Failed to update email');
        }

        $stmt->close();
        clearStoredResults($conn);

        $_SESSION['email'] = $email;

        echo json_encode([
            'status' => 'success',
            'message' => 'Email updated successfully',
            'email' => $email
        ]);
        exit;
    }

    if ($action === 'updatePassword') {
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            throw new Exception('All password fields are required');
        }

        if (strlen($newPassword) < 8) {
            throw new Exception('New password must be at least 8 characters long');
        }

        if ($newPassword !== $confirmPassword) {
            throw new Exception('New passwords do not match');
        }

        $stmt = $conn->prepare("SELECT `password` FROM `user` WHERE userUUID = ?");
        $stmt->bind_param("s", $userUUID);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            throw new Exception('User not found');
        }

        if (!password_verify($currentPassword, $user['password'])) {
            throw new Exception('Current password is incorrect');
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateStmt = $conn->prepare("CALL UpdateUser(?, ?, ?)");
        $nullEmail = null;
        $updateStmt->bind_param("sss", $userUUID, $nullEmail, $newPasswordHash);

        if (!$updateStmt->execute()) {
            $updateStmt->close();
            throw new Exception('Failed to update password');
        }

        $updateStmt->close();
        clearStoredResults($conn);

        echo json_encode([
            'status' => 'success',
            'message' => 'Password updated successfully'
        ]);
        exit;
    }

    throw new Exception('Invalid action');

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
?>