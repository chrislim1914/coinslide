<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/DatabaseMysql.php';
include_once '../../models/Banner.php';

//instantiate database and banner Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$banner = new  Banner($dbmysql);

//query statement for banner 
$stmt = $banner->readAllBanners();
$num = $stmt->rowCount();

// check if more than 0 record found
if($num>0){
 
    // banner array
    $banner_arr=array();
    $banner_arr["data"]=array();
 
    // retrieve our table banner
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        
        extract($row);

        $bannerList = array(
            "idbanner" => $idbanner,
            "title" => $title,
            "description" => $description,
            "img" => $img,
            "startdate" => $startdate,
            "enddate" => $enddate,
            "position" => $position
        );
 
        array_push($banner_arr["data"], $bannerList);
    }
 
    echo json_encode($banner_arr);
} else {
    echo json_encode(
        array("message" => "No Banner yet!")
    );
}
 
