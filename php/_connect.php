<?php

// Composer autoloader required for Dotenv.
require __DIR__ . "/../vendor/autoload.php";

class inquizitiveDB
{
    // Database credentials.
    private $host;
    private $username;
    private $password;
    private $dbname;

    public $connect;
    
    public function __construct()
    {
        // Loading .env file variables.
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
        $dotenv->load();

        // Assign database credentials from env file.
        $this->host = $_ENV["DATABASE_HOSTNAME"];
        $this->username = $_ENV["DATABASE_USERNAME"];
        $this->password = $_ENV["DATABASE_PASSWORD"];
        $this->dbname = $_ENV["DATABASE_NAME"];

        // Create database connection.
        $this->connect = mysqli_connect($this->host,
                                        $this->username,
                                        $this->password,
                                        $this->dbname);
        
        if (!$this->connect)
        {
            die("Connection to database has failed!");
        }
    }

    // Executes a query with prepared statements to prevent SQL injection.
    public function Query($SQL, $params = [])
    {
        $stmt = mysqli_prepare($this->connect, $SQL);

        if (count($params) > 0)
        {
            // Binding all parameters as strings.
            $types = str_repeat("s", count($params));
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        }
        else
        {
            $result = mysqli_query($this->connect, $SQL);
        }

        // Clears any leftover result sets from previous queries.
        // Fixes "Commands out of sync" errors.
        while ($this->connect->more_results() && $this->connect->next_result()) 
        {
            // Store current result set as if it exists.
            if ($resultSet = $this->connect->store_result()) 
            {
                // Free the memory associated with the result set.
                $resultSet->free();
            }
        }

        return $result;
    }

    // Automatically close database connection.
    public function __destruct()
    {
        mysqli_close($this->connect);
    }
}