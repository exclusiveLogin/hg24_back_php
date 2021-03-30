<?php
include_once "headers.php";
include_once "dbsetting_n_connect.php";


// GETTERS
if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

    $login = isset($_GET['login']) ? $_GET['login'] : NULL;
    $password = isset($_GET['password']) ? $_GET['password'] : NULL;
    $q = "SELECT * FROM `users` WHERE `login` = '$login' AND `password` = '$password'";

    $auth=array("auth" => false, "login" => "");
    
    $res = $mysql->query( $q );

    if($mysql->error) echo json_encode($mysql->error);

    $row = $res ? $res->fetch_assoc() : false;
    

    if($row) {
        $auth['auth'] = true;
        $auth["login"] = $login;
        $auth["msg"] = "Авторизация для Пользователя ".$login." прошла успешно";
        $auth["email"] = $row['email'];
        $auth["id"] = $row['id'];
        $auth["title"] = $row['title'];
    } else {
        $auth["login"]=$login;
        $auth["msg"]="Неверный пароль для Пользователя ".$login;
        echo json_encode($auth);
    }

    echo json_encode( $auth );
    $res->close();
    $mysql->close();
} else {
    die(
        json_encode( 
            array("msg" => "Другие запросы кроме GET запрещены")
        )
    );
} 
