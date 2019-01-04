<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
//echo '<pre>'; print_r($_REQUEST);
$state_id = filter_var(isset($_REQUEST['state_id']) ? TRIM($_REQUEST['state_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$lga_id = filter_var(isset($_REQUEST['lga_id']) ? TRIM($_REQUEST['lga_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$farm_name = filter_var(isset($_REQUEST['farm_name']) ? TRIM($_REQUEST['farm_name']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$address = filter_var(isset($_REQUEST['address']) ? TRIM($_REQUEST['address']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$contact_name = filter_var(isset($_REQUEST['contact_name']) ? TRIM($_REQUEST['contact_name']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$contact_phone = filter_var(isset($_REQUEST['contact_phone']) ? TRIM($_REQUEST['contact_phone']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$username = filter_var(isset($_REQUEST['username']) ? TRIM($_REQUEST['username']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$password = filter_var(isset($_REQUEST['password']) ? TRIM($_REQUEST['password']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$not_id = filter_var(isset($_REQUEST['not_id']) ? TRIM($_REQUEST['not_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

$response = array('code' => 0, "message" => "Problem Understanding Request!");
if(empty($state_id) || empty($lga_id) || empty($farm_name) || empty($address) || empty($contact_name) ||  empty($contact_phone) || empty($username) || empty($password)  )
  $response["message"]="INCOMPLETE PARAMETER";
else{
  if (isExistingUsername($username)) {
      $response["message"]="username Already Exist";
  }
  else if (isExistingFarmName($farm_name)) {
      $response["message"]="Farm Name Already Exist";
  }
  else if (isExistingFarmContactPhoneNumber($contact_phone)) {
      $response["message"]="Farm Contact Phone Number Already Exist";
  }
  else{
   iniTransaction();
   $userId = insertNewUser($username,$password,$not_id,1);
   $val = insertFarm($farm_name,$contact_name,$contact_phone,$state_id,$lga_id,$userId);
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
