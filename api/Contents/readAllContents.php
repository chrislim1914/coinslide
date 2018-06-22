<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Contents.php';
include_once '../../class/Utilities.php';

//instantiate database and Contents Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$contents = new  Contents($dbmysql);
$utilities = new Utilities();

//query statement for contents
$stmt = $contents->readAllContents();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){
 
    // contents array
    $contents_arr=array();
    $contents_arr["data"]=array();
 
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
        extract($row);

        $contentsList = array(
            "idcontent" => $idcontent,
            "user_id" => $user_id,
            "nickname" => $nickname,
            "title" => $title,
            "content" => $content,
            "createdate" => $createdate,
            "modifieddate" => $modifieddate,
            "timelapse" => $utilities->get_time_ago(strtotime($createdate)),
            "delete" => $delete
        );
 
        array_push($contents_arr["data"], $contentsList);
    }
 
    echo json_encode($contents_arr);
} else {
    echo json_encode(
        array("message" => "No Content yet!")
    );
}
 
