<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Banner.php';

//instantiate database and banner Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$banner = new Banner($dbmysql);

//get raw content data
$data = json_decode(file_get_contents("php://input"));
print_r($data);
// bind data
// $banner->title = $data->title;
// $banner->description = $data->description;
// $banner->img = $data->img;
// $banner->startdate = $data->startdate;
// $banner->enddate = $data->enddate;
// $banner->position = $data->position;

//create User
// if($banner->createBanner()) {
//     echo json_encode(
//         array('message' => 'Banner Created')
//     );
// } else {
//     echo json_encode(
//         array('message' => 'Banner Not Created')
//     );
// }   
