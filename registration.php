<?php
include_once "dbsetting.php";
$mysql= new mysqli($dbhost,$logindb,$passdb,$dbname);
if($mysql->connect_errno)die("error db:".$mysql->connect_error);
$login = null;
if($_GET["login"]=="ssv"){
    $query = "SELECT `password` FROM `users` WHERE `login`='ssv'";
    $login = 'ssv';
    
}else if ($_GET["login"]=="msn"){
    $query = "SELECT `password` FROM `users` WHERE `login`='msn'";
    $login = 'msn';
}else{
    die("Script access denied");
}
$res = $mysql->query($query);
$row = $res->fetch_assoc();
$pass = null;
while($row){
    $pass = $row['password'];
    echo 'password of user '.$login.': '.$pass;
    $row = $res->fetch_assoc();
}
$res->close();
if(isset($_GET['oldpass'])&&isset($_GET['newpass'])){
    if($_GET['oldpass']==$pass){
        echo "<br>new password setting";
        $newpass = $_GET['newpass'];
        $query = "UPDATE `users` SET `password`='".$newpass."' WHERE `login`= '".$login."'";
        $mysql->query($query);
        echo "<br>new password setted";
    }else{
        echo "<br>old password incorrect";
    }
}

$mysql->close();