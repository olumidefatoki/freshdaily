<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');
//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080


$response = array('code' => 0, "message" => "Problem Understanding Request!");

    $corpList = array();
    $rs = getAllCropList();
    foreach ($rs as $v) {
      $cropArray = array('id' => $v["id"], "cropName" => $v["cropName"], "cropCategory" => $v["cropCategory"], "cropCategoryId" =>$v["cropCategoryId"]);
      $corpList[]=$cropArray;
    }
    $response["code"]=1;
    $response["message"]=$corpList;
//}
header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
