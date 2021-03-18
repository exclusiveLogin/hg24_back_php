<?php
include_once "dbsetting.php";
$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno)die("error db:".$mysql->connect_error);

$query = "SELECT * FROM `users`";


$res = $mysql->query($query);
$arr4json = array();
$row = $res->fetch_assoc();
while($row) {
    array_push($arr4json, $row);
    $row = $res->fetch_assoc();
}

echo json_encode($arr4json);