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


if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $arr = json_decode(file_get_contents('php://input'), true);

    if( isset($arr['mode']) && isset($arr['login']) && $arr['mode'] === 'add_login' ){
        $id = isset($arr['last_id']) ? $arr['last_id'] : false;
        $login = $arr['login'];
    
        $userAgent = isset($arr['user_agent']) ? '"'.$arr['user_agent'].'"' : 'NULL';
        $battery = isset($arr['battery']) ? '"'.$arr['battery'].'"' : 'NULL';

        $position_lat = isset($arr['position_lat']) ? '"'.$arr['position_lat'].'"' : 'NULL';
        $position_lon = isset($arr['position_lon']) ? '"'.$arr['position_lon'].'"' : 'NULL';
        $accuracy = isset($arr['accuracy']) ? '"'.$arr['accuracy'].'"' : 'NULL';

        $network_equal = isset($arr['network_equal']) ? '"'.$arr['network_equal'].'"' : 'NULL';
        $dlink = isset($arr['dlink']) ? '"'.$arr['dlink'].'"' : 'NULL';

        if ( $id ){
            if( $battery && $battery !== 'NULL'){
                $q = "UPDATE `user_login` 
                SET `battery` = $battery
                WHERE `id`= $id";

                $mysql->query($q);
            }

            if( $position_lat && $position_lat !== 'NULL'){
                $q = "UPDATE `user_login` 
                SET `position_lat` = $position_lat,
                `position_lon` = $position_lon,
                `accuracy` = $accuracy 
                WHERE `id`= $id";

                $mysql->query($q);
            }

            // if( $network_equal && $network_equal !== 'NULL'){
            //     $q = "UPDATE `user_login` 
            //     SET `network_equal` = $network_equal
            //     WHERE `id`= $id";

            //     $mysql->query($q);
            // }

            // if( $dlink && $dlink !== 'NULL'){
            //     $q = "UPDATE `user_login` 
            //     SET `dlink` = $dlink, 
            //     WHERE `id`= $id";

            //     $mysql->query($q);
            
            // }
            $q = false;
            
        } else {
            $q = "INSERT INTO `user_login` ( `login`, `user_agent`, `battery`, `position_lat`, `position_lon`, `accuracy`, `network_equal`, `dlink` ) 
            VALUES ( \"$login\", $userAgent, $battery, $position_lat, $position_lon, $accuracy, $network_equal, $dlink )";
        }
    
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
    if( isset($_GET['login']) &&  isset($_GET['mode']) &&  $_GET['mode'] === 'get_last_login' ){
        
        $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 1';
        $login = $_GET['login'];

        $q = "SELECT * FROM `user_login` WHERE `login`=\"$login\" ORDER BY `id` DESC $limit";

    }

    if( isset($_GET['login']) &&  isset($_GET['mode']) &&  $_GET['mode'] === 'get_logins' ){
      $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 10';
      $skip = isset($_GET['skip']) ? ' OFFSET '.$_GET['skip'] : ' OFFSET 0';
      $login = $_GET['login'];

      $q = "SELECT * FROM `user_login` WHERE `login`=\"$login\" ORDER BY `id` DESC $limit $skip";
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
