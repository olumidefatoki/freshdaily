<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
echo '<pre>'; print_r($_REQUEST);
$markerterName = filter_var(isset($_REQUEST['markerter_name']) ? TRIM($_REQUEST['markerter_name']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$markerterAddress = filter_var(isset($_REQUEST['markerter_address']) ? TRIM($_REQUEST['markerter_address']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$markerterPhone = filter_var(isset($_REQUEST['markerter_phone_number']) ? TRIM($_REQUEST['markerter_phone_number']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$username = filter_var(isset($_REQUEST['username']) ? TRIM($_REQUEST['username']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$password = filter_var(isset($_REQUEST['password']) ? TRIM($_REQUEST['password']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$notId = filter_var(isset($_REQUEST['not_id']) ? TRIM($_REQUEST['not_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

$response = array('code' => 0, "message" => "Problem Understanding Request!");
if(empty($markerterName) || empty($markerterAddress) || empty($markerterPhone) || empty($username) || empty($password) || empty($notId)  )
  $response["message"]="INCOMPLETE PARAMETER";
else{
  if (isExistingUsername($username)) {
      $response["message"]="username Already Exist";
  }
  else if (isExistingMarketerPhoneNumber($markerterPhone)) {
      $response["message"]="Phone Number Already Exist";
  }
  else{
   iniTransaction();
   $userId = insertNewUser($username,$password,$notId,2);
   $val = insertMarkerter($markerterName,$markerterPhone,$markerterAddress,$userId);
   if ($val != "1" || $db->hasFailedTrans()) {
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
