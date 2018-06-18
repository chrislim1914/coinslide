<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Users.php';

//instantiate database and Users Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$users = new  Users($dbmysql);

//query statement for users
$stmt = $users->readUsers();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){
 
    // users array
    $users_arr=array();
    $users_arr["userslist"]=array();
 
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
        extract($row);

        $usersList = array(
            "iduser" => $iduser,
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $email,
            "phone" => $phone,
            "nickname" => $nickname,
            "password" => $password,
            "createdate" => $createdate,
            "national" => $national
        );
 
        array_push($users_arr["userslist"], $usersList);
    }
 
    echo json_encode($users_arr);
} else {
    echo json_encode(
        array("message" => "No Users are found.")
    );
}
 
