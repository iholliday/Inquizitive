<?php
require_once __DIR__ . "/_connect.php";
header("Content-Type: application/json; charset=utf-8");

function out($ok, $msg, $extra = []) {
    echo json_encode(array_merge(["ok" => $ok, "message" => $msg], $extra));
    exit;
}

try {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    out(false, "Invalid request method.");
    }

    $subjectTitle = trim($_POST["subjectName"] ?? "");
    $subjectDescription = trim($_POST["subjectDescription"] ?? "");

    // Validation
    if ($subjectTitle === "") {
    out(false, "Please enter a subject name.");
    }
    if (mb_strlen($subjectTitle) > 100) {
    out(false, "Subject name must be 100 characters or less.");
    }
    if ($subjectDescription !== "" && mb_strlen($subjectDescription) > 100) {
    out(false, "Subject description must be 100 characters or less.");
    }
    $subjectDescription = ($subjectDescription === "") ? null : $subjectDescription;

    // Check if session has been started, if not starts it
    if (session_status() === PHP_SESSION_NONE) session_start();

    $authorUUID = $_SESSION["userUUID"] ?? "";
    if ($authorUUID === "") {
    out(false, "Unauthorized. Please log in again.");
    }

    $db = new inquizitiveDB();
    $conn = $db->connect;

    $stmt = mysqli_prepare($conn, "CALL CreateSubject(?, ?, ?)");
    if (!$stmt) {
    out(false, "Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sss", $subjectTitle, $subjectDescription, $authorUUID);

    try {
    mysqli_stmt_execute($stmt);
    } catch (mysqli_sql_exception $e) {
    out(false, $e->getMessage());
    } finally {
    mysqli_stmt_close($stmt);

    while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
        $junk = mysqli_store_result($conn);
        if ($junk) mysqli_free_result($junk);
    }
    }

    out(true, "Subject created successfully.");

} catch (Throwable $e) {
    http_response_code(500);
    out(false, "Server error: " . $e->getMessage());
}