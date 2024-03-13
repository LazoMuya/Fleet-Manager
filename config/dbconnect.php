<?php
    $host = "127.0.0.1:3306";
    $dbname = "tfm_system";
    $username = "system_user";
    $password = "458Italia";

    $mysqli = new mysqli($host, $username, $password, $dbname);

    if ($mysqli -> connect_errno){
        die("Connectio  Error ". $mysqli->connect_error);
    }
    return $mysqli;
?>