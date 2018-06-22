<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Users.php';
include_once '../../class/PasswordEncrypt.php';


//instantiate database and Users Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$user = new  Users($dbmysql);
$hash = new PasswordEncrypt();

//get raw user data
$data = json_decode(file_get_contents("php://input"));

// set ID property of user to be edited
$user->iduser = $data->iduser;
$data->password = $hash->hash($data->password);

//property value
$user->password = $data->password;
$user->iduser = $data->iduser;

//update User
if($user->updateUserPassword()) {
    echo json_encode(
        array('message' => 'User Password Updated')
    );
} else {
    echo json_encode(
        array('message' => 'User Password Not Updated')
    );
}