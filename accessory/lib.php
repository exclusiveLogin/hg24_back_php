<?php
include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

$arr = new stdClass();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    //$arr = json_decode(file_get_contents('php://input'), true);

    //$arr['title'] = isset($arr['title']) ? $arr['title'] : '';
    //$arr['text_field'] = isset($arr['text_field']) ? $arr['text_field'] : '';
    //$arr['author'] = isset($arr['author']) ? $arr['author'] : '';

    //$query = "INSERT INTO `blog` (`title`, `text_field`, `author`) VALUES (\"$arr[title]\", \"$arr[text_field]\", \"$arr[author]\")";

    //$res = $mysql->query($query);

    //echo json_encode( $arr );
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

  if(isset($_GET['mode']) && $_GET['mode'] == 'all'){
    $query = "SELECT DISTINCT `id` FROM `game_objects`";
  }

  if(isset($_GET['mode']) && isset($_GET['id']) && $_GET['mode'] == 'byid'){
    $id = $_GET['id'];
    $query = "SELECT  `game_objects`.*,
      `item_category`.`id` AS `cat_id`,
      `item_category`.`image_min` AS `cat_icon`,
      `item_category`.`title` AS `cat_title`,
      `elements`.`title` AS `element_title`
      FROM `game_objects`,`item_category`,`elements`
      WHERE `game_objects`.`id` = $id
      AND `game_objects`.`category_object`=`item_category`.`name`
      AND `game_objects`.`element` = `elements`.`name`";

  }

  if(isset($_GET['mode']) && isset($_GET['name']) && $_GET['mode'] == 'byname'){
    $name = $_GET['name'];
    $query = "SELECT * FROM `game_objects` WHERE `name` = $name";
  }

  if(isset($_GET['mode']) && $_GET['mode'] == 'unlinked_rgos'){
    $query = "SELECT * FROM `real_game_objects` WHERE id NOT IN (SELECT `rgo_id` FROM `object_slots` WHERE rgo_id IS NOT NULL)";
  }

  //SELECT * FROM `real_game_objects` WHERE id NOT IN (SELECT `rgo_id` FROM `object_slots` WHERE rgo_id IS NOT NULL)

  //if(isset($_GET['mode']) && $_GET['mode'] == 'byslot'){
    //$query = "SELECT DISTINCT `target` FROM `reciept_parts`";
  //}
  $json = array();

  if( $query ){
    $res = $mysql->query($query);
    $row = $res->fetch_assoc();

    while( $row ){
        array_push($json, $row);
        $row = $res->fetch_assoc();
    }
  }

  echo json_encode($json);
}
