<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$farmId = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$start = filter_var(isset($_REQUEST['start']) ? TRIM($_REQUEST['start']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$response = array('code' => 0, "message" => "Problem Understanding Request!");


if(empty($farmId) || empty($start) )
  $response["message"]="INCOMPLETE PARAMETER";
else{
  $orderList = array();

  $rs = getOrderByFarm($farmId , $start);
  foreach ($rs as $v) {
    $orderArray = array('marketerName' => $v["marketerName"], "marketerPhone" => $v["marketerPhone"],"orderReference" => $v["orderReference"],
                        "date" => $v["creationDate"],"amount" => $v["amount"]);
    $orderList[]=$orderArray;
  }
  $response["code"]=1;
  $response["message"]=$orderList;

}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
