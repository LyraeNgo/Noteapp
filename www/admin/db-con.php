<?php
    define("HOST","mysql-server");
    define("USER","root");
    define("PASS","root");
    define("DB","noteapp");

    function create_connection(){

        $conn = new mysqli(HOST, USER, PASS, DB);
        if ($conn->connect_error) {
            die("". $conn->connect_error);
        }
        return $conn;
    }
?>