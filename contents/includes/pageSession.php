<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['lastPage'])) {
    $_SESSION['lastPage'] = $_POST['lastPage'];
}

if (isset($_SESSION['lastPage'])) {
    echo $_SESSION['lastPage'];
}
?>