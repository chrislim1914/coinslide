<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../../class/Core.php';
include_once '../../class/Utilities.php';
include_once '../../config/DatabaseMysql.php';
include_once '../../models/Banner.php';

// instantiate database and banner object
$utilities = new Utilities();
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$banner = new  Banner($dbmysql);

// query banner
$stmt = $banner->bannerPagination($from_record_num, $records_per_page);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // banner array
    $banner_arr=array();
    $banner_arr["data"]=array();
    $banner_arr["paging"]=array();
 
    // retrieve our table banner
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
        $banner_item=array(
            "idbanner" => $idbanner,
            "title" => $title,
            "description" => $description,
            "img" => $img,
            "startdate" => $startdate,
            "enddate" => $enddate,
            "position" => $position
        );
 
        array_push($banner_arr["data"], $banner_item);
    }
 
 
    // include paging
    $total_rows=$banner->count();
    $page_url="http://localhost:8900/api/Banner/bannerPagination.php?";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $banner_arr["paging"]=$paging;
 
    echo json_encode($banner_arr);
}
 
else{
    echo json_encode(
        array("message" => "No banner found.")
    );
}