<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: content-type");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Request-Method: POST, GET, OPTION");

include_once "../dbsetting_n_connect.php";

$arr = new stdClass();

function getLastID(){
    $query = "SELECT LAST_INSERT_ID() FROM `news` ";

    global $mysql;
    $res = $mysql->query($query);

    $row = $res->fetch_row();
    $newslotId = $row[0]; // id нового созданного слота

    return $newslotId;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    $arr = json_decode(file_get_contents('php://input'), true);

    if( isset($arr['operation']) && $arr['operation'] == 'add' ){
        $title = isset($arr['title']) ? $arr['title'] : 'Без заголовка';
        $text = isset($arr['text']) ? $arr['text'] : '';
        $author = isset($arr['author']) ? $arr['author'] : 'hg';
        $img = isset($arr['img']) ? '"'.$arr['img'].'"' : 'NULL';
        $img_min = isset($arr['image_min']) ? '"'.$arr['image_min'].'"' : 'NULL';
        $private = isset($arr['private']) ? '"'.$arr['private'].'"' : 'NULL';
        $category_id = isset($arr['category_id']) ? $arr['category_id'] : 'NULL';

        $query =    "INSERT INTO `news` ( `title`, `text`, `author`, `img`, `img_min`, `private`, `category_id` ) 
                    VALUES (\"$title\", \"$text\", \"$author\", ${img}, ${img_min}, ${private}, ${category_id} )";


        $res = $mysql->query($query);
        $last = getLastID();

        $arr = array_merge( $arr, array( "id" => $last ) );
        $arr = array_merge( $arr, array( "q" => $query ) );

        echo json_encode( $arr );
    }
    
    if( isset($arr['operation']) && isset($arr['id']) && $arr['operation'] == 'remove' ){
        $id = isset($arr['id']) ? $arr['id'] : NULL;
        
        if($id){
            $query = "DELETE FROM `news` WHERE `id`= $id";
            $mysql->query($query);
            $arr = array_merge($arr, array('q' => $query, "deleted_id" => $id));

        } else 
            $arr = array_merge($arr, array('q' => $query, "error" => 'no_id'));
        

        echo json_encode( $arr );
        
      }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $limit = isset($_GET['limit']) ? ' LIMIT '.$_GET['limit'] : '';
    $skip = isset($_GET['skip']) ? ' OFFSET '.$_GET['skip'] : '';
    $author  = isset($_GET['author']) ? '`author`="'.$_GET['author'].'"' : NULL;

    $where = ($author) ? 'WHERE '.$author : '';
    
    $query = "SELECT * FROM `news` $where ORDER BY `id` DESC $limit $skip";

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
