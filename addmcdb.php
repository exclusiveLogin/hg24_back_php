<?php
include_once "dbsetting.php";

$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno){
    echo 'error:'.$mysql->connect_error;
    die();
}

if($_POST['addmc']){
    $namemc=mysqli_real_escape_string($mysql,$_POST["namemc"]);
    $descriptionmc=mysqli_real_escape_string($mysql,$_POST["descriptionmc"]);
    $addmcflag=$_POST["addmc"];
    $lat = $_POST["lat"];
    $lng = $_POST["lng"];
    $sql = "INSERT INTO `units` (`name`,`description`,`lat`,`lng`) VALUES ('$namemc','$descriptionmc','$lat','$lng')";
    echo "<p>SQL:$sql</p>";
    print_r($_POST);
    $mysql->query($sql);
}
elseif($_POST['deletemc']){
    $deletemcid=$_POST["mcid"];
    $sql = "DELETE FROM `units` WHERE id=$deletemcid";
    echo $sql;
    $res = $mysql->query($sql);
    if($mysql->errno){echo "errors:".$mysql->error;}
}
$mysql->close();