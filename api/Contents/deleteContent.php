<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../../config/DatabaseMysql.php';
include_once '../../models/Contents.php';
 
//instantiate database and Contents Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$contents = new  Contents($dbmysql);

//get raw contents data
$data = json_decode(file_get_contents("php://input"));

// set ID property of contents to be deleted
$contents->idcontent = $data->idcontent;

//update contents
if($contents->deleteContent()) {
    echo json_encode(
        array('message' => 'contents info Deleted')
    );
} else {
    echo json_encode(
        array('message' => 'contents Info Not Deleted')
    );
}