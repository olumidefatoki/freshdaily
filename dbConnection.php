<?php
function iniTransaction() {
  global $db;
  $db->StartTrans();
}
function completeTransaction($autoComplete = true, $unLockTables = true) {
  global $db, $success;
  $db->CompleteTrans($autoComplete);

  if ($unLockTables)
    unLockTables();

  if ($autoComplete)
    $success = 1;
  else
    $success = 0;

  //checkAndResetFormToken($autoComplete);
  if ($autoComplete)
    $_REQUEST['iID'] = null;
  return "The Requested Operation Was Successfully<br />Completed";
}

function unLockTables() {
  global $db;

  $sql = "UNLOCK TABLES";
  $db->Execute($sql);
}

function insertFarm($farm_name,$contact_name,$contact_phone,$state_id,$lga_id,$email)
{
  global $db;
  //$db->debug=true;
  $sql = "INSERT INTO farm(name, contact_name, contact_phone_number, state_id, lga_id,user_id) VALUES
  ("  . $db->qstr($farm_name, get_magic_quotes_gpc()) . "," . $db->qstr($contact_name, get_magic_quotes_gpc()) . ",
  " . $db->qstr($contact_phone, get_magic_quotes_gpc()) . " , " . $db->qstr($state_id, get_magic_quotes_gpc()) . ",
  " . $db->qstr($lga_id, get_magic_quotes_gpc()) .",". $db->qstr($email, get_magic_quotes_gpc()) .")";
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
  //return $db->INSERT_ID();
}

function isExistingFarmName($farmName) {
  global $db;

  $sql = "SELECT name FROM farm WHERE name = " . $db->qstr(trim($farmName), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}
function isExistingFarmContactPhoneNumber($farmContactPhoneNumber) {
  global $db;

  $sql = "SELECT contact_phone_number FROM farm WHERE contact_phone_number = " . $db->qstr($farmContactPhoneNumber, get_magic_quotes_gpc())." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

function isExistingUsername($username) {
  global $db;
  $db->debug=true;
  $sql = "SELECT username FROM user WHERE username = " . $db->qstr(trim($username), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

function insertNewUser($username,$password,$notId,$userType)
{
  global $db;
  //$db->debug=true;
  $sql = "INSERT INTO user(username,password, notificiation_id,status_id, user_type) VALUES "
          ."("  . $db->qstr($username, get_magic_quotes_gpc()) . ",md5(" . $db->qstr($password, get_magic_quotes_gpc()) .") ,
          " . $db->qstr($notId, get_magic_quotes_gpc()) . ",1," . $userType . ")";
  $val = $db->Execute($sql);
  return $db->INSERT_ID();
}

function isValidUser($username,$password) {
  global $db;
  //$db->debug=true;
  $sql = " SELECT f.id farmId, u.username , f.name farmName, f.contact_name, f.contact_phone_number FROM user u
            INNER JOIN farm f  ON f.user_id =u.id
            WHERE u.username = " . $db->qstr(trim($username), get_magic_quotes_gpc()) ."
            AND u.password = md5(". $db->qstr($password, get_magic_quotes_gpc()) .")";
  return  $db->getRow($sql);

}

function getCropByCropCategory($cropCategoryId) {
  global $db;

  $sql = "SELECT id, name FROM crop WHERE crop_category_id = " .$cropCategoryId ;
  return $db->GetAll($sql);

}

function getOrderListByFarm($farmId, $start, $statusId) {
  global $db;
  //$db->debug=true;
  $sql = "SELECT  m.name marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
          ord.creation_date creationDate , ord.amount  FROM  produce_order ord
          INNER JOIN markerter m  ON m.id  = ord.marketer_id
          WHERE ord.farm_id = " . $db->qstr($farmId, get_magic_quotes_gpc()) . "
          AND ord.status_id = " .$db->qstr($statusId, get_magic_quotes_gpc()) . "
          ORDER BY ord.creation_date DESC
          LIMIT $start , " . RECORD_SIZE ;
  return $db->GetAll($sql);

}
function getOrderDetailsByOrderId($orderRef) {
  global $db;
  //$db->debug=true;
  $sql = "SELECT  c.name cropName ,pod.qty qty, pod.amount   FROM  produce_order ord
          INNER JOIN produce_order_details pod ON pod.produce_order_id  = ord.id
          INNER JOIN crop c ON c.id = pod.crop_id
          WHERE ord.order_reference = '" .$orderRef."'" ;
  return $db->GetAll($sql);

}
function getOrderByRef($refId) {
  global $db;
  //$db->debug=true;
  $sql = "SELECT  m.name marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
          ord.creation_date creationDate , ord.amount  FROM  produce_order ord
          INNER JOIN markerter m  ON m.id  = ord.marketer_id
          WHERE order_reference = '" .$refId ."' ";
  return $db->getRow($sql);

}

function genCode($id) {
  global $db;

  $sql = "SELECT prefix, next_num FROM code_gen WHERE id = $id FOR UPDATE";
  $rs = $db->Execute($sql);

  $code = $rs->fields['prefix']  . $rs->fields['next_num'];

  $sql = "UPDATE code_gen SET next_num = next_num + 1 WHERE id = $id ";
  $db->Execute($sql);

  $db->Execute('COMMIT');

  return strtoupper($code);
}

function getCropCategory() {
  global $db;
  $sql = "SELECT id, name FROM crop_category "  ;
  return $db->GetAll($sql);
}
function getStatusList() {
  global $db;
  $sql = "SELECT id, name FROM status "  ;
  return $db->GetAll($sql);
}
function getAllCropList() {
  global $db;
  //$db->debug=true;
  $sql = " SELECT c.id, c.name cropName, cc.name cropCategory, cc.id cropCategoryId FROM crop c
          INNER JOIN crop_category cc ON c.crop_category_id = cc .id "  ;
  return $db->GetAll($sql);
}

function isExistingFarmId($farmId) {
  global $db;
  //$db->debug=true;
  $sql = "SELECT name FROM farm WHERE id = " . $db->qstr(trim($farmId), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}
function updateUserNotificationId($farmId,$not_id)
{
  global $db;
  //$db->debug=true;
  $sql = " UPDATE  user  SET notificiation_id = '" . $not_id . "'   WHERE id IN (
           SELECT user_id FROM farm where id = " . $db->qstr(trim($farmId), get_magic_quotes_gpc()) ." ) ";
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
  //return $db->INSERT_ID();
}

function updateUserPassword($farmId,$password)
{
  global $db;
  //$db->debug=true;
  $sql = " UPDATE  user  SET password = md5(" . $db->qstr($password, get_magic_quotes_gpc()) .")   WHERE id IN (
           SELECT user_id FROM farm where id = " . $db->qstr(trim($farmId), get_magic_quotes_gpc()) ." ) ";
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
  //return $db->INSERT_ID();
}

function isValidPassword($farmId,$password) {
  global $db;
  //$db->debug=true;
  $sql = " SELECT 1 FROM user u
            INNER JOIN farm f  ON f.user_id =u.id
            WHERE f.id = " . $db->qstr(trim($farmId), get_magic_quotes_gpc()) ."
            AND u.password = md5(". $db->qstr($password, get_magic_quotes_gpc()) .")";
            $rs = $db->getRow($sql);
  if(!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}
function updateFarmProfile($farmId,$contactPersonName,$contactPersonPhoneNumber) {
  global $db;
  //$db->debug=true;
  $sql = " UPDATE  farm  SET contact_name = " . $db->qstr($contactPersonName, get_magic_quotes_gpc()) ." ,
            contact_phone_number = " . $db->qstr($contactPersonPhoneNumber, get_magic_quotes_gpc()) ."
            WHERE id = " . $farmId ;
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
}
function updateOrderStatus($orderId,$statusId) {
  global $db;
  //$db->debug=true;
  $sql = " UPDATE   produce_order  SET status_id = " . $db->qstr($statusId, get_magic_quotes_gpc()) ."
          WHERE   order_reference = " . $db->qstr($orderId, get_magic_quotes_gpc());

  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
}

function insertNewStock($farmId,$cropList)
{
  global $db;
  //$db->debug=true;
  $sql0 = "INSERT INTO farm_stock(farm_id,crop_id, qty,creation_date) VALUES";

  $sql = "";
  foreach ($cropList as $v) {
    //var_dump($v);  //echo($v["corpid"]);
    $std = new stdClass();
    $std = $v;
    $sql .= " (" . $db->qstr($farmId, get_magic_quotes_gpc()) . ", " . $db->qstr($std->cropid, get_magic_quotes_gpc())  . ",
	 " . $db->qstr($std->qty, get_magic_quotes_gpc()) . ",NOW()), ";
  }
    $sql = substr($sql, 0, -2);
    //echo " sql : " + $sql;
    $sql0 .= $sql;
    $val = $db->Execute($sql0);
    if (!$val)
      return 0;
    return 1;
}

function fetchStockOutQty($farmId) {
  global $db;
  //$db->debug=true;
  /*$sql = " SELECT pod.crop_id,
            IFNULL(SUM( CASE WHEN po.status_id = 3 THEN pod.qty ELSE '0' END ), 0) AS 'PENDING',
            IFNULL(SUM( CASE WHEN po.status_id = 6 THEN pod.qty ELSE '0' END ), 0) AS 'ACCEPTED',
            IFNULL(SUM( CASE WHEN po.status_id = 11 THEN pod.qty ELSE '0' END ), 0) AS 'REJECTED'
            FROM produce_order po
            INNER JOIN produce_order_details pod ON pod.produce_order_id = po.id
            WHERE po.farm_id =  " . $farmId . "
            group by pod.crop_id";*/
  $sql = "SELECT pod.crop_id cropId, IFNULL(SUM(pod.qty),0) stock_out
          FROM produce_order po
          INNER JOIN produce_order_details pod ON pod.produce_order_id = po.id
          WHERE po.farm_id = ". $farmId . " AND po.status_id  IN(3,6)
          GROUP BY pod.crop_id";
  return  $db->GetAll($sql);
}
function fetchStockInQty($farmId) {
  global $db;
  //$db->debug=true;
  $sql = " SELECT fs.crop_id cropId,C.name cropName, cc.name cropCategory ,IFNULL(SUM(qty),0) stock_in
            FROM farm_stock  fs
            INNER JOIN crop c ON c.id = fs.crop_id
            INNER JOIN crop_category cc ON cc.id = crop_category_id
            WHERE  fs.farm_id = " . $farmId . "
          GROUP BY farm_id,crop_id ";
  return  $db->GetAll($sql);
}
function getFetchFarmList() {
  global $db;
  //$db->debug=true;
  $sql = "SELECT st.id farmId, f.name farmName, f.contact_name, f.contact_phone_number, st.name stateName,
          IF(LOCATE('_',l.name), SUBSTRING(l.name, (LOCATE('_', l.name) + 1)), l.name) lgaName
          FROM farm f
          INNER JOIN LGA l ON l.id = f.lga_id
          INNER JOIN  states st ON st.id = l.state_id "  ;
  return $db->GetAll($sql);
}

function getOrderListByMarkerter($marketerId, $start) {
  global $db;
  //$db->debug=true;
  $sql = "SELECT st.name status,  m.name marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
          ord.creation_date creationDate , ord.amount  FROM  produce_order ord
          INNER JOIN markerter m  ON m.id  = ord.marketer_id
          INNER JOIN status st on st.id = ord.status_id
          WHERE ord.marketer_id = " . $db->qstr($marketerId, get_magic_quotes_gpc()) . "
          ORDER BY ord.creation_date DESC
          LIMIT $start , " . RECORD_SIZE ;
  return $db->GetAll($sql);

}

function isValidMarkerter($username,$password) {
  global $db;
  //$db->debug=true;
  $sql = " SELECT m.id markerterId,  m.name markerterName,  m.phone_number markerterPhoneNumber
            FROM markerter m
            INNER JOIN user u  ON m.user_id =u.id
            WHERE u.username = " . $db->qstr(trim($username), get_magic_quotes_gpc()) ."
            AND u.password = md5(". $db->qstr($password, get_magic_quotes_gpc()) .")";
  return  $db->getRow($sql);

}

function isExistingMarketerPhoneNumber($marketerPhoneNumber) {
  global $db;

  $sql = "SELECT phone_number FROM markerter WHERE phone_number = " . $db->qstr($marketerPhoneNumber, get_magic_quotes_gpc())." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

function insertMarkerter($markerterName,$markerterPhoneNumber,$address,$userId)
{
  global $db;
  //$db->debug=true;
  $sql = "INSERT INTO markerter(`name`, `phone_number`, `address`, `user_id`, `status_id`) VALUES
  ("  . $db->qstr($markerterName, get_magic_quotes_gpc()) . "," . $db->qstr($markerterPhoneNumber, get_magic_quotes_gpc()) . ",
  " . $db->qstr($address, get_magic_quotes_gpc()) . " , ". $userId .",1)";
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
  //return $db->INSERT_ID();
}
 ?>
