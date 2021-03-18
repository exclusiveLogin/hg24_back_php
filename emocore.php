<?php
include_once "dbsetting.php";


$target_user = $_GET['t_user'];
if ($target_user){
    $mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
    if($mysql->connect_errno){
        die('{"errors":true,"errormsg":"error db":"'.$mysql->connect_error.'"}');
    }
    $mysql->query("SET NAMES 'UTF8';");

    //проверка юзера есть ли в базе такой
    $query='SELECT  `id` FROM  `users` WHERE `login` = "'.$target_user.'"';
    $res = $mysql->query($query);
    $row = $res->fetch_assoc();
    if(!$row["id"])die('{"errors":true,"errormsg":"user from query not exists from DB"}');
    $res->free();
    $row = null;
    //----------------------------------------
    //-----определяем последнее настроение----
    $query="SELECT  `value` FROM  `".$target_user."_emo` WHERE id = (SELECT MAX( `id`) FROM `".$target_user."_emo` )";
    $res = $mysql->query($query);
    $row = $res->fetch_assoc();

    if($row){
        $last_emo = $row['value'];
    }
    else{
        die('{"errors":true,"errormsg":"data lastEMO from DB is null"}');
    }
    $res->free();
    $row = null;
    //-----определяем предпоследнее настроение----

    $query="SELECT  `value` FROM  `".$target_user."_emo` WHERE id < (SELECT MAX( `id`) FROM `".$target_user."_emo`  LIMIT 1) ORDER BY `id` DESC";
    $res = $mysql->query($query);

    $row = $res->fetch_assoc();
    if($row){
        $prev_emo = $row['value'];
    }
    else{
        die('{"errors":true,"errormsg":"data prevEMO from DB is null"}');
    }
    $res->free();

    //Если есть сеттер
    if($_GET["setemo"]){
        $newval = (int)$_GET["setemo"];
        $emo_title = $_GET["emo_title"];
        $emo_desc = $_GET["emo_desc"];
        
        
        $query='INSERT INTO `'.$target_user.'_emo` (`value`,`emo_title`,`emo_desc`) VALUES ('.$newval.',"'.$emo_title.'","'.$emo_desc.'")';

        $msg = "Новое настроение ".$newval." для пользователя ".$target_user." установлено ";
        
        $mysql->query($query);
    }
    if($_GET['status_code']){
        $status = $_GET['status_code'];
        $danger = $_GET['danger'];
        $status_msg = $_GET['status_msg'];
        $query='UPDATE `users_act` SET `status_code` = "'.$status.'" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $query='UPDATE `users_act` SET `danger` = '.$danger.' WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $query='UPDATE `users_act` SET `status_msg` = "'.$status_msg.'" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $query='UPDATE `users_act` SET `upd_status` = NOW() WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $dng_msn = "";
        if ($danger=="true"){
            $dng_msn="Опасно";
        }
        else {
            $dng_msn="Не опасно";
        }
        $msg = "Новый статус пользователя ".$target_user." : ".$status." установлен Класс опасности: ".$dng_msn;
    }
    if($_GET['played']){
        $played = $_GET['played'];
        $query='UPDATE `users_act` SET `played` = '.$played.' WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $pl_msn = "";
        if ($played=="true"){
            $pl_msn="Играет";
        }
        else if($played=="false"){
            $pl_msn="Не играет";
        }
        $msg='Игровой статус пользователя('.$target_user.'): '.$pl_msn;
    }    
    if($_GET['o_code']){
        $state = $_GET['o_code'];
        $code_msg = $_GET['code_msg'];
        $query='UPDATE `users_act` SET `o_code` = '.$state.' WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $query='UPDATE `users_act` SET `code_msg` = "'.$code_msg.'" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $code_msn = "";
        if ($state=="true"){
            $code_msn="установил";
        }
        else if($state=="false"){
            $code_msn="снял";
            $query='UPDATE `users_act` SET `code_msg` = "" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
            $mysql->query($query);
        }
        $msg='Пользователь '.$target_user.' '.$code_msn.' статус Оранжевый код';
    }
    if($_GET['r_code']){
        $state = $_GET['r_code'];
        $code_msg = $_GET['code_msg'];
        $query='UPDATE `users_act` SET `r_code` = '.$state.' WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $query='UPDATE `users_act` SET `code_msg` = "'.$code_msg.'" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $code_msn;
        if ($state=="true"){
            $code_msn="установил";
        }
        else if($state=="false"){
            $code_msn="снял";
            $query='UPDATE `users_act` SET `code_msg` = "" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
            $mysql->query($query);
        }
        $msg='Пользователь '.$target_user.' '.$code_msn.' статус Красный код';
    }
    if($_GET['g_code']){
        $state = $_GET['g_code'];
        $code_msg = $_GET['code_msg'];
        //$query='UPDATE `users_act` SET `r_code` = '.$state.' WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        //$mysql->query($query);
        $query='UPDATE `users_act` SET `code_msg` = "'.$code_msg.'" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
        $mysql->query($query);
        $code_msn;
        if ($state=="true"){
            $code_msn="установил";
        }
        else if($state=="false"){
            $code_msn="снял";
            $query='UPDATE `users_act` SET `code_msg` = "" WHERE `id_user`=(SELECT `id` FROM `users` WHERE `login`="'.$target_user.'")';
            $mysql->query($query);
        }
        $msg='Пользователь '.$target_user.' '.$code_msn.' новый статус';
    }


    $mysql->query("SET time_zone = '+00:00'");

    $query="SELECT DATE_FORMAT(`datetime`,'%Y,%m,%d,%H,%i,%S') AS `datetime`,`value`,`emo_title`,`emo_desc` FROM `".$target_user."_emo` ORDER BY `datetime` ASC";
    $res = $mysql->query($query);

    $row = $res->fetch_assoc();
    $arr = array();
    while ($row){
        $num_arr = array_push($arr,[$row['datetime'],$row['value'],$row['emo_title'],$row['emo_desc']]);
        //echo "var dump DT:".$row['datetime']."  -VAL: ".$row['value']."--new_num:".$num_arr."<br>";
        $row = $res->fetch_assoc();
    }
    //echo var_dump($arr);
    $res->free();
    $mysql->close();
    //echo "<br>";
    echo '{"last_emo":'.$last_emo.',"prev_emo":'.$prev_emo.',"trend":'.json_encode($arr,JSON_UNESCAPED_UNICODE).',"msg":"'.$msg.'"}';
}
else{
    die('{"errors":true,"errormsg":"target user is null"}');
}



