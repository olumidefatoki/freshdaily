<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$newPassword = filter_var(isset($_REQUEST['new_password']) ? TRIM($_REQUEST['new_password']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$oldPassword= filter_var(isset($_REQUEST['old_password']) ? TRIM($_REQUEST['old_password']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$marketerId = filter_var(isset($_REQUEST['marketer_id']) ? TRIM($_REQUEST['marketer_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$response = array('code' => 0, "message" => "Problem Understanding Request!");


if(empty($newPassword) ||  empty($oldPassword) || empty($marketerId))
  $response["message"]="INCOMPLETE PARAMETER";
else{
  if (! isExistingMarketerId($marketerId)) {
      $response["message"] = " Invalid Marketer Id";
  }
  else{
    if (! isValidMarketerPassword($marketerId, $oldPassword)) {
        $response["message"] = "Invalid Password";
    }
    else{
      iniTransaction();
      $val = updateMarketerPassword($marketerId,$newPassword);
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
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
