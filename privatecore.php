<?php
include_once "dbsetting.php";
$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno){
    die('{"errors":true,"errormsg":"error db":"'.$mysql->connect_error.'"}');
}
$mysql->query("SET NAMES 'UTF8';");


if(!$_GET["loged"]){
    $ip = $_SERVER["REMOTE_ADDR"];
    $token = $_GET["token"];
    $user = $_GET["user"];

    $user_agent = $_GET["user_agent"];
    $geo_status = $_GET["geo_status"];


    $geo_alt = $_GET["geo_alt"];
    $geo_lat = $_GET["geo_lat"];
    $geo_lon = $_GET["geo_lon"];
    $geo_accuracy = $_GET["geo_accuracy"];

    $city = $_GET["city"];
    $region = $_GET["region"];
    $provider = $_GET["provider"];

    $id_user = 0;

    if($user){
        $query = "SELECT id FROM `users` WHERE `login`=\"$user\"";
        $qr = $mysql->query($query);
        $result = $qr->fetch_assoc();
        if ($result){
            $id_user = $result["id"];
        }
    }

    $query = "INSERT INTO `private_data` (`id_user`,`name_user`,`token`,`lat`,`lon`,`alt`,`ip`,`user_agent`,`accuracy`,`region`,`city`,`provider`)
                              VALUES ($id_user,\"$user\",$token,$geo_lat,$geo_lon,$geo_alt,\"$ip\",\"$user_agent\",$geo_accuracy,\"$region\",\"$city\",\"$provider\")";
    echo $query;
    $mysql->query($query);
}
else{
    $token = $_GET["token"];
    $user = $_GET["user"];
    if($user){
        $query = "SELECT id FROM `users` WHERE `login`=\"$user\"";
        $qr = $mysql->query($query);
        $result = $qr->fetch_assoc();
        if ($result){
            $id_user = $result["id"];
        }
    }
    $query="UPDATE `private_data` SET `id_user`=$id_user,`name_user`=\"$user\" WHERE `token` = $token AND `id` = (SELECT MAX(`id`) FROM (SELECT `id` FROM `private_data` WHERE `token`=$token) AS `premax`)";
    echo $query;
    $mysql->query($query);
}


$mysql->close();

//echo $query;

