<?php

  include_once('simpleCMS.php');
  $obj = new simpleCMS();
  $obj->host = 'localhost:3306';
  $obj->username = 'root';
  $obj->table = 'my_test_database';
  $obj->connect();

  if ($_POST)
    $obj->write($_POST);
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

  if ($_GET['admin'] == 1) {
    echo $obj->display_admin();
  } elseif ($_GET['entry']) {
    echo $obj->display_entry($_GET['entry']);
  } else {
    echo $obj->display_public();
  }

?>
</body>
</html>
