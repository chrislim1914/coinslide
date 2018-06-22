<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Contents.php';

//instantiate database and Contents Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$contents = new  Contents($dbmysql);

//get user id
$contents->idcontent = isset($_GET['idcontent']) ? $_GET['idcontent'] : die();
// read the details of user to be edited
$contents->readOneContent();
 
// create array
$contents_arr = array(
    "idcontent" => $contents->idcontent,
    "user_id" => $contents->user_id,
    "nickname" => $contents->nickname,
    "title" => $contents->title,
    "content" => $contents->content
);

if($contents_arr["idcontent"] !== NULL) {
    echo json_encode($contents_arr);
} else {
    echo json_encode(
        array("message" => "No Users are found.")
    );
}

