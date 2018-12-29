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

function isValidToken($token) {
  global $db;

  $sql = "SELECT id FROM users WHERE reset_token = " . $db->qstr($token) . " ";
  $rs = $db->GetRow($sql);
  if ($rs && is_array($rs) && sizeof($rs))
    return true;
  return false;
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

  $sql = "SELECT $username FROM user WHERE username = " . $db->qstr(trim($username), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}
function insertNewUser($username,$password,$notId)
{
  global $db;
  //$db->debug=true;
  $sql = "INSERT INTO user(username,password, notificiation_id,status_id, user_type) VALUES "
  ."("  . $db->qstr($username, get_magic_quotes_gpc()) . ",md5(" . $db->qstr($password, get_magic_quotes_gpc()) .") ,'".$notId."',1,1)";
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
          WHERE ord.farm_id = " .$farmId . "
          AND ord.status_id = " .$statusId . "
          ORDER BY ord.creation_date DESC
          LIMIT " . $start . " , " . RECORD_SIZE ;
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

  return $code;
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

  $sql = "SELECT name FROM farm WHERE id = " . $db->qstr(trim($farmId), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

 ?>
