<?php

$ip = $_SERVER['REMOTE_ADDR']; // the IP address to query
//echo $ip."<br>";
$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
if($query && $query['status'] == 'success') {
    //echo 'Hello visitor from '.$query['country'].', '.$query['city'].'!<br>';
    echo json_encode($query);
} else {
    echo '{"status":"error"}';
}