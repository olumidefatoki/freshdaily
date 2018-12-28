<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

$email = filter_var(isset($_REQUEST['email']) ? TRIM($_REQUEST['email']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$password = filter_var(isset($_REQUEST['password']) ? TRIM($_REQUEST['password']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

$response = array('code' => 0, "message" => "Problem Understanding Request!");


if(empty($email) || empty($password))
    $response["message"]="INCOMPLETE PARAMETER";
else{
    $rs = isValidUser($email, $password);
    if (!$rs || !is_array($rs) || !sizeof($rs)) {
      $response["message"]="Invalid user and or password  ";
    }
    else {
      $response["code"]=1;
      $response["message"] = array('farmName' => $rs["farmName"], "farmContactName" => $rs["contact_name"],
                                   "farmContactPhone" => $rs["contact_phone_number"],"farmId" => $rs["farmId"]);
    }


}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
