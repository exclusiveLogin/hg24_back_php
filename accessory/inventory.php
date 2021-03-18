<?php
include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

$arr = new stdClass();

function clearSlot($id){
  $_q = "UPDATE `object_slots` SET `rgo_id` = NULL WHERE `id`= $id";
  echo $_q;
  global $mysql;
  $mysql->query($_q);
}

function clearSlotByItemID($id){
  $_q = "UPDATE `object_slots` SET `rgo_id` = NULL WHERE `rgo_id`= $id";
  //echo $_q;
  global $mysql;
  $mysql->query($_q);
}

function removeSlot($id){
  $_q = "DELETE FROM `object_slots` WHERE `id`= $id";
  //echo $_q;
  global $mysql;
  $mysql->query($_q);
}


function removeItem($id){
  $_q = "DELETE FROM `real_game_objects` WHERE `id`= $id";
  //echo $_q;
  global $mysql;
  $mysql->query($_q);
}


function createNewSlotByUser( $_owner ){
  $query = "INSERT INTO `object_slots` (`owner`, `owner_type`) VALUES ( \"$_owner\", \"user\" )";
  //Берем последний id созданного
  $query2 = "SELECT LAST_INSERT_ID() FROM `object_slots` ";

  global $mysql;
  $mysql->query($query);
  $res = $mysql->query($query2);

  $row = $res->fetch_row();
  $newslotId = $row[0]; // id нового созданного слота

  return $newslotId;
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

  //удаляем все линки на RGO  из  слотов
  $query = "UPDATE `object_slots` SET `rgo_id` = NULL WHERE `rgo_id`= $idRGO";
  //выставляем правильный линк с нужного слота
  $query2 = "UPDATE `object_slots` SET `rgo_id` = $idRGO WHERE `id`= $idSlot";

  global $mysql;
  $mysql->query( $query );
  $mysql->query( $query2 );

}

function craftNewRGO( $_object_id, $_creator_name, $_slot){
  //создаем новый RGO
  $query = "INSERT INTO `real_game_objects` (`object_id`, `creator_name`) VALUES ( $_object_id, \"$_creator_name\" )";
  //Берем последний id созданного
  $query2 = "SELECT LAST_INSERT_ID() FROM `real_game_objects` ";

  global $mysql;
  $mysql->query($query);
  $res = $mysql->query($query2);
  $row = $res->fetch_row();
  $rgo = $row[0]; // id нового созданного RGO
  if($rgo){
    //связываем слот с созданным RGO

    //$query_upd_slot = "UPDATE `object_slots` SET `rgo_id` = $rgo WHERE `id` = $_slot";
    //$mysql->query($query_upd_slot);
    linkSlotOnRGO( $_slot, $rgo);
  }

}

function spawnNewRGO( $_object_id ){
  //создаем новый RGO
  $query = "INSERT INTO `real_game_objects` (`object_id`) VALUES ( $_object_id )";
  //Берем последний id созданного
  $query2 = "SELECT LAST_INSERT_ID() FROM `real_game_objects` ";

  global $mysql;
  $mysql->query($query);
  $res = $mysql->query($query2);
  $row = $res->fetch_row();
  $rgo = $row[0]; // id нового созданного RGO
  if($rgo){
    //связываем слот с созданным RGO
    $newslotId = createNewSlot();
    linkSlotOnRGO( $newslotId, $rgo);
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    $arr = json_decode(file_get_contents('php://input'), true);

    if(isset($arr['mode']) && $arr['mode'] == 'craft_new_item'){
      $object_id = isset($arr['object_id']) ? $arr['object_id'] : NULL;
      $creator_name = isset($arr['creator_name']) ? $arr['creator_name'] : NULL;
      $slot = isset($arr['slot']) ? $arr['slot'] : NULL;

      if( $object_id && $creator_name && $slot ) {
        craftNewRGO( $object_id, $creator_name, $slot );
      }

      echo json_encode( $arr );
      die();
    }

    if(isset($arr['mode']) && $arr['mode'] == 'spawn_new_rgo'){
      $object_id = isset($arr['object_id']) ? $arr['object_id'] : NULL;

      if( $object_id ) {
        spawnNewRGO( $object_id );
      }

      echo json_encode( $arr );
      die();
    }

    if(isset($arr['mode']) && $arr['mode'] == 'create_new_slot_by_user'){
      $owner = isset($arr['owner']) ? $arr['owner'] : NULL;

      if( $owner ) {
        $newslotId = createNewSlotByUser($owner);
        //добавляем новый id слота в ответ
        $arr = (object) array_merge( (array)$arr, array( 'newslotId' => $newslotId ) );
      }

      echo json_encode( $arr );
      die();
    }

    if(isset($arr['mode']) && $arr['mode'] == 'create_new_slot_by_map'){
      $newslotId = createNewSlot();
      //добавляем новый id слота в ответ
      $arr = (object) array_merge( (array)$arr, array( 'newslotId' => $newslotId ) );

      echo json_encode( $arr );
      die();
    }

    if(isset($arr['mode']) && $arr['mode'] == 'grind_item'){
      $owner = isset($arr['owner']) ? $arr['owner'] : NULL;
      $slot_id = isset($arr['slot_id']) ? $arr['slot_id'] : NULL;
      $spawn_id = isset($arr['spawn_id']) ? $arr['spawn_id'] : NULL;

      if( $owner && $slot_id ) {
        //определяем у слота нового хозяина
        $query = "UPDATE `object_slots` SET `owner` = \"$owner\", `owner_type`=\"user\" WHERE `id`= $slot_id";
        $mysql->query( $query );
        $arr = (object) array_merge( (array)$arr, array( 'newslotOwner' => $owner ) );
      }

      if( $spawn_id ){
        $query = "UPDATE `object_spawn` SET `armed_slot_id` = NULL WHERE `id`= $spawn_id";
        $mysql->query( $query );
        $arr = (object) array_merge( (array)$arr, array( 'updatedSpawn' => $spawn_id ) );
      }

      echo json_encode( $arr );
      die();
    }

    if(isset($arr['mode']) && $arr['mode'] == 'drop_item'){
      $slot_id = isset($arr['slot_id']) ? $arr['slot_id'] : NULL;

      if( $slot_id ) {
        //определяем у слота нового хозяина
        $query = "UPDATE `object_slots` SET `owner` = NULL, `owner_type`=\"map\" WHERE `id`= $slot_id";
        $mysql->query( $query );
        $arr = (object) array_merge( (array)$arr, array( 'newslotOwner' => 'map' ) );
      }

      echo json_encode( $arr );
      die();
    }

    if(isset($arr['mode']) && isset($arr['slot_id']) && $arr['mode'] == 'remove_slot'){
      $id = $arr['slot_id'];
      //Удаляем слот
      removeSlot($id);

      die(json_encode(array(removedSlot => $id)));
    }

    if(isset($arr['mode']) && isset($arr['slot_id']) && $arr['mode'] == 'clear_slot'){
      $id = $arr['slot_id'];
      //Освобождаем слот
      clearSlot($id);

      die(json_encode(array(clearedSlot => $id)));
    }

    if(isset($arr['mode']) && isset($arr['item_id']) && $arr['mode'] == 'utilization_item'){
      $id = $arr['item_id'];
      //Удаление RGO
      removeItem($id);
      clearSlotByItemID($id);

      die(json_encode(array(utilizedItemId => $id)));
    }
  
    if(isset($arr['mode']) && isset($arr['item_id']) && $arr['mode'] == 'utilization_rgo'){
      $id = $arr['item_id'];
      //Удаление RGO
      removeItem($id);

      die(json_encode(array(removedRGOId => $id)));
    }
  
    if(isset($arr['mode']) && isset($arr['item_id']) && $arr['mode'] == 'wrap_rgo_in_slot'){
      $id = $arr['item_id'];
  
      $newslotId = createNewSlot();
      linkSlotOnRGO( $newslotId, $id);
  
      die(json_encode(array(newSlotId => $newslotId, rgoId => $id)));
    }


}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

  if(isset($_GET['mode']) && $_GET['mode'] == 'all_slots'){
    $query = "SELECT `object_slots`.* ,
    `game_objects`.`id` as `go_id`,
    `object_spawn`.id as `spawn`
    FROM `object_slots`
    LEFT JOIN `real_game_objects` ON `object_slots`.`rgo_id` = `real_game_objects`.`id`
    LEFT JOIN `game_objects` ON `real_game_objects`.`object_id` = `game_objects`.`id`
    LEFT JOIN `object_spawn` ON `object_spawn`.`armed_slot_id` = `object_slots`.`id`
    ORDER BY `id` DESC
    ";
  }

  

  // выбрать те эелементы на которые нет линков
  // SELECT * FROM real_game_objects WHERE id not in (SELECT rgo_id from object_slots where rgo_id is not null)

  //SELECT object_slots.*, game_objects.id as go_id FROM `object_slots` LEFT JOIN `real_game_objects` ON object_slots.rgo_id = real_game_objects.id LEFT JOIN `game_objects` ON real_game_objects.object_id = game_objects.id
  if(isset($_GET['mode']) && $_GET['mode'] == 'slots_by_user'){
    $owner  = isset($_GET['owner']) ? '`owner`="'.$_GET['owner'].'"' : NULL;
    $where = ( $owner ) ? 'WHERE '.$owner : '';
    $query = "SELECT `object_slots`.* ,
    `game_objects`.`id` as `go_id`
    FROM `object_slots`
    LEFT JOIN `real_game_objects` ON `object_slots`.`rgo_id` = `real_game_objects`.`id`
    LEFT JOIN `game_objects` ON `real_game_objects`.`object_id` = `game_objects`.`id`
    $where
    ORDER BY `id` DESC";
  }

  if(!$query) die(json_encode([]));
  $res = $mysql->query($query);

  $row = $res->fetch_assoc();
  $json = array();

  while( $row ){
      array_push($json, $row);
      $row = $res->fetch_assoc();
  }

  echo json_encode($json);
}
