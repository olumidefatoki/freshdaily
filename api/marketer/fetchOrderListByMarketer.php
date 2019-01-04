<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$marketerId = filter_var(isset($_REQUEST['marketer_id']) ? TRIM($_REQUEST['marketer_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$start = filter_var(isset($_REQUEST['start']) ? TRIM($_REQUEST['start']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
//$statusId = filter_var(isset($_REQUEST['status_id']) ? TRIM($_REQUEST['status_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

$response = array('code' => 0, "message" => "Problem Understanding Request!");


if(empty($marketerId) ||  empty($start) )
  $response["message"]="INCOMPLETE PARAMETER";
else{
  $orderList = array();

  $rs = getOrderListByMarkerter($marketerId,$start);
  foreach ($rs as $v) {
    $orderArray = array('marketerName' => $v["marketerName"], "marketerPhone" => $v["marketerPhone"],"orderReference" => $v["orderReference"],
                        "date" => $v["creationDate"],"amount" => $v["amount"],"status" => $v["status"]);
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
