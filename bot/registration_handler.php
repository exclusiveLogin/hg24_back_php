<?php

include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  $arr = json_decode(file_get_contents('php://input'), true);

  if( isset($arr['operation']) && isset($arr['id']) && $arr['operation'] == 'remove' ){
    $id = isset($arr['id']) ? $arr['id'] : NULL;
    
    if($id){
        $query = "DELETE FROM `bot_registrations` WHERE `id`= $id";
        $mysql->query($query);
        array_push($arr, array('q' => $query, "deleted_id" => $id));
    } else array_push($arr, array('q' => $query, "error" => 'no_id'));

    if($mysql->error) echo json_encode($mysql->error);
    

    echo json_encode( $arr );
    
  }

//   if( isset($arr['operation']) && $arr['operation'] == 'remove_all' ){
//     $query = "DELETE FROM `units`";
//     $mysql->query($query);
//     array_push($arr, array('q' => $query, "deleted units" => "all"));
//     echo json_encode( $arr );
    
//   }

  if( isset($arr['operation'] ) && isset($arr['chat_id'] ) && $arr['operation'] === 'add_registration'){

    $chat_id = $arr['chat_id'];
    $username = isset($arr['username']) ? '"'.$arr['username'].'"' : 'NULL';
    $active  = '1';
   
    $q = "INSERT INTO `bot_registrations` ( `chat_id`, `username`, `active`) 
          VALUES ($chat_id, $username, $active)
          ON DUPLICATE KEY UPDATE `active`  = '1', `username` = $username";


    $mysql->query( $q );

    if($mysql->error) echo json_encode($mysql->error);

    $arr = array_merge( $arr, array( "q" => $q ) );

    echo json_encode($arr);
  }

  if( isset($arr['operation'] ) && isset($arr['chat_id'] ) && $arr['operation'] === 'activate_registration'){

      $chat_id = $arr['chat_id'];
      $username = isset($arr['username']) ? '"'.$arr['username'].'"' : 'NULL';
      $active  = '1';
    
      $q = "UPDATE `bot_registrations` SET `active` = '1' WHERE `chat_id` = $chat_id";


      $mysql->query( $q );

      if($mysql->error) echo json_encode($mysql->error);

      $arr = array_merge( $arr, array( "q" => $q ) );

      echo json_encode($arr);
  }

  if( isset($arr['operation'] ) && isset($arr['chat_id'] ) && $arr['operation'] === 'deactivate_registration'){
    $chat_id = $arr['chat_id'];
    $q = "UPDATE `bot_registrations` SET `active` = NULL WHERE `chat_id` = $chat_id";


    $mysql->query( $q );

    if($mysql->error) echo json_encode($mysql->error);

    $arr = array_merge( $arr, array( "q" => $q ) );

    echo json_encode($arr);
  }
}

// GETTERS
if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

  $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 100 ';


  if( isset($_GET['mode']) && $_GET['mode'] == 'get_all_registrations'){
    $q = "SELECT * FROM `bot_registrations` $limit";
  }

  if( isset($_GET['mode']) && $_GET['mode'] === 'get_active_registrations' ){
    $q = "SELECT * FROM `bot_registrations` WHERE `active` = '1' $limit";
  }

  if( $q ){
    $json = array();

    $res = $mysql->query( $q );

    if($mysql->error) echo json_encode($mysql->error);

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
