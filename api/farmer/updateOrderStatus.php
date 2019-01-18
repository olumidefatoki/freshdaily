<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');
include_once('../../PushNotification.php');
//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$orderId = filter_var(isset($_REQUEST['order_id']) ? TRIM($_REQUEST['order_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$statusId= filter_var(isset($_REQUEST['status_id']) ? TRIM($_REQUEST['status_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

$response = array('code' => 0, "message" => "Problem Understanding Request!");

if(empty($orderId))
  $response["message"]="INCOMPLETE PARAMETER";
else{
  iniTransaction();
  $val = updateOrderStatus($orderId,$statusId);
  if($val != "1" || $db->hasFailedTrans()) {
    completeTransaction(false);
    $response["message"]="An Error Occurred";
  }
else{
   completeTransaction(true);
   $response["code"]=1;
   $response["message"]="Successful";

   $obj = new SendNotification();
   $message = "Your Order " . $orderId ." has been " . getStatusNameByStatusID($statusId) ;
   $notId = getNotIdByOrderRef($orderId);
   $obj->sendPushNotificationToFCMSever($notId, $message);
  }
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();


?>
