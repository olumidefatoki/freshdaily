<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$farmId = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$cropList = isset($_REQUEST['crop_list']) ? TRIM($_REQUEST['crop_list']) : NULL;
$response = array('code' => 0, "message" => "Problem Understanding Request!");

if(empty($farmId))
  $response["message"]="INCOMPLETE PARAMETER";
else if(! isExistingFarmId($farmId)) {
    $response["message"] = " Invalid Farm Id";
}
else{

  $corpList = array();
  $rs =getAllCropList(); //getFarmCropPrice($farmId);
  foreach ($rs as $v) {
    $v["price"] = getFarmCropPrice($farmId,$v["id"]);
    $cropArray = array('cropId' => $v["id"], "cropName" => $v["cropName"], "cropCategoryName" => $v["cropCategory"],
                        "cropCategoryId" =>$v["cropCategoryId"],"price" =>$v["price"]);
    $corpList[]=$cropArray;
  }
  $response["code"]=1;
  $response["message"]=$corpList;

}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
