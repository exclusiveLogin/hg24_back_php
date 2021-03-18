<?php
$logindb="host1490316_hellgame";
$passdb="q1w2e3r4t5y";
$dbhost="localhost";
$dbname="host1490316";

$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno){
    die('{"errors":true,"errormsg":"error db":"'.$mysql->connect_error.'"}');
}
$mysql->query("SET NAMES 'UTF8';");
