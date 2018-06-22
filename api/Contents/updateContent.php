<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../../config/DatabaseMysql.php';
include_once '../../models/Contents.php';
 
//instantiate database and Contents Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$contents = new  Contents($dbmysql);

//get raw user data
$data = json_decode(file_get_contents("php://input"));

// set ID property of user to be edited
$contents->idcontent = $data->idcontent;

//property value
$contents->title = $data->title;
$contents->content = $data->content;

//update User
if($contents->updateContent()) {
    echo json_encode(
        array('message' => 'contents info Updated')
    );
} else {
    echo json_encode(
        array('message' => 'contents Info Not Updated')
    );
}