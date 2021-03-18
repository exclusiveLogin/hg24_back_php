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
    $query = "SELECT * FROM `reciept_parts`";
  }

  if(isset($_GET['mode']) && isset($_GET['target']) && $_GET['mode'] == 'bytarget'){
    $target = $_GET['target'];
    $query = "SELECT * FROM `reciept_parts` WHERE `target` = $target";
  }

  if(isset($_GET['mode']) && $_GET['mode'] == 'list'){
    $query = "SELECT DISTINCT `target` FROM `reciept_parts`";
  }


  $res = $mysql->query($query);

  $row = $res->fetch_assoc();
  $json = array();

  while( $row ){
      array_push($json, $row);
      $row = $res->fetch_assoc();
  }

  echo json_encode($json);
}
