<?php
include_once "dbsetting.php";
function windparser($deg){
    $wind = ["wind_direction_deg" => round($deg,0),
        "wind_direction_str_en" => "NA",
        "wind_direction_str_ru" => "NA"];

    if($deg>0 && $deg<11.25){
        $wind["wind_direction_str_en"] = "N";
        $wind["wind_direction_str_ru"] = "С";
    }
    else if($deg>11.25 && $deg<33.75){
        $wind["wind_direction_str_en"] = "NNE";
        $wind["wind_direction_str_ru"] = "ССВ";
    }
    else if($deg>33.75 && $deg<56.25){//45
        $wind["wind_direction_str_en"] = "NE";
        $wind["wind_direction_str_ru"] = "СВ";
    }
    else if($deg>56.25 && $deg<78.75){
        $wind["wind_direction_str_en"] = "ENE";
        $wind["wind_direction_str_ru"] = "ВСВ";
    }
    else if($deg>78.75 && $deg<101.25){//90
        $wind["wind_direction_str_en"] = "E";
        $wind["wind_direction_str_ru"] = "В";
    }
    else if($deg>101.25 && $deg<123.75){
        $wind["wind_direction_str_en"] = "ESE";
        $wind["wind_direction_str_ru"] = "ВЮВ";
    }
    else if($deg>123.75 && $deg<146.25){//135
        $wind["wind_direction_str_en"] = "SE";
        $wind["wind_direction_str_ru"] = "ЮВ";
    }
    else if($deg>146.25 && $deg<168.75){
        $wind["wind_direction_str_en"] = "SSE";
        $wind["wind_direction_str_ru"] = "ЮЮВ";
    }
    else if($deg>168.75 && $deg<191.25){//180
        $wind["wind_direction_str_en"] = "S";
        $wind["wind_direction_str_ru"] = "Ю";
    }
    else if($deg>191.25 && $deg<213.75){
        $wind["wind_direction_str_en"] = "SSW";
        $wind["wind_direction_str_ru"] = "ЮЮЗ";
    }
    else if($deg>213.75 && $deg<236.25){//225
        $wind["wind_direction_str_en"] = "SW";
        $wind["wind_direction_str_ru"] = "ЮЗ";
    }
    else if($deg>236.25 && $deg<258.75){
        $wind["wind_direction_str_en"] = "WSW";
        $wind["wind_direction_str_ru"] = "ЗЮЗ";
    }
    else if($deg>258.75 && $deg<281.25){//270
        $wind["wind_direction_str_en"] = "W";
        $wind["wind_direction_str_ru"] = "З";
    }
    else if($deg>281.25 && $deg<303.75){
        $wind["wind_direction_str_en"] = "WWN";
        $wind["wind_direction_str_ru"] = "ЗЗС";
    }
    else if($deg>303.75 && $deg<326.25){//315
        $wind["wind_direction_str_en"] = "NW";
        $wind["wind_direction_str_ru"] = "СЗ";
    }
    else if($deg>326.25 && $deg<348.75){
        $wind["wind_direction_str_en"] = "NNW";
        $wind["wind_direction_str_ru"] = "ССВ";
    }
    else if($deg>348.75 && $deg<360){//360
        $wind["wind_direction_str_en"] = "N";
        $wind["wind_direction_str_ru"] = "С";
    }
    else{
        $wind["wind_direction_str_en"] = "ERR";
        $wind["wind_direction_str_ru"] = "ERR";
    }

    return $wind;
}
$file_z = $_SERVER['DOCUMENT_ROOT']."/temp/z_plane.json";
$tmp_z = file_get_contents($file_z);
$tmp_z = json_decode($tmp_z);

$file_w = $_SERVER['DOCUMENT_ROOT']."/temp/weather.json";
$tmp_w = file_get_contents($file_w);
$tmp_w = json_decode($tmp_w);

if($tmp_w->wind->speed){
    $tmp_current_speed = $tmp_w->wind->speed;
}
if($tmp_w->wind->deg){
    $tmp_dir_deg = round($tmp_w->wind->deg);
    $tmp_wind_arr = windparser($tmp_dir_deg);
    $tmp_dir_str_ru = $tmp_wind_arr["wind_direction_str_ru"];
    $tmp_dir_str_en = $tmp_wind_arr["wind_direction_str_en"];
}

if($tmp_z->widget){
    foreach ($tmp_z->widget as $name => $key){
        if($tmp_z->widget[$name]->name=="wind"){
            $tmp_real_speed = $tmp_z->widget[$name]->value;
        }
    }
}



if($tmp_real_speed && $tmp_current_speed && $tmp_dir_deg && $tmp_dir_str_en && $tmp_dir_str_ru){
    //echo "Yes its fine all data exists";
    $mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
    if($mysql->connect_errno){
        die('{"errors":true,"errormsg":"error db":"'.$mysql->connect_error.'"}');
    }
    $mysql->query("SET NAMES 'UTF8';");
    $query = "INSERT INTO `windrose`(`dir_str_ru`,`dir_str_en`,`dir_deg`,`real_speed`,`current_speed`) VALUES 
(\"$tmp_dir_str_ru\",\"$tmp_dir_str_en\",$tmp_dir_deg,$tmp_real_speed,$tmp_current_speed)";
    $result = $mysql->query($query);
    echo $query;
    //echo "<br>";
    //echo "result:".$result;
    echo '{"error":false,"error_msg":"No errors, data added into db"}';
}
else{
    echo '{"error":true,"error_msg":"No full data entired"}';
}