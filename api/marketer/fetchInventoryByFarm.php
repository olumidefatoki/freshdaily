<?php
include ("../../adodb5/adodb.inc.php");
include('../../dbConnection.php');
include('../../constants.php');

//global $db;
//$db->debug=true;

//?state_id=1&lga_id=1&farm_name=test&address=test&contact_name=test&contact_phone=080
$farmId = filter_var(isset($_REQUEST['farm_id']) ? TRIM($_REQUEST['farm_id']) : NULL, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_STRIP_HIGH);
$response = array('code' => 0, "message" => "Problem Understanding Request!");

if(empty($farmId))
  $response["message"]="INCOMPLETE PARAMETER";
else{

$rs = fetchStockOutQty($farmId);
//echo '<pre>'; print_r($rs);

$rs1 = fetchStockInQty($farmId);
//echo '<pre>'; print_r($rs1);

$cropStockList = array();
foreach ($rs1 as $v1) {
//var_dump($v1);
  $stockBal=$v1["stock_in"];
  foreach ($rs as $v) {
    //var_dump($v);

    //echo 'stock_in' . $v1["stock_in"] .'<br>';
  //  echo 'stock_out' . $v["stock_out"] .'<br>';
    if ($v1["cropId"] == $v["cropId"] ) {
      $stockBal = $v1["stock_in"] - $v["stock_out"] ;
    }
  }
  $cropStock =  array('cropId' => $v1["cropId"],"cropName" => $v1["cropName"],"cropCategory" => $v1["cropCategory"],
                      "qty" => $stockBal,"price" => 12.00,'cropCategoryId' => $v1["cropCategoryId"] );
  $cropStockList[]= $cropStock;
}

$response["code"]=1;
$response["message"] = $cropStockList;
}

header('Content-type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE);
@ob_flush();
flush();
?>
