<?php

require_once "dbsetting_n_connect.php";
$mysql->query("SET time_zone = '+00:00'");

    if($_GET["get_messages"]){
        if(isset($_GET["from"]) && isset($_GET["to"])){
            $_from = $_GET["from"];
            $_to = $_GET["to"];
            $q = "SELECT * FROM `private_messages` WHERE `from_msg` = '$_from' AND `to_msg` = '$_to' ORDER BY `id`";

            $resource = $mysql->query($q);
            $json_return = array();
            
            ($resource) ? $row = $row = $resource->fetch_assoc() : $row = false;

            while($row){
                array_push($json_return, $row);
                $row = $resource->fetch_assoc();
            }

            echo json_encode($json_return);
        }
    }