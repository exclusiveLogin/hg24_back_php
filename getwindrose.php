<?php
require_once "dbsetting_n_connect.php";
$mysql->query("SET time_zone = '+00:00'");

$windrose_summary = array("N"=>0,"NNE"=>0,"NE"=>0,"ENE"=>0,"E"=>0,"ESE"=>0,"SE"=>0,"SSE"=>0,"S"=>0,"SSW"=>0,"SW"=>0,"WSW"=>0,
    "W"=>0,"WWN"=>0,"NW"=>0,"NNW"=>0);
$trend_dir_deg = array();
$trend_real_speed = array();
$trend_current_speed = array();
$trend_timestamps = array();

foreach ($windrose_summary as $key=>$elem){
    //echo $key.":".$elem."<br>";
    $query = "SELECT COUNT(*) AS `cnt` FROM `windrose` WHERE `dir_str_en`=\"$key\"";
    //echo $query;
    $result = $mysql->query($query);
    $row = $result->fetch_assoc();
    if($row){
        //echo "Result ".$key.":".$row["cnt"]."<br>";
        $windrose_summary[$key] = (int)$row["cnt"];
    }
}

$query = "SELECT `dir_deg`,`real_speed`,`current_speed`,DATE_FORMAT(`datetime`,'%Y,%m,%d,%H,%i,%S') AS `datetime` FROM `windrose` ORDER BY `id` ASC LIMIT 8760";
$result = $mysql->query($query);
$row = $result->fetch_assoc();
while($row){
    array_push($trend_dir_deg, $row["dir_deg"]);
    array_push($trend_real_speed, $row["real_speed"]);
    array_push($trend_current_speed, $row["current_speed"]);
    array_push($trend_timestamps,$row["datetime"]);
    $row = $result->fetch_assoc();
}

//var_dump($windrose_summary);
echo '{"summary":';
echo json_encode($windrose_summary).',"trend_dir_deg":';
echo json_encode($trend_dir_deg).',"trend_real_speed":';
echo json_encode($trend_real_speed).',"trend_current_speed":';
echo json_encode($trend_current_speed).',"trend_timestamps":';
echo json_encode($trend_timestamps);
echo '}';