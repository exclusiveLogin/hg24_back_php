<?php
include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

$arr = array();

function createRGO( $_object_id ){
  //создаем новый RGO
  $query = "INSERT INTO `real_game_objects` (`object_id`) VALUES ( $_object_id )";
  //Берем последний id созданного
  $query2 = "SELECT LAST_INSERT_ID() FROM `real_game_objects` ";

  global $mysql;
  $mysql->query($query);
  $res = $mysql->query($query2);
  $row = $res->fetch_row();
  $rgo = $row[0]; // id нового созданного RGO

  //echo "rgo created: $rgo from object type: $_object_id > ";
  return $rgo;
}

function createNewSlot(){
  $query = "INSERT INTO `object_slots` (`owner_type`) VALUES ( \"map\" )";
  //Берем последний id созданного
  $query2 = "SELECT LAST_INSERT_ID() FROM `object_slots` ";

  global $mysql;
  $mysql->query($query);
  $res = $mysql->query($query2);

  $row = $res->fetch_row();
  $newslotId = $row[0]; // id нового созданного слота

  return $newslotId;
}

function linkSlotOnRGO( $idSlot, $idRGO ){

  //echo "link Slot $idSlot and RGO $idRGO > ";
  //удаляем все линки на RGO  из  слотов
  $query = "UPDATE `object_slots` SET `rgo_id` = NULL WHERE `rgo_id`= $idRGO";
  //выставляем правильный линк с нужного слота
  $query2 = "UPDATE `object_slots` SET `rgo_id` = $idRGO WHERE `id`= $idSlot";

  global $mysql;
  $mysql->query( $query );
  $mysql->query( $query2 );

}

function linkSlotOnSpawn( $idSpawn, $idSlot ){

  //удаляем все линки на RGO  из  слотов
  $query = "UPDATE `object_spawn` SET `armed_slot_id` = NULL WHERE `armed_slot_id`= $idSlot";
  //выставляем правильный линк с нужного слота
  $query2 = "UPDATE `object_spawn` SET `armed_slot_id` = $idSlot WHERE `id`= $idSpawn";
  //обновление last emit;
  $query3 = "UPDATE `object_spawn` SET `last_emit` = NOW() WHERE `id`= $idSpawn";

  global $mysql;
  $mysql->query( $query );
  $mysql->query( $query2 );
  $mysql->query( $query3 );
}

function getFreeSpawn(){
  $query = "SELECT * FROM `object_spawn` WHERE `armed_slot_id` IS NULL LIMIT 1";

  global $mysql;
  $res = $mysql->query( $query );
  $row = $res ? $res->fetch_assoc() : false;

  //echo "free spawn selected: $row[id] > ";
  return $row;
}

function getSpawn( $id ){
  $query = "SELECT * FROM `object_spawn` WHERE `id` = $id AND `armed_slot_id` IS NULL LIMIT 1";

  global $mysql;
  $res = $mysql->query( $query );
  $row = $res ? $res->fetch_assoc() : false;

  return $row;
}

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  $arr = json_decode(file_get_contents('php://input'), true);

  // Select Spawn by ID
  if( isset($arr['mode'] ) && isset($arr['id']) && $arr['mode'] === 'id'){
    $id = $arr['id'];
    // Выбор спауна
    $spawn = getSpawn( $id );

    // проверка наличия
    if( $spawn && $spawn['object_id']){
      // создание RGO
      $rgoID = createRGO( $spawn['object_id'] );

      // создание слота
      $slotID = createNewSlot();
      linkSlotOnRGO( $slotID, $rgoID );

      // связывание spawn с новым слотом
      linkSlotOnSpawn( $spawn['id'], $slotID );

      $arr = array_merge( $arr, array( 'newslotId' => $slotID, 'newRGOId' => $rgoID, 'spawnId' => $spawn['id'] ) );

      echo json_encode($arr);

    } else {

      echo json_encode( array( 'error'=>'unknown spawn' ));

    }
  }

  // Single random select Spawn

  if(isset($arr['mode']) && $arr['mode'] === 'single'){
    // Выбор спауна
    $spawn = getFreeSpawn();

    // проверка наличия
    if( $spawn && $spawn['object_id']){
      // создание RGO
      $rgoID = createRGO( $spawn['object_id'] );

      // создание слота
      $slotID = createNewSlot();
      linkSlotOnRGO( $slotID, $rgoID );

      // связывание spawn с новым слотом
      linkSlotOnSpawn( $spawn['id'], $slotID );

      $arr = array_merge( $arr, array( 'newslotId' => $slotID, 'newRGOId' => $rgoID, 'spawnId' => $spawn['id'] ) );

      echo json_encode($arr);

    } else {

      echo json_encode( array( 'error'=>'unknown spawn' ));

    }
  }

}

// GETTERS
if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

  if( isset($_GET['mode']) && $_GET['mode'] == 'get_all_spawn'){
    $q = "SELECT *
          FROM `object_spawn`";
  }

  if( isset($_GET['mode']) && isset($_GET['id']) && $_GET['mode'] === 'get_spawn_by_id' ){
    $id = $_GET['id'];
    $q = "SELECT *
          FROM `object_spawn`
          WHERE `id` = $id";
  }

  if( isset($_GET['mode']) && isset($_GET['id']) && $_GET['mode'] === 'get_rgo_by_spawn' ){
    $id = $_GET['id'];

    $q = "SELECT `real_game_objects`.*
          FROM `object_spawn`, `object_slots`, `real_game_objects`
          WHERE `object_spawn`.`id` = $id
          AND `object_spawn`.`armed_slot_id` = `object_slots`.`id`
          AND `object_slots`.`rgo_id` = `real_game_objects`.`id`";
  }

  if( isset($_GET['mode']) && isset($_GET['id']) && $_GET['mode'] === 'get_slot_by_spawn' ){
    $id = $_GET['id'];

    $q = "SELECT `object_slots`.*
          FROM `object_spawn` , `object_slots`
          WHERE `object_spawn`.`id` = $id
          AND `object_spawn`.`armed_slot_id` = `object_slots`.`id`";
  }

  if( isset($_GET['mode']) && isset($_GET['id']) && $_GET['mode'] === 'get_spawn_by_rgo' ){
    $id = $_GET['id'];

    $q = "SELECT `object_spawn`.*
          FROM `object_slots`, `object_spawn`
          WHERE `object_spawn`.`armed_slot_id` = `object_slots`.`id`
          AND `object_slots`.`rgo_id` = $id";
  }

  if( isset($_GET['mode']) && isset($_GET['id']) && $_GET['mode'] === 'get_spawn_by_slot' ){
    $id = $_GET['id'];

    $q = "SELECT *
          FROM `object_spawn`
          WHERE `object_spawn`.`armed_slot_id` = $id";
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
