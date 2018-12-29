<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;
$farm_id = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$not_id = filter_var(isset($_REQUEST['not_id']) ? TRIM($_REQUEST['not_id']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

$response = array('code' => 0, "message" => "Problem Understanding Request!");

if(empty($farm_id) || empty($not_id))
  $response["message"]="INCOMPLETE PARAMETER";
else{
  if (! isExistingFarmId($farm_id)) {
      $response["message"] = " Invalid Farm Id";
  }
  else{
   iniTransaction();
   $val = updateUser($farm_id,$not_id);
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
