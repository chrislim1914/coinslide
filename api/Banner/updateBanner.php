<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// include database and object files
include_once '../../config/DatabaseMysql.php';
include_once '../../models/Banner.php';
 
//instantiate database and banner Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$banner = new  Banner($dbmysql);

//get raw user data
$data = json_decode(file_get_contents("php://input"));

// set ID property of user to be edited
$banner->idbanner = $data->idbanner;

//property value
$banner->title = $data->title;
$banner->description = $data->description;
$banner->img = $data->img;
$banner->startdate = $data->startdate;
$banner->enddate = $data->enddate;
$banner->position = $data->position;

//update User
if($banner->updateBanner()) {
    echo json_encode(
        array('message' => 'banner Updated')
    );
} else {
    echo json_encode(
        array('message' => 'banner Not Updated')
    );
}