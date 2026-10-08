<?php
class dbconnect
{
    function connect()
    {
        $host = EnvLoader::get('DB_HOST');
        $username = EnvLoader::get('DB_USERNAME');
        $password = EnvLoader::get('DB_PASSWORD');
        $database = EnvLoader::get('DB_NAME');
        $connection = mysqli_connect($host, $username, $password, $database);
        return $connection;
    }
}
?>
