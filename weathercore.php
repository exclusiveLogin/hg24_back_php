<?php
include_once ("dbsetting.php");
echo "{";
$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno){
    die('{"errors":true,"errormsg":"error db -'.$mysql->connect_error.'"}');
}
$mysql->query("SET NAMES 'UTF8';");

$path = $_SERVER['DOCUMENT_ROOT']."/temp";
if(!file_exists($path)){
    if(mkdir($path)){
        echo '"path":"created",';
    }else{
        echo '"error":true, "error_desc":"path not created"}';
        die();
    }
}

$w_file = $_SERVER['DOCUMENT_ROOT']."/temp/weather.json";
$f_file = $_SERVER['DOCUMENT_ROOT']."/temp/forecast.json";
$owm_current = "http://api.openweathermap.org/data/2.5/weather?id=484972&appid=69e74215c599f98adce65d87e9fdb41c&lang=ru&units=metric";
$owm_forecast = "http://api.openweathermap.org/data/2.5/forecast?id=484972&appid=69e74215c599f98adce65d87e9fdb41c&lang=ru&units=metric";

//если не было скачанных с owm файлов в tmp
if(!file_exists($w_file)){
    $query = "TRUNCATE TABLE `weather`";
    $res = $mysql->query($query);
    $json_weather = file_get_contents($owm_current);
    if(!$json_weather){
        echo '"error":true, "error_desc":"owm not answered"}';
        die();
    }
    $result = file_put_contents($w_file,$json_weather);
    if(!$result){
        echo '"error":true, "error_desc":"w_file not created"}';
        die();
    }
    //пишем в БД новую запись
    $query = "INSERT INTO `weather`(`status`) VALUES (1)";
    $res = $mysql->query($query);
    echo '"req_cur":"'.$owm_current.'",';
}
if(!file_exists($f_file)){
    $query = "TRUNCATE TABLE `weather`";
    $res = $mysql->query($query);
    $json_forecast = file_get_contents($owm_forecast);
    //echo "forecast:".file_get_contents($owm_forecast);
    if(!$json_forecast){
        echo '"error":true, "error_desc":"owm not answered"}';
        die();
    }
    $result = file_put_contents($f_file,$json_forecast);
    if(!$result){
        echo '"error":true, "error_desc":"f_file not created"}';
        die();
    }
    //пишем в БД новую запись
    $query = "INSERT INTO `weather`(`status`) VALUES (1)";
    $res = $mysql->query($query);
    echo '"req_forecast":"'.$owm_forecast.'",';
}

$query = "SELECT `status` FROM `weather`";
$res = $mysql->query($query);
$row = $res->fetch_assoc();//запрос на статус записи в БД

if(!$row){
    echo '"debug":"db empty",';

    $json_weather = file_get_contents($owm_current);
    $json_forecast = file_get_contents($owm_forecast);

    //-------------------CURRENT--------------------------------------
    if($json_weather){//успешно получены данные current
        $query = "INSERT INTO `weather`(`status`) VALUES (1)";
        $res = $mysql->query($query);
        echo '"weather":'.$json_weather.',';
    }
    else{//если не удалось получить данные с OWM
        $query = "INSERT INTO `weather`(`status`) VALUES (0)";
        $res = $mysql->query($query);
        echo '"error":true, "error_desc":"owm not answered"}';
        die();
    }
    //-------------------FORECAST------------------------------
    if($json_forecast){//успешно получены данные forecast
        $query = "INSERT INTO `weather`(`status`) VALUES (1)";
        $res = $mysql->query($query);
        echo '"forecast":'.$json_forecast.',';
    }
    else{//если не удалось получить данные с OWM
        $query = "INSERT INTO `weather`(`status`) VALUES (0)";
        $res = $mysql->query($query);
        echo '"error":true, "error_desc":"owm not answered"}';
        die();
    }
}
else{
    echo '"debug":"db ok",';
    //проверяем дату и таймаут записи если все ок . читаем Файл и отдаем пользователю.
    //если нет скачиваем файл отдаем пользователю пишем в DB что все ок обновляем таймауты
    $query = "SELECT *,NOW() AS cur_time FROM `weather`";
    $res = $mysql->query($query);
    $row = $res->fetch_assoc();

    if(row){
        $compare = strtotime($row['cur_time']) - strtotime($row['upd']);
        echo '"compare":'.$compare.",";
        //echo "Данные из БД получены обновлены: ".$row['upd'].'<br> Cur:'.$row['cur_time'].'<br> прошло '.$compare."секунд";
        if($compare>300){//качаем файл заново            
            $json_weather = file_get_contents($owm_current);
            $json_forecast = file_get_contents($owm_forecast);
            //-------------------CURRENT--------------------------------
            if($json_weather){//успешно получены данные current
                $query = "UPDATE `weather` SET `status`=1";
                $res = $mysql->query($query);
                $query = "UPDATE `weather` SET `upd`=NOW()";
                $res = $mysql->query($query);
                echo '"weather":'.$json_weather.',';
                file_put_contents($w_file,$json_weather);
            }
            else{//если не удалось получить данные с OWM
                $query = "UPDATE `weather` SET `status`=0";
                $res = $mysql->query($query);
                $query = "UPDATE `weather` SET `upd`=NOW()";
                $res = $mysql->query($query);
                echo '"error":true, "error_desc":"owm not answered"}';
                die();
            }
            //-------------------FORECAST------------------------------
            if($json_forecast){//успешно получены данные forecast
                $query = "UPDATE `weather` SET `status`=1";
                $res = $mysql->query($query);
                $query = "UPDATE `weather` SET `upd`=NOW()";
                $res = $mysql->query($query);
                echo '"forecast":'.$json_forecast.',"error":false';
                file_put_contents($f_file,$json_forecast);
            }
            else{//если не удалось получить данные с OWM
                $query = "UPDATE `weather` SET `status`=0";
                $res = $mysql->query($query);
                $query = "UPDATE `weather` SET `upd`=NOW()";
                $res = $mysql->query($query);
                echo '"error":true, "error_desc":"owm not answered"}';
                die();
            }
            //--------------------------------------------------------
        }
        else{//отдаем старый
            $json_weather = file_get_contents($w_file);
            $json_forecast = file_get_contents($f_file);
            echo '"weather":'.$json_weather.',';
            echo '"forecast":'.$json_forecast.',"error":false';
        }
    }
}
echo "}";
$mysql->close();


