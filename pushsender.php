<?php
require_once "GCM/Sender.php";
require_once "GCM/Exception.php";
require_once "GCM/Message.php";
require_once "GCM/Response.php";
require_once "dbsetting_n_connect.php";

require_once ""


$sender = new Sender("AIzaSyCDV_cGYt4mHY0kGg6_4vvsvMc41Fw5g3c");



try {
    $response = $sender->sendMessage(
        array("cax-fsYSk84:APA91bHpCCGawUDRGW6FkugcXG96YT1nytiY-s72SqJ6gyTdTioWcIUICgGGB4TxsGkiPWa7BYykTHNZVmN_6Kh5DtPwxmaEy5NGwCTSQacu5VQMiWqyHH-teFMS18IoEMd8cdrTq6m4"),
        array("data" => "123"),
        "collapse_key"
    );

    if ($response->getNewRegistrationIdsCount() > 0) {
        $newRegistrationIds = $response->getNewRegistrationIds();
        foreach ($newRegistrationIds as $oldRegistrationId => $newRegistrationId){
            //Update $oldRegistrationId to $newRegistrationId in DB
            //TODO
            echo $oldRegistrationId.":".$newRegistrationId."<br>";
        }
    }

    if ($response->getFailureCount() > 0) {
        $invalidRegistrationIds = $response->getInvalidRegistrationIds();
        foreach($invalidRegistrationIds as $invalidRegistrationId) {
            //Remove $invalidRegistrationId from DB
            echo "invalidRegistrationId:".$invalidRegistrationId."<br>";
        }

        //Schedule to resend messages to unavailable devices
        $unavailableIds = $response->getUnavailableRegistrationIds();
        if($unavailableIds){
            //echo "Имеются недействительные токены<br>";
        }
    }
} catch (myException $e) {

    switch ($e->getCode()) {
        case myException::ILLEGAL_API_KEY:
        case myException::AUTHENTICATION_ERROR:
        case myException::MALFORMED_REQUEST:
        case myException::UNKNOWN_ERROR:
        case myException::MALFORMED_RESPONSE:
            //Deal with it
            break;
    }
}