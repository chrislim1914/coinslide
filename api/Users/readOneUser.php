<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Users.php';

//instantiate database and User Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$users = new  Users($dbmysql);

//get user id
$users->iduser = isset($_GET['iduser']) ? $_GET['iduser'] : die();
// read the details of user to be edited
$users->readOneUser();
 
// create array
$users_arr = array(
    "iduser" => $users->iduser,
    "first_name" => $users->first_name,
    "last_name" => $users->last_name,
    "email" => $users->email,
    "phone" => $users->phone,
    "nickname" => $users->nickname,
    "password" => $users->password,
    "createdate" => $users->createdate,
    "national" => $users->national
);

if($users_arr["iduser"] !== NULL) {
    echo json_encode($users_arr);
} else {
    echo json_encode(
        array("message" => "No Users are found.")
    );
}
