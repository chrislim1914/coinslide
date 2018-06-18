<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Users.php';

//instantiate database and Users Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$user = new  Users($dbmysql);

//get raw user data
$data = json_decode(file_get_contents("php://input"));

// set ID property of user to be edited
$user->uid = $data->uid;

//property value
$user->ufirstname = $data->ufirstname;
$user->ulastname = $data->ulastname;
$user->uemail = $data->uemail;
$user->unickname = $data->unickname;
$user->upw = $data->upw;

//update User
if($user->updateUser()) {
    echo json_encode(
        array('message' => 'User info Updated')
    );
} else {
    echo json_encode(
        array('message' => 'User Info Not Updated')
    );
}