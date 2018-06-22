<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../../class/Core.php';
include_once '../../class/Utilities.php';
include_once '../../config/DatabaseMysql.php';
include_once '../../models/Contents.php';

// instantiate database and contents object
$utilities = new Utilities();
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$contents = new  Contents($dbmysql);

// query contents
$stmt = $contents->readPaging($from_record_num, $records_per_page);
$num = $stmt->rowCount();
 
// check if more than 0 record found
if($num>0){
 
    // contents array
    $contents_arr=array();
    $contents_arr["data"]=array();
    $contents_arr["paging"]=array();
 
    // retrieve our table contents
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);
 
        $contents_item=array(
            "idcontent" => $idcontent,
            "user_id" => $user_id,
            "nickname" => $nickname,
            "title" => $title,
            "content" => $content
        );
 
        array_push($contents_arr["data"], $contents_item);
    }
 
 
    // include paging
    $total_rows=$contents->count();
    $page_url="http://localhost:8900/api/contents/readPaging.php?";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url);
    $contents_arr["paging"]=$paging;
 
    echo json_encode($contents_arr);
}
 
else{
    echo json_encode(
        array("message" => "No contents found.")
    );
}