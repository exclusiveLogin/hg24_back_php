<?php
$logindb="birthhelper";
$passdb="q1w2e3r4t5y6";
$dbhost="185.178.46.248";
$dbname="hellgame";

$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno){
    die('{"errors":true,"errormsg":"error db":"'.$mysql->connect_error.'"}');
}
$mysql->query("SET NAMES 'UTF8';");
