<?php

include_once "../headers.php";
include_once "../dbsetting_n_connect.php";

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
  $arr = json_decode(file_get_contents('php://input'), true);

  if( isset($arr['operation']) && isset($arr['id']) && $arr['operation'] == 'remove' ){
    $id = isset($arr['id']) ? $arr['id'] : NULL;
    
    if($id){
        $query = "DELETE FROM `units` WHERE `id`= $id";
        $mysql->query($query);
        array_push($arr, array('q' => $query, "deleted_id" => $id));
    } else array_push($arr, array('q' => $query, "error" => 'no_id'));
    

    echo json_encode( $arr );
    
  }

  if( isset($arr['mode'] ) && $arr['mode'] === 'add_unit'){

    $status = isset($arr['status']) ? '"'.$arr['status'].'"'  : 'NULL';
    $level = isset($arr['level']) ? '"'.$arr['level'].'"' : 'global';
    $name  = isset($arr['name']) ? '"'.$arr['name'].'"' : 'NULL';
    $description = isset($arr['description']) ?  '"'.$arr['description'].'"'  : 'NULL';
    $class = isset($arr['class']) ? '"'.$arr['class'].'"' : 'NULL';
    $active = isset($arr['active']) ? $arr['active'] : 'NULL';
    $snow = isset($arr['snow']) ? $arr['snow'] : 'NULL';
    $rain = isset($arr['rain']) ? $arr['rain'] : 'NULL';
    $clearsky = isset($arr['clearsky']) ? $arr['clearsky'] : 'NULL';


    $overcast = isset($arr['overcast']) ? $arr['overcast'] : 'NULL';
    $temperature_min = isset($arr['temperature_min']) ? $arr['temperature_min'] : 'NULL';
    $temperature_max = isset($arr['temperature_max']) ? $arr['temperature_max'] : 'NULL';
    $wind_min = isset($arr['wind_min']) ? $arr['wind_min'] : 'NULL';
    $wind_max = isset($arr['wind_max']) ? $arr['wind_max'] : 'NULL';

    $speed = isset($arr['speed']) ? $arr['speed'] : 'NULL';

    $lat = isset($arr['lat']) ?  '"'.$arr['lat'].'"'  : 'NULL';
    $lng = isset($arr['lng']) ?  '"'.$arr['lng'].'"'  : 'NULL';

    $q = "INSERT INTO `units` ( 
        `status`, 
        `level`, 
        `name`, 
        `description`, 
        `class`, 
        `active`, 
        `snow`, 
        `rain`, 
        `clearsky`,
        `overcast`,
        `temperature_min`,
        `temperature_max`,
        `wind_min`,
        `wind_max`,
        `speed`,
        `lat`,
        `lng`,
        ) 
          VALUES ( 
              $status, 
              $level, 
              $name, 
              $description, 
              $class, 
              $active, 
              $snow, 
              $rain, 
              $clearsky, 
              $overcast, 
              $temperature_min,
              $temperature_max,
              $wind_min,
              $wind_max,
              $speed, 
              $lat, 
              $lng, 
              )";


    $mysql->query( $q );

    $arr = array_merge( $arr, array( "q" => $q ) );

    echo json_encode($arr);
  }
}

// GETTERS
if ( $_SERVER['REQUEST_METHOD'] === 'GET' ) {

  $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 100 ';


  if( isset($_GET['mode']) && $_GET['mode'] == 'get_all_units'){
    $q = "SELECT * FROM `units` $limit";
  }

  if( isset($_GET['mode']) && isset($_GET['status']) && $_GET['mode'] === 'get_units_by_status' ){
    $status = $_GET['status'];
    $q = "SELECT * FROM `units` WHERE `status` = \"$status\" $limit";
  }

  if( isset($_GET['mode']) && isset($_GET['level']) && $_GET['mode'] === 'get_units_by_level' ){
    $level = $_GET['level'];
    $q = "SELECT * FROM `units` WHERE `level` = $level $limit";
  }

  if( isset($_GET['mode']) && $_GET['mode'] === 'get_active_units' ){
    $q = "SELECT * FROM `units` WHERE `active` = 1 $limit";
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
