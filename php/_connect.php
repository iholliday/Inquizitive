<?php

require __DIR__ . "/../vendor/autoload.php";

class inquizitiveDB
{
    private $host;
    private $username;
    private $password;
    private $dbname;

    public $connect;
    
    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();

        $this->host = $_ENV["DATABASE_HOSTNAME"];
        $this->username = $_ENV["DATABASE_USERNAME"];
        $this->password = $_ENV["DATABASE_PASSWORD"];
        $this->dbname = $_ENV["DATABASE_NAME"];

        $this->connect = mysqli_connect($this->host,
                                        $this->username,
                                        $this->password,
                                        $this->dbname);
        
        if (!$this->connect)
        {
            die("Connection to database has failed!");
        }
    }

    public function Query($SQL, $params = [])
    {
        $stmt = mysqli_prepare($this->connect, $SQL);

        if (count($params) > 0)
        {
            $types = str_repeat("s", count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        }
        else
        {
            $result = mysqli_query($this->connect, $SQL);
        }


        return $result;
    }

    public function __destruct()
    {
        mysqli_close($this->connect);
    }
}