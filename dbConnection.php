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
function insertNewUser($username,$password)
{
  global $db;
  //$db->debug=true;
  $sql = "INSERT INTO user(username,password, status_id, user_type) VALUES "
  ."("  . $db->qstr($username, get_magic_quotes_gpc()) . ",md5(" . $db->qstr($password, get_magic_quotes_gpc()) ."),1,1)";
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

function getOrderByFarm($farmId, $start) {
  global $db;
  //$db->debug=true;
  $sql = "SELECT  m.name marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
          ord.creation_date creationDate , ord.amount  FROM  produce_order ord
          INNER JOIN markerter m  ON m.id  = ord.marketer_id
          WHERE farm_id = " .$farmId . "
          ORDER BY ord.creation_date DESC
          LIMIT " . $start . " , 10 ";
  return $db->GetAll($sql);

}
function getOrderDetailsByOrderId($orderId) {
  global $db;
  //$db->debug=true;
  $sql = "SELECT  m.name marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
          ord.creation_date creationDate , ord.amount  FROM  produce_order ord
          INNER JOIN markerter m  ON m.id  = ord.marketer_id
          WHERE farm_id = " .$farmId . "
          ORDER BY ord.creation_date DESC
          LIMIT " . $start . " , 10 ";
  return $db->GetAll($sql);

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

 ?>
