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
        $arr['title'] = isset($arr['title']) ? $arr['title'] : '';
        $arr['text_field'] = isset($arr['text_field']) ? $arr['text_field'] : '';
        $arr['author'] = isset($arr['author']) ? $arr['author'] : '';

        $query = "INSERT INTO `blog` (`title`, `text_field`, `author`) VALUES (\"$arr[title]\", \"$arr[text_field]\", \"$arr[author]\")";

        $res = $mysql->query($query);

        echo json_encode( $arr );
    }
    
    if( isset($arr['operation']) && isset($arr['id']) && $arr['operation'] == 'remove' ){
        $id = isset($arr['id']) ? $arr['id'] : NULL;
        
        if($id){
            $query = "DELETE FROM `blog` WHERE `id`= $id";
            $mysql->query($query);
            array_push($arr, array('q' => $query, "deleted_id" => $id));
        } else array_push($arr, array('q' => $query, "error" => 'no_id'));
        

        echo json_encode( $arr );
        
      }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // The request is using the GET method
    $arr = array();

    $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : ' LIMIT 10';
    $skip = isset($_GET['skip']) ? ' OFFSET '.$_GET['skip'] : '';
    $author  = isset($_GET['author']) ? '`author`="'.$_GET['author'].'"' : NULL;

    $where = ($author) ? 'WHERE '.$author : '';
    
    $query = "SELECT * FROM `blog` $where ORDER BY `id` DESC $limit $skip";

    $res = $mysql->query($query);
    //echo $query;

    $row = $res->fetch_assoc();
    $json = array();

    while( $row ){
        array_push($json, $row);
        $row = $res->fetch_assoc();
    } 



    echo json_encode($json);
}
