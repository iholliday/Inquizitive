<?php

if(isset($_POST['lastpage'])){
    $lastPage = $_POST['lastpage'];
    $_SESSION['lastPage'] = $lastPage;
}

if(isset($_SESSION['lastPage']))
{
    echo $_SESSION['lastPage'];
}

?>