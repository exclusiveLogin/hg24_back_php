<?php
include_once "dbsetting.php";
header("Access-Control-Allow-Origin:*");

$newCode = false;

if(isset($_GET['global_status'])){
    $newCode = $_GET['global_status'] | '';
}

$newGlobalMessage = '';

if(isset($_GET['global_message'])){
    $newGlobalMessage = $_GET['global_message'] | '';
}


$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);

if($newCode){
    $query = "INSERT INTO `global`(`global_code`,`message`) VALUES('$newCode', '$newGlobalMessage')";
    $mysql->query($query);
}



$query = 'SELECT * FROM `global` ORDER BY `id` DESC LIMIT 1';

$res = $mysql->query($query);

$row = $res->fetch_assoc();
if(!$row) {
    die('{}');
}

echo json_encode($row);