<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Request-Method: POST, GET, OPTION");


include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

function getLastID(){
    $query = "SELECT LAST_INSERT_ID() FROM `user_status` ";

    global $mysql;
    $res = $mysql->query($query);

    $row = $res->fetch_row();
    $newslotId = $row[0]; // id нового созданного слота

    return $newslotId;
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $arr = json_decode(file_get_contents('php://input'), true);

    if( isset($arr['mode']) && isset($arr['status']) && isset($arr['login']) && $arr['mode'] === 'add_status' ){
        $status = $arr['status'];
        $login = $arr['login'];
        $class = isset($arr['class']) ? $arr['class'] : 'default';
        $title = isset($arr['title']) ? $arr['title'] : '';

        $q = "INSERT INTO `user_status` ( `login`, `status`, `title`, `class` ) 
        VALUES ( \"$login\", \"$status\", \"$title\", \"$class\" )";
      }

    if( $q ){
        $res = $mysql->query($q);
        $last = getLastID();

        $arr = array_merge( $arr, array( "id" => $last ) );
        $arr = array_merge( $arr, array( "q" => $q ) );
    }
    
    echo json_encode( $arr );
}

// GETTERS
if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {
    if( isset($_GET['login']) &&  isset($_GET['mode']) &&  $_GET['mode'] === 'get_status' ){
        $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 1';
        $skip = isset($_GET['skip']) ? ' OFFSET '.$_GET['skip'] : ' OFFSET 0';
        $login = $_GET['login'];

        $q = "SELECT * FROM `user_status` WHERE `login`=\"$login\" ORDER BY `id` DESC $limit $skip";

        //echo $q;

    }

    if( $q ){
        $json = array();
    
        $res = $mysql->query( $q );
        $row = $res->fetch_assoc();
    
        while( $row ){
          array_push( $json, $row);
          $row = $res->fetch_assoc();
        }
    
        echo json_encode( $json );
    
      } else {
        echo json_encode( array('error' => 'request error') );
      }

}
