<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');
//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080

//$corpCategory = filter_var(isset($_REQUEST['crop_category']) ? TRIM($_REQUEST['crop_category']) : null, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);

$response = array('code' => 0, "message" => "Problem Understanding Request!");

    $corpList = array();
    $rs = getFetchFarmList();
    foreach ($rs as $v) {
      $cropArray = array('farmId' => $v["farmId"], "farmName" => $v["farmName"],
                          'contactName' => $v["contact_name"], "contactPhoneNumber" => $v["contact_phone_number"],
                          'stateName' => $v["stateName"], "lgaName" => $v["lgaName"]);
      $corpList[]=$cropArray;
    }
    $response["code"]=1;
    $response["message"]=$corpList;

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
