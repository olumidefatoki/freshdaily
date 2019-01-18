<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');
include_once('../../PushNotification.php');
//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080

$farmId = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$marketerId = filter_var(isset($_REQUEST['marketer_id']) ? TRIM($_REQUEST['marketer_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$totalAmount = filter_var(isset($_REQUEST['total_amount']) ? TRIM($_REQUEST['total_amount']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$deliveryDate = filter_var(isset($_REQUEST['delivery_date']) ? TRIM($_REQUEST['delivery_date']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$deliveryTime = filter_var(isset($_REQUEST['delivery_time']) ? TRIM($_REQUEST['delivery_time']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$cropList = isset($_REQUEST['crop_list']) ? TRIM($_REQUEST['crop_list']) : null;
$response = array('code' => 0, "message" => "Problem Understanding Request!");



//echo '<pre>'; print_r($_REQUEST);

if(empty($farmId) || empty($marketerId) || empty($totalAmount) || empty($deliveryDate) || empty($deliveryTime) || empty($cropList))
  $response["message"]="INCOMPLETE PARAMETER";
else{
  $cropListArray =  json_decode($cropList);

  if (! isExistingFarmId($farmId)) {
      $response["message"]="Invalid Farm Id";
  }
  else if (! isExistingMarketerId ($marketerId)) {
      $response["message"]="Invalid Marketer Id";
  }
  else if (! isValidCropIdList($cropListArray)) {
      $response["message"]="Invalid Crop Id";
  }
  //isExistingFarmId

  else{
    iniTransaction();
    $produceOrderid = insertNewProduceOrder($farmId,$marketerId,$totalAmount,$deliveryDate,$deliveryTime);
    $val = insertNewProduceOrderDetails($produceOrderid,$cropListArray);
    if ($val != "1" || $db->hasFailedTrans()) {
      completeTransaction(false);
      $response["message"]="An Error Occurred";
   }
   else{
     completeTransaction(true);
     $response["code"]=1;
     $response["message"]="Successful";
     
     $obj = new SendNotification();
     $message = 'New Order has been placed on your farm';
     $notId = getNotIdByFarmId($farmId);
     $obj->sendPushNotificationToFCMSever($notId, $message);

   }
  }
}
header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();




function isValidCropIdList($cropListArray){

  $cropIdList = "";
  foreach ($cropListArray as $v) {
    //var_dump($v);  //echo($v["corpid"]);
    $std = new stdClass();
    $std = $v;
    $rs =  isValidCropId($std->cropid);
    if ($rs != 'true') {
      return false;
    }
  }
return true;
}
?>
