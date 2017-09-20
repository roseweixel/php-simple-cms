<?php
  include_once('simpleCMS.php');
  $obj = new simpleCMS();
  $obj->host = 'localhost:3306';
  $obj->username = 'root';
  $obj->table = 'my_test_database';
  $obj->connect();

  if ($_POST)
    $request_vars = array();
    $request_contents = file_get_contents('php://input');
    parse_str($request_contents, $request_vars);

    if ($request_vars['_method'] == 'delete') {
      $obj->delete($request_vars);
    } else {
      $obj->write($request_vars);
    }
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SimpleCMS</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
<?php

  if ($_GET['addEntry'] === '1') {
    echo $obj->display_entry_form();
  } elseif ($_GET['entry']) {
    echo $obj->display_entry($_GET['entry']);
  } else {
    echo $obj->display_entries();
  }

?>
</body>
</html>
