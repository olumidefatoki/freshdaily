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

function insertFarm($farm_name,$contactPersonFirstName,$contactPersonLastName,$contact_phone,$state_id,$lga_id,$email,$address)
{
  global $db;

  $sql = "INSERT INTO farm(farm_name, contact_person_first_name,contact_person_last_name, contact_person_phone_number, state_id, lga_id,user_id,address) VALUES
  ("  . $db->qstr($farm_name, get_magic_quotes_gpc()) . "," . $db->qstr($contactPersonFirstName, get_magic_quotes_gpc()) . "," . $db->qstr($contactPersonLastName, get_magic_quotes_gpc()) . ",
  " . $db->qstr($contact_phone, get_magic_quotes_gpc()) . " , " . $db->qstr($state_id, get_magic_quotes_gpc()) . ",
  " . $db->qstr($lga_id, get_magic_quotes_gpc()) .",". $db->qstr($email, get_magic_quotes_gpc()) .",". $db->qstr($address, get_magic_quotes_gpc()) .")";
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
  //return $db->INSERT_ID();
}

function isExistingFarmName($farmName) {
  global $db;

  $sql = "SELECT farm_name FROM farm WHERE farm_name = " . $db->qstr(trim($farmName), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}
function isExistingFarmContactPhoneNumber($farmContactPhoneNumber) {
  global $db;

  $sql = "SELECT contact_person_phone_number FROM farm WHERE contact_person_phone_number = " . $db->qstr($farmContactPhoneNumber, get_magic_quotes_gpc())." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

function isExistingUsername($username) {
  global $db;
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

  $sql = "INSERT INTO user(username,password, notificiation_id,status_id, user_type) VALUES "
          ."("  . $db->qstr($username, get_magic_quotes_gpc()) . ",md5(" . $db->qstr($password, get_magic_quotes_gpc()) .") ,
          " . $db->qstr($notId, get_magic_quotes_gpc()) . ",1," . $userType . ")";
  $val = $db->Execute($sql);
  return $db->INSERT_ID();
}

function isValidUser($username,$password) {
  global $db;
  $sql = " SELECT f.id farmId, u.username , f.farm_name farmName, concat(f.contact_person_first_name, ' ', f.contact_person_last_name) contact_name ,
            f.contact_person_phone_number contact_phone_number FROM user u
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

function getOrderListByFarmByStatus($farmId, $start, $statusId) {
  global $db;

  $sql = "SELECT  concat(m.first_name,' ', m.last_name) marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
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

  $sql = "SELECT  c.name cropName ,pod.qty qty, pod.amount   FROM  produce_order ord
          INNER JOIN produce_order_details pod ON pod.produce_order_id  = ord.id
          INNER JOIN crop c ON c.id = pod.crop_id
          WHERE ord.order_reference = '" .$orderRef."'" ;
  return $db->GetAll($sql);

}
function getOrderByRef($refId) {
  global $db;

  $sql = "SELECT  concat(m.first_name,' ',m.last_name) marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
          ord.creation_date creationDate,ord.amount,ord.order_expected_date,ord.order_expected_time
          FROM  produce_order ord
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

  $sql = " SELECT c.id, c.name cropName, cc.name cropCategory, cc.id cropCategoryId FROM crop c
          INNER JOIN crop_category cc ON c.crop_category_id = cc .id "  ;
  return $db->GetAll($sql);
}

function isExistingFarmId($farmId) {
  global $db;

  $sql = "SELECT farm_name FROM farm WHERE id = " . $db->qstr(trim($farmId), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}
function updateUserNotificationId($farmId,$not_id)
{
  global $db;

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
function updateFarmProfile($farmId,$contactPersonFirstName,$contactPersonLastName,$contactPersonPhoneNumber) {
  global $db;

  $sql = " UPDATE  farm  SET contact_person_first_name = " . $db->qstr($contactPersonFirstName, get_magic_quotes_gpc()) ." ,
            contact_person_Last_name = " . $db->qstr($contactPersonLastName, get_magic_quotes_gpc()) .",
            contact_person_phone_number = " . $db->qstr($contactPersonPhoneNumber, get_magic_quotes_gpc()) ."
            WHERE id = " . $farmId ;
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
}
function updateOrderStatus($orderId,$statusId) {
  global $db;

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
          WHERE po.farm_id = ". $farmId . " AND po.status_id  IN(3,6,12)
          GROUP BY pod.crop_id";
  return  $db->GetAll($sql);
}
function fetchStockInQty($farmId) {
  global $db;

  $sql = " SELECT fs.crop_id cropId,c.name cropName, cc.name cropCategory , cc.id cropCategoryId, IFNULL(SUM(qty),0) stock_in
            FROM farm_stock  fs
            INNER JOIN crop c ON c.id = fs.crop_id
            INNER JOIN crop_category cc ON cc.id = crop_category_id
            WHERE  fs.farm_id = " . $farmId . "
          GROUP BY farm_id,crop_id ";
  return  $db->GetAll($sql);
}
function getFetchFarmList() {
  global $db;

  $sql = "SELECT f.id farmId, f.farm_name farmName, concat(f.contact_person_first_name ,' ',f.contact_person_Last_name)contact_person_name,
          f.contact_person_phone_number, st.name stateName, f.address,
          IF(LOCATE('_',l.name), SUBSTRING(l.name, (LOCATE('_', l.name) + 1)), l.name) lgaName
          FROM farm f
          INNER JOIN LGA l ON l.id = f.lga_id
          INNER JOIN  states st ON st.id = l.state_id "  ;
  return $db->GetAll($sql);
}

function getOrderListByMarkerter($marketerId, $start) {
  global $db;

  $sql = "SELECT st.name status,  concat(m.first_name,' ', m.last_name) marketerName ,m.phone_number marketerPhone, ord.order_reference orderReference,
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

  $sql = " SELECT m.id markerterId,  concat(m.first_name,' ',m.last_name) markerterName,  m.phone_number markerterPhoneNumber
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

function insertMarkerter($markerteFirstName,$markerterLastName,$markerterPhoneNumber,$address,$userId)
{
  global $db;

  $sql = "INSERT INTO markerter(first_name,last_name, phone_number, address, user_id, status_id) VALUES
  ("  . $db->qstr($markerteFirstName, get_magic_quotes_gpc()) . "," . $db->qstr($markerterLastName, get_magic_quotes_gpc()) . ",
  " . $db->qstr($markerterPhoneNumber, get_magic_quotes_gpc()) . "," . $db->qstr($address, get_magic_quotes_gpc()) . " , ". $userId .",1)";
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
  //return $db->INSERT_ID();
}

function isExistingNotificationId($notId) {
  global $db;

  $sql = "SELECT notificiation_id FROM user WHERE notificiation_id = " . $db->qstr($notId, get_magic_quotes_gpc())." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;

}

function getOrderListByFarm($farmId, $start) {
  global $db;

  $sql = "SELECT st.name status,  concat(f.contact_person_first_name,' ', f.contact_person_Last_name) farmContactPersonName,
          f.farm_name farmName,f.contact_person_phone_number farmContactPersonPhoneNumber, ord.order_reference orderReference,
          ord.creation_date creationDate , ord.amount  FROM  produce_order ord
          INNER JOIN farm f  ON f.id  = ord.farm_id
          INNER JOIN status st on st.id = ord.status_id
          WHERE ord.farm_id = " . $db->qstr($farmId, get_magic_quotes_gpc()) . "
          ORDER BY ord.creation_date DESC
          LIMIT $start , " . RECORD_SIZE ;
  return $db->GetAll($sql);

}

function isExistingMarketerId($marketerId) {
  global $db;

  $sql = "SELECT 1 FROM markerter WHERE id = " . $db->qstr(trim($marketerId), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

function isValidCropId($cropId) {
  global $db;

  $sql = "SELECT 1 FROM crop WHERE id = " . $db->qstr(trim($cropId), get_magic_quotes_gpc()) ." ";
  $rs = $db->getRow($sql);
  if (!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

function insertNewProduceOrder($farmId,$markerId,$amount,$orderExpectedDate,$orderExpectedTime)
{
  global $db;
  $orderRef = genCode(1);
  $sql = "INSERT INTO produce_order(order_reference, `farm_id`, `marketer_id`, `amount`,`order_expected_date`, `order_expected_time`) VALUES
  ("  . $db->qstr($orderRef, get_magic_quotes_gpc()) . "," . $db->qstr($farmId, get_magic_quotes_gpc()) . "," . $db->qstr($markerId, get_magic_quotes_gpc()) . ",
  " . $db->qstr($amount, get_magic_quotes_gpc()) . " , " . $db->qstr($orderExpectedDate, get_magic_quotes_gpc()) . ",
  " . $db->qstr($orderExpectedTime, get_magic_quotes_gpc()) .")";
  $val = $db->Execute($sql);
  return $db->INSERT_ID();
}
function insertNewProduceOrderDetails($produceOrderid,$OrderDetailsList)
{
  global $db;

  $sql0 = "INSERT INTO  produce_order_details(`produce_order_id`, `crop_id`, `qty`, `amount`) VALUES ";

  $sql = "";
  foreach ($OrderDetailsList as $v) {
    //var_dump($v);  //echo($v["corpid"]);
    $std = new stdClass();
    $std = $v;
    $sql .= "("  . $db->qstr($produceOrderid, get_magic_quotes_gpc()) . "," . $db->qstr($std->cropid, get_magic_quotes_gpc()) . "," . $db->qstr($std->qty, get_magic_quotes_gpc()) . ",
            " . $db->qstr($std->amount, get_magic_quotes_gpc()) . ") ,";
  }
    $sql = substr($sql, 0, -2);
    //echo " sql : " + $sql;
    $sql0 .= $sql;
    $val = $db->Execute($sql0);
    if (!$val)
      return 0;
    return 1;
}
function updateMarketerProfile($marketerId,$marketerFirstName,$marketerLastName,$markerterPhone) {
  global $db;

  $sql = " UPDATE  markerter  SET first_name = " . $db->qstr($marketerFirstName, get_magic_quotes_gpc()) ." ,
            last_name = " . $db->qstr($marketerLastName, get_magic_quotes_gpc()) .",
            phone_number = " . $db->qstr($markerterPhone, get_magic_quotes_gpc()) ."
            WHERE id = " .  $db->qstr($marketerId, get_magic_quotes_gpc()) ;
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
}

function isValidMarketerPassword($marketerId,$password) {
  global $db;

  $sql = " SELECT 1 FROM user u
            INNER JOIN markerter m  ON m.user_id =u.id
            WHERE m.id = " . $db->qstr(trim($marketerId), get_magic_quotes_gpc()) ."
            AND u.password = md5(". $db->qstr($password, get_magic_quotes_gpc()) .")";
            $rs = $db->getRow($sql);
  if(!$rs || !is_array($rs) || !sizeof($rs))
    return false;
  else
    return true;
}

function updateMarketerPassword($marketerId,$password)
{
  global $db;

  $sql = " UPDATE  user  SET password = md5(" . $db->qstr($password, get_magic_quotes_gpc()) .")   WHERE id IN (
           SELECT user_id FROM markerter where id = " . $db->qstr(trim($marketerId), get_magic_quotes_gpc()) ." ) ";
  $val = $db->Execute($sql);
  if (!$val)
  return 0;
  else return 1;
  //return $db->INSERT_ID();
}
function getNotIdByFarmId($farmId) {
  global $db;

  $sql = " SELECT u.notificiation_id FROM user u
          INNER JOIN farm f ON f.user_id = u.id
           WHERE  f.id = " . $farmId  ;
  return $db->GetOne($sql);
}
function getNotIdByOrderRef($orderRef) {
  global $db;

  $sql = " SELECT u.notificiation_id FROM markerter m
           INNER JOIN produce_order por on por.marketer_id = m.id
           INNER JOIN user u ON u.id = m.user_id
           WHERE  por.order_reference = " ."'$orderRef'";
  return $db->GetOne($sql);
}
function getStatusNameByStatusID($statusId) {
  global $db;

  $sql = " SELECT name FROM status WHERE id = " . $statusId  ;
  return $db->GetOne($sql);
}

function insertNewFarmCropPrice($farmId,$cropList)
{
  global $db;

  $sql0 = "INSERT INTO farm_crop_price(farm_id, crop_id, price,creation_date) VALUES";

  $sql = "";
  foreach ($cropList as $v) {
    //var_dump($v);  //echo($v["corpid"]);
    $std = new stdClass();
    $std = $v;
    $sql .= " (" . $db->qstr($farmId, get_magic_quotes_gpc()) . "," . $db->qstr($std->corpid, get_magic_quotes_gpc()) . ",
              " . $db->qstr($std->price, get_magic_quotes_gpc()) . ",NOW()), ";
  }
    $sql = substr($sql, 0, -2);
    //echo " sql : " + $sql;
    $sql0 .= $sql;
    $val = $db->Execute($sql0);
    if (!$val)
      return 0;
    return 1;
}

function getFarmCropPrice($farmId,$cropId) {
  global $db;

  $sql = "SELECT FORMAT(IFNULL(SUM(price),0),2) price
          FROM  farm_crop_price fcp
          WHERE  fcp.farm_id =  " . $db->qstr($farmId, get_magic_quotes_gpc()) ."
          AND fcp.crop_id =  " . $db->qstr($cropId, get_magic_quotes_gpc()) . "
          ORDER BY 1 LIMIT 1 ";
          return $db->GetOne($sql);
}



 ?>
