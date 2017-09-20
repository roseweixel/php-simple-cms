<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SimpleCMS</title>
  <link rel="stylesheet" href="">
</head>
<body>

<?php

  include_once('simpleCMS.php');
  $obj = new simpleCMS();
  $obj->host = 'localhost:3306';
  $obj->username = 'root';
  $obj->table = 'my_test_database';
  $obj->connect();

  if ($_POST)
    $obj->write($_POST);

  echo ($_GET['admin'] == 1) ? $obj->display_admin() : $obj->display_public();

?>

</body>
</html>
