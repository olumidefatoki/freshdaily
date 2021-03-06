<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$contactPersonFirstName = filter_var(isset($_REQUEST['contact_person_first_name']) ? TRIM($_REQUEST['contact_person_first_name']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$contactPersonLastName = filter_var(isset($_REQUEST['contact_person_last_name']) ? TRIM($_REQUEST['contact_person_last_name']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$contactPersonPhoneNumber = filter_var(isset($_REQUEST['contact_person_phone_number']) ? TRIM($_REQUEST['contact_person_phone_number']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$farmId = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$response = array('code' => 0, "message" => "Problem Understanding Request!");


if(empty($contactPersonFirstName) || empty($contactPersonLastName) || empty($contactPersonPhoneNumber) || empty($farmId))
  $response["message"]="INCOMPLETE PARAMETER";
else{
  if (! isExistingFarmId($farmId)) {
      $response["message"] = " Invalid Farm Id";
  }
  else{
    iniTransaction();
    $val = updateFarmProfile($farmId,$contactPersonFirstName,$contactPersonLastName,$contactPersonPhoneNumber);
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
