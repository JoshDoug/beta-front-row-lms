<?php
try {
    $dsn = 'mysql:host=localhost;dbname=frontRowLMS';
    $db = new PDO($dsn, 'root', 'root');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    $error = $e->getMessage();
}