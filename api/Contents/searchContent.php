<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
// include database and object files
include_once '../../config/DatabaseMysql.php';
include_once '../../models/Contents.php';
 
//instantiate database and Contents Object
$databaseMysql = new DatabaseMysql();
$dbmysql = $databaseMysql->connect();
$contents = new  Contents($dbmysql);
 
// get keywords
$keywords=isset($_GET["search"]) ? $_GET["search"] : "";

if($keywords == null) {
    echo json_encode(
        array("message" => "No search string found.")
    );
} else {
    // query content
    $stmt = $contents->searchContent($keywords);
    $num = $stmt->rowCount();
    
    // check if more than 0 record found
    if ($num>0) {
    
    // contents array
    $contents_arr=array();
    $contents_arr["data"]=array();
    
        // retrieve our table contents
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);
    
            $contentsList=array(
                "idcontent" => $idcontent,
                "nickname" => $nickname,
                "title" => html_entity_decode($title)
            );
    
            array_push($contents_arr["data"], $contentsList);
        }
    
        echo json_encode($contents_arr);
    } else {
        echo json_encode(
            array("message" => "No content found.")
        );
    }
} 
