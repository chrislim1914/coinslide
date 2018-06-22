<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Contents.php';

//instantiate database and Contents Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$contents = new  Contents($dbmysql);

//get raw content data
$data = json_decode(file_get_contents("php://input"));

// bind data
$contents->user_id = $data->user_id;
$contents->title = $data->title;
$contents->content = $data->content;

//create User
if($contents->createContent()) {
    echo json_encode(
        array('message' => 'Content Created')
    );
} else {
    echo json_encode(
        array('message' => 'Content Not Created')
    );
}   
