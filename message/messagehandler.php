<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Request-Method: POST, GET, OPTION");

include_once "../dbsetting_n_connect.php";

$arr = new stdClass();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    $arr = json_decode(file_get_contents('php://input'), true);

    if( isset($arr['operation']) && $arr['operation'] == 'add' ){
      $arr['subject'] = isset($arr['subject']) ? $arr['subject'] : '';
      $arr['text_field'] = isset($arr['text_field']) ? $arr['text_field'] : '';
      $arr['author'] = isset($arr['author']) ? $arr['author'] : '';
      $arr['to_user'] = isset($arr['to_user']) ? $arr['to_user'] : '';

      //var_dump($arr);

      $query = "INSERT INTO `messages` (`subject`, `text_field`, `author`, `to_user`) VALUES (\"$arr[subject]\", \"$arr[text_field]\", \"$arr[author]\", \"$arr[to_user]\")";

      array_push($arr, $query);

      $res = $mysql->query($query);

      echo json_encode( $arr );
    }

    if( isset($arr['operation']) && $arr['operation'] == 'mark' ){
      $arr['id'] = isset($arr['id']) ? $arr['id'] : NULL;
      $arr['field'] = isset($arr['field']) ? $arr['field'] : NULL;
      $arr['flag'] = isset($arr['flag']) ? $arr['flag'] : NULL;

      if($arr['id'] && $arr['field']){
        if( $arr['flag']) $query = "UPDATE `messages` SET $arr[field] = \"$arr[flag]\" WHERE `id`= $arr[id]";
        else $query = "UPDATE `messages` SET $arr[field] = NULL WHERE `id`= $arr[id]";

        array_push($arr, $query);

        $res = $mysql->query($query);

        echo json_encode( $arr );
      }
    }

    if( isset($arr['operation']) && $arr['operation'] == 'remove' ){
      $arr['id'] = isset($arr['id']) ? $arr['id'] : NULL;

      if( $arr['id'] ){
        $query = "DELETE FROM `messages` WHERE `id`= $arr[id]";

        array_push($arr, $query);

        $res = $mysql->query($query);

        echo json_encode( $arr );
      }
    }


}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // The request is using the GET method
    $arr = array();

    $limit = isset($_GET['limit']) ? 'LIMIT='.$_GET['limit'] : '';
    $to_user  = isset($_GET['to_user']) ? '`to_user`="'.$_GET['to_user'].'"' : NULL;
    $readed  = isset($_GET['readed']) ? ($_GET['readed'] != 'false') ? '`readed`= 1' : '`readed` IS NULL'  : NULL;
    //$arr['group'] = isset($_GET['group']) ? $_GET['group'] : NULL;

    $where = ($to_user) ? 'WHERE '.$to_user : '';

    //var_dump($where);

    $where = ( strlen($where) && $readed) ? $where.' AND '.$readed : $where;

    $query = "SELECT * FROM `messages` $where ORDER BY `id` DESC";

    //echo $query;


    $res = $mysql->query($query);

    if( $res ) $row = $res->fetch_assoc();
    $json = array();

    while( isset($row) && $row ){
        array_push($json, $row);
        $row = $res->fetch_assoc();
    }

    echo json_encode($json);
}
