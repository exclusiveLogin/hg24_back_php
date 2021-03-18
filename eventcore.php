<?php
/**
 * Created by PhpStorm.
 * User: SavinSV
 * Date: 28.06.16
 * Time: 8:50
 */

require_once "dbsetting_n_connect.php";


//-----------answer----------------------------
$answer = array("error" => true,"msg" => "no data", "data" => false,q => array());
//-----------Deleter---------------------------
/*
 * Получаем все записи с for=once и интервалом более 1 минуты
 * далее формируем цикл где перебираем ID и удаляем из БД
 * */
$query = "DELETE FROM `events` WHERE `datetime` < ADDDATE(NOW(),INTERVAL -1 MINUTE) AND `for`=\"once\"";
$mysql->query($query);
//-----------Setter---------------------------
if($_GET["add"] && $_GET["add_title"] && $_GET["add_desc"]){
    $title = $_GET["add_title"];
    $description = $_GET["add_desc"];
    if($_GET["add_status"]){
        $status = $_GET["add_status"];
    }
    else{
        $status = "ok";
    }

    /*if($_GET["add_notify"]){
        $notify = 1;
    }
    else{
        $notify = 0;
    }*/
    $notify = 1;

    if($_GET["for"] && gettype($_GET["for"])=="array"){
        foreach($_GET["for"] as $key => $user){
            //ловим почту юзера
            $query = "SELECT * FROM `users` WHERE `login`=\"$user\"";
            $result = $mysql->query($query);
            $row = $result->fetch_assoc();
            $result = mail($row["email"],$title,$description);
            //-----------------
			if($_GET["img"]){
				$img = $_GET["img"];
				$query = "INSERT INTO `events` (`for`,`title`,`desc`,`status`,`notify`,`img`) VALUES (\"$user\",\"$title\",\"$description\",\"$status\",$notify,\"$img\")";
			}else{
				$query = "INSERT INTO `events` (`for`,`title`,`desc`,`status`,`notify`) VALUES (\"$user\",\"$title\",\"$description\",\"$status\",$notify)";
			}
            //echo $query."<br>";
            array_push($answer["q"],$query);
            $mysql->query($query);
            $answer["error"] = false;
        }
    }else{
        //ловим почту юзера
        $query = "SELECT * FROM `users` WHERE `login`=\"$user\"";
        $result = $mysql->query($query);
        $row = $result->fetch_assoc();
        $result = mail($row["email"],$title,$description);
        //-----------------
        $query = "INSERT INTO `events` (`for`,`title`,`desc`,`status`,`notify`) VALUES (\"once\",\"$title\",\"$description\",\"$status\",$notify)";
        //echo $query."<br>";
        array_push($answer["q"],$query);
        $mysql->query($query);
        $answer["error"] = false;
    }
}
//-----------Getter---------------------------
if($_GET["getlast"]){
    if($_GET["getfor"]){
        $tmp_user = $_GET["getfor"];
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
