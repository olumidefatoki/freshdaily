<?php

  date_default_timezone_set('UTC');
  define('DB_DRIVER', 'mysqli');
  define('DB_SERVER', 'localhost');
  define('DB_USERNAME', 'root');//latausr
  define('DB_PASSWORD', 'root');//Lusr-2016
  define('DB_NAME', 'fresh_daily');


  $db = ADONewConnection(DB_DRIVER);
  $db->connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

  $imgconv="jpg";

  $qrstartdate = "1900";
  $qrenddate = date("Y") + 1;

  $curdate = date("Y-m-d");

  define('RECORD_SIZE', '20');


  ?>
