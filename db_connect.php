<?php

$host = "localhost";
$db = "sirb_db";

try {

    $connectionString = "mysql:host=$host;port=8889;dbname=$db";
    $user = "root";
    $pass = "root";
    $pdo = new PDO($connectionString, $user, $pass);
    
    
    // error & fetch mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $ex) {
    die($ex->getMessage());
}
