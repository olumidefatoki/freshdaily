<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$refId = filter_var(isset($_REQUEST['order_reference']) ? TRIM($_REQUEST['order_reference']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$response = array('code' => 0, "message" => "Problem Understanding Request!");
//response corp Name, qty, price, MMarketer name,marketer phone,order id, date, total order amount,


if(empty($refId))
  $response["message"]="INCOMPLETE PARAMETER";
else{

  $orderDetailsList = array();
   $rs = getOrderDetailsByOrderId($refId); //var_dump($rs);
  $res = getOrderByRef($refId);
  foreach ($rs as $v) {
    $orderDetailsArray = array('cropName' => $v["cropName"], "qty" => $v["qty"],"price" => $v["amount"]);
    $orderDetailsList[]=$orderDetailsArray;
  }

  $resposeContent = array("totalAmount" =>$res["amount"],'marketerName' => $res["marketerName"], "marketerPhone" => $res["marketerPhone"],
                            "orderReference" => $res["orderReference"],"date" => $res["creationDate"], "orderDetailsList" => $orderDetailsList);

  //$orderCrop = array('corpName' => "Rice", "qty" => "3","Measurement" => "25KG","price" => "10.00");
  //$orderCrop1 = array('corpName' => "Bean", "qty" => "3","Measurement" => "25KG","price" => "15.00");
  //$orderCrop2= array('corpName' => "Soya", "qty" => "3","Measurement" => "25KG","price" => "20.00");
  //$orderCropList[]=$orderCrop;$orderCropList[]=$orderCrop1;$orderCropList[]=$orderCrop2;
  //$resposeContent = array("totalAmount" => 25000.00,"orderId" => "3",'marketerName' => "johnny", "marketerPhone" => "08060099476",
  //                          "orderReference" => "1234567890","date" => "2018-12-01 10:15:00", "orderCropList" => $orderCropList);
    $response["code"]=1;
    $response["message"]=$resposeContent;

}


header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
