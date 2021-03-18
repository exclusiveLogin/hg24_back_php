<?php
require_once "dbsetting_n_connect.php";
//-----------answer----------------------------
$answer = array("error" => true,"msg" => "no data", "data" => false);
//-----------Getter---------------------------
if($_POST["getlast"] || $_GET["getlast"]){
    if($_POST["getfor"] || $_GET["getfor"]){
        if(isset($_POST["getfor"]))$tmp_user = $_POST["getfor"];
        if(isset($_GET["getfor"]))$tmp_user = $_GET["getfor"];
        $query = "SELECT * FROM `events` WHERE `for`=\"$tmp_user\"";
        $result = $mysql->query($query);
        $row = $result->fetch_assoc();
        $tmp_last_ev = array();
        while ($row){
            array_push($tmp_last_ev,$row);
            $row = $result->fetch_assoc();
        }
        $answer["data"] = json_encode($tmp_last_ev);
        $answer["error"] = false;
        $query = "DELETE FROM `events` WHERE `for`=\"$tmp_user\"";
        $mysql->query($query);
    }else{
        $answer["msg"] = "запрошенный пользователь не найден в БД";
    }
}
echo json_encode($answer);
