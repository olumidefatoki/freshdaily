<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');
//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080

$farmId = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$marketerId = filter_var(isset($_REQUEST['marketer_id']) ? TRIM($_REQUEST['marketer_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$totalAmount = filter_var(isset($_REQUEST['total_amount']) ? TRIM($_REQUEST['total_amount']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$deliveryDate = filter_var(isset($_REQUEST['delivery_date']) ? TRIM($_REQUEST['delivery_date']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$deliveryTime = filter_var(isset($_REQUEST['delivery_time']) ? TRIM($_REQUEST['delivery_time']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$corpList = isset($_REQUEST['crop_list']) ? TRIM($_REQUEST['crop_list']) : null;
$response = array('code' => 0, "message" => "Problem Understanding Request!");

if(empty($farmId) || empty($marketerId) || empty($totalAmount) || empty($deliveryDate) || empty($deliveryTime) || empty($corpList))
  $response["message"]="INCOMPLETE PARAMETER";
else{

    $corpList = array();
    $rs = getCropCategory();
    foreach ($rs as $v) {
      $cropArray = array('id' => $v["id"], "Name" => $v["name"]);
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
