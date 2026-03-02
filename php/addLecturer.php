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

  $firstName = trim($_POST["firstName"] ?? "");
  $lastName  = trim($_POST["lastName"] ?? "");
  $email     = trim($_POST["email"] ?? "");
  $password  = $_POST["password"] ?? "";
  $confirm   = $_POST["confirmPassword"] ?? "";

  if ($firstName === "" || $lastName === "" || $email === "" || $password === "" || $confirm === "") {
    out(false, "Please fill in all fields.");
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    out(false, "Please enter a valid email address.");
  }

  if ($password !== $confirm) {
    out(false, "Passwords do not match.");
  }

  $passwordHash = password_hash($password, PASSWORD_DEFAULT);

  $db = new inquizitiveDB();
  $conn = $db->connect;

  $stmt = mysqli_prepare($conn, "CALL AddLecturer(?, ?, ?, ?)");
  if (!$stmt) {
    out(false, "Database error: " . mysqli_error($conn));
  }

  mysqli_stmt_bind_param($stmt, "ssss", $email, $firstName, $lastName, $passwordHash);

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

  out(true, "Lecturer created successfully.");

} catch (Throwable $e) {
  http_response_code(500);
  out(false, "Server error: " . $e->getMessage());
}
