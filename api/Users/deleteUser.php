<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Users.php';

//instantiate database and Users Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$user = new  Users($dbmysql);

//get raw user data
$data = json_decode(file_get_contents("php://input"));

// set ID property of user to be deleted
$user->uid = $data->uid;

//update User
if($user->deleteUser()) {
    echo json_encode(
        array('message' => 'User info Deleted')
    );
} else {
    echo json_encode(
        array('message' => 'User Info Not Deleted')
    );
}