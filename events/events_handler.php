<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Request-Method: POST, GET, OPTION");


include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  $arr = json_decode(file_get_contents('php://input'), true);

  if( isset($arr['mode'] ) && $arr['mode'] === 'add_event'){

    $segment = isset($arr['segment']) ? '"'.$arr['segment'].'"'  : 'NULL';
    $level = isset($arr['level']) ? '"'.$arr['level'].'"' : 'global';
    $author  = isset($arr['author']) ? '"'.$arr['author'].'"' : 'NULL';
    $title = isset($arr['title']) ? '"'.$arr['title'].'"' : 'NULL';
    $description = isset($arr['description']) ?  '"'.$arr['description'].'"'  : 'NULL';
    $desktop_notify = isset($arr['desktop_notify']) ? $arr['desktop_notify'] : 'NULL';
    $telegram_notify = isset($arr['telegram_notify']) ? $arr['telegram_notify'] : 'NULL';
    $push_notify = isset($arr['push_notify']) ? $arr['push_notify'] : 'NULL';
    $img = isset($arr['img']) ? '"'.$arr['img'].'"' : 'NULL';

    $q = "INSERT INTO `events` ( `author`, `title`, `description`, `level`, `segment`, `desktop_notify`, `push_notify`, `telegram_notify`, `img` ) 
          VALUES ( $author, $title, $description, $level, $segment, $desktop_notify, $push_notify, $telegram_notify, $img )";


    $mysql->query( $q );

    $arr = array_merge( $arr, array( "q" => $q ) );

    echo json_encode($arr);
  }
}

// GETTERS
if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

  $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 100 ';


  if( isset($_GET['mode']) && $_GET['mode'] == 'get_all_events'){
    $q = "SELECT * FROM `events` $limit";
  }

  if( isset($_GET['mode']) && isset($_GET['author']) && $_GET['mode'] === 'get_events_by_author' ){
    $author = $_GET['author'];

    $q = "SELECT * FROM `events` WHERE `author` = \"$author\" $limit";
  }

  if( isset($_GET['mode']) && isset($_GET['segment']) && $_GET['mode'] === 'get_events_by_level' ){
    $level = $_GET['level'];
    $q = "SELECT * FROM `events` WHERE `level` = \"$level\" $limit";
  }

  if( isset($_GET['mode']) && isset($_GET['segment']) && $_GET['mode'] === 'get_events_by_segment' ){
    $segment = $_GET['segment'];
    $q = "SELECT * FROM `events` WHERE `segment` = \"$segment\" $limit";
  }

  if( isset($_GET['mode']) && isset($_GET['id']) && $_GET['mode'] === 'get_new_events' ){
    $last_id = $_GET['id'];
    $q = "SELECT * FROM `events` WHERE `id` > $last_id ";
  }

  if( isset($_GET['mode']) && $_GET['mode'] === 'get_last_event' ){
    $q = "SELECT * FROM `events` ORDER BY `id` DESC LIMIT 1";
  }


  if( $q ){
    $json = array();

    $res = $mysql->query( $q );
    $row = $res ? $res->fetch_assoc() : false;

    while( $row ){
      array_push( $json, $row);
      $row = $res->fetch_assoc();
    }

    echo json_encode( $json );

  } else {
    echo json_encode( array('error' => 'request error') );
  }


}
