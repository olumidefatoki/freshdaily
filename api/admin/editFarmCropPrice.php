<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$farmId = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$cropList = isset($_REQUEST['crop_list']) ? TRIM($_REQUEST['crop_list']) : NULL;
$response = array('code' => 0, "message" => "Problem Understanding Request!");

if(empty($farmId) || empty($cropList))
  $response["message"]="INCOMPLETE PARAMETER";
else{

  $cropListArray =  json_decode($cropList);

  iniTransaction();
  $val = insertNewFarmCropPrice($farmId,$cropListArray);
  if ($val != "1" || $db->hasFailedTrans()) {
    completeTransaction(false);
    $response["message"]="An Error Occurred";
  }
  else{
    completeTransaction(true);
    $response["code"]=1;
    $response["message"]="Successful";
  }


//substr($clearedString, 0, -2)
  /*$orderCrop =  array('corpid' => 1, "qty" => "3");
  $orderCrop1= array('corpid' => 2, "qty" => "3");
  $orderCrop2=  array('corpid' => 3, "qty" => "3");
  $orderCropList[]=$orderCrop;$orderCropList[]=$orderCrop1;$orderCropList[]=$orderCrop2;
  $resposeContent = array("farmId" =>4, "orderCropList" => $orderCropList);*/

    //$response["code"]=1;
    //$response["message"]=$resposeContent;
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
