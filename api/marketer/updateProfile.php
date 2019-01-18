<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$marketerFirstName = filter_var(isset($_REQUEST['markerter_first_name']) ? TRIM($_REQUEST['markerter_first_name']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$marketerLastName = filter_var(isset($_REQUEST['markerter_last_name']) ? TRIM($_REQUEST['markerter_last_name']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$markerterPhone = filter_var(isset($_REQUEST['markerter_phone_number']) ? TRIM($_REQUEST['markerter_phone_number']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$marketerId = filter_var(isset($_REQUEST['marketer_id']) ? TRIM($_REQUEST['marketer_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$response = array('code' => 0, "message" => "Problem Understanding Request!");


if(empty($marketerFirstName) || empty($marketerLastName) || empty($markerterPhone) || empty($marketerId))
  $response["message"]="INCOMPLETE PARAMETER";
else{
  if (! isExistingMarketerId($marketerId)) {
      $response["message"] = " Invalid Marketer Id";
  }
  else{
    iniTransaction();
    $val = updateMarketerProfile($marketerId,$marketerFirstName,$marketerLastName,$markerterPhone);
    if($val != "1" || $db->hasFailedTrans()) {
      completeTransaction(false);
      $response["message"]="An Error Occurred";
    }
  else{
     completeTransaction(true);
     $response["code"]=1;
     $response["message"]="Successful";
    }
  }
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
