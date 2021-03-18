<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Request-Method: POST, GET, OPTION");


include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

function getLastID(){
    $query = "SELECT LAST_INSERT_ID() FROM `user_emo` ";

    global $mysql;
    $res = $mysql->query($query);

    $row = $res->fetch_row();
    $newslotId = $row[0]; // id нового созданного слота

    return $newslotId;
}

function getDelta( $login ){

  
  $query = "SELECT * FROM `user_emo` WHERE `login`=\"$login\" ORDER BY `id` DESC LIMIT 1";

    global $mysql;
    $res = $mysql->query($query);

    $row = $res->fetch_assoc();
    
    return $row;
}


if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $arr = json_decode(file_get_contents('php://input'), true);

    if( isset($arr['mode']) && isset($arr['value']) && isset($arr['login']) && $arr['mode'] === 'add_emo' ){
        $value = $arr['value'];
        $login = $arr['login'];
        $d = getDelta( $login );
        $delta = isset($d) ? $d['value'] : 0;
        $title = isset($arr['title']) ? '"'.$arr['title'].'"' : 'NULL';

        $q = "INSERT INTO `user_emo` ( `login`, `value`, `delta`, `title` ) 
        VALUES ( \"$login\", $value, $delta, $title )";

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
    if( isset($_GET['login']) &&  isset($_GET['mode']) &&  $_GET['mode'] === 'get_emo' ){
        $limit_num = isset($_GET['no_pair']) ? 1 : 2;
        $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : " LIMIT ${limit_num}";
        $login = $_GET['login'];

        $q = "SELECT * FROM `user_emo` WHERE `login`=\"$login\" ORDER BY `id` DESC $limit";

    }

    if( isset($_GET['login']) &&  isset($_GET['mode']) &&  $_GET['mode'] === 'get_trend' ){
      $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 10';
      $skip = isset($_GET['skip']) && !!$_GET['skip'] ? ' OFFSET '.$_GET['skip'] : '';
      $login = $_GET['login'];

      $q = "SELECT *, UNIX_TIMESTAMP(`datetime`)*1000 AS `utc` FROM `user_emo` WHERE `login`=\"$login\" ORDER BY `id` DESC $limit $skip";
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
