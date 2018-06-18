<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Users.php';
include_once '../../class/PasswordEncrypt.php';

//instantiate database and User Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$user = new Users($dbmysql);
$hash = new PasswordEncrypt();

//get raw user data
$data = json_decode(file_get_contents("php://input"));

//hash the password data using bcrypt
$data->password = $hash->hash($data->password);

// bind data
$user->first_name = $data->first_name;
$user->last_name = $data->last_name;
$user->email = $data->email;
$user->phone = $data->phone;
$user->nickname = $data->nickname;
$user->password = $data->password;
$user->national = $data->national;

//create User
if($user->createUser()) {
    echo json_encode(
        array('message' => 'User Created')
    );
} else {
    echo json_encode(
        array('message' => 'User Not Created')
    );
}