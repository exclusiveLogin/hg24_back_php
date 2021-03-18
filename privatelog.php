<?php

require_once "dbsetting.php";
$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno){
    die('{"errors":true,"errormsg":"error db":"'.$mysql->connect_error.'"}');
}
$mysql->query("SET NAMES 'UTF8';");
$mysql->query("SET time_zone = '+04:00'");

$query = "SELECT * FROM `private_data` ORDER BY `id` DESC LIMIT 20";
$result = $mysql->query($query);
$row = $result->fetch_assoc();
echo "[";
while($row){
    echo json_encode($row);
    $row = $result->fetch_assoc();
    if($row)echo ",";
}
echo "]";
$mysql->close();
