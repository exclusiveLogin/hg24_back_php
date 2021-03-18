<?php
require_once "dbsetting.php";
$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno){
    die('{"errors":true,"errormsg":"error db":"'.$mysql->connect_error.'"}');
}
$mysql->query("SET NAMES 'UTF8';");

if($_GET["last_private_user"]){
    $t_user = $_GET["last_private_user"];
    $query = "SELECT * FROM `private_data` WHERE name_user = \"$t_user\" AND
        id = (SELECT MAX(id)FROM (SELECT id FROM `private_data` WHERE name_user = \"$t_user\" AND accuracy<>\"0\")AS tmp)";
    //echo $query;
    $result = $mysql->query($query);
    $row = $result->fetch_assoc();
    while($row){
        echo json_encode($row);
        $row = $result->fetch_assoc();
        if($row)echo ",";
    }
}else{
    die('{"errormsg":"No user in response","errors":true}');
}


$mysql->close();