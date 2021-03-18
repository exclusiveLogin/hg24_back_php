<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Request-Method: POST, GET, OPTION");


include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  $arr = json_decode(file_get_contents('php://input'), true);

  if( isset($arr['mode'] ) && isset($arr['segment']) && $arr['mode'] === 'update'){
    $segment = $arr['segment'];
    $q = "INSERT INTO `update_segment` ( `segment` ) VALUES ( \"$segment\" ) ON DUPLICATE KEY UPDATE `updated` = NOW()";

    $mysql->query( $q );

    echo json_encode($arr);
  }
}

// GETTERS
if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

  if( isset($_GET['mode']) && $_GET['mode'] == 'get_all_segments'){
    $q = "SELECT * FROM `update_segment`";
  }

  if( isset($_GET['mode']) && isset($_GET['segment']) && $_GET['mode'] === 'get_segment' ){
    $segment = $_GET['segment'];
    $q = "SELECT * FROM `update_segment` WHERE `segment` = \"$segment\"";
  }

  if( isset($_GET['mode']) && isset($_GET['segment']) && isset($_GET['old_date']) && $_GET['mode'] === 'get_fresh_segments' ){
    $segment = $_GET['segment'];
    $old_date = $_GET['old_date'];
    $q = "SELECT * FROM `update_segment` WHERE `updated` > TIMESTAMP( \"$old_date\" )";
  }

  if( isset($_GET['login']) ){
    $login = $_GET['login'];
    $_q = "UPDATE `users_act` SET `upd` = NOW() WHERE `id_user` = ( SELECT `id` FROM `users` WHERE `login`= \"$login\" )";
    $mysql->query( $_q );
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
