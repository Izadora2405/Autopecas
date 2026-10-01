<?php

$host = "localhost";
$port = 3306;
$dbname = "db_autopecas";
$username = "root";
$password = "";

try {

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("SET NAMES utf8");

} catch (PDOException $e) {

    die("Erro de conexão com o banco de dados: " . $e->getMessage());

}
?>