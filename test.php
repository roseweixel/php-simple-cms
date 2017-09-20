  public function write($p) {
    if ($p['title'])
      $title = mysql_real_escape_string($p['title']);
    if ($p['bodytext'])
      $bodytext = mysql_real_escape_string($p['bodytext']);
    if ($p['id']) {
      $sql = <<<MySQL_QUERY
        UPDATE %s
        SET title='%s', bodytext='%s'
        WHERE id={$p['id']};
MySQL_QUERY;

      $sql = sprintf($sql, self::TABLE_NAME, $title, $bodytext);

      if(mysql_query($sql)) {
        header("Location: {$_SERVER['PHP_SELF']}?entry={$p['id']}");
      } else {
        echo 'Something went wrong!';
      }
    } else {
      if ($title && $bodytext) {
        $created = time();

        $sql = <<<MySQL_QUERY
          INSERT INTO %s (title, bodytext, created) VALUES(
            '%s',
            '%s',
            '%s'
        )
MySQL_QUERY;

        $sql = sprintf($sql, self::TABLE_NAME, $title, $bodytext, $created);

        if(mysql_query($sql)) {
          $id = mysql_insert_id();
          header("Location: {$_SERVER['PHP_SELF']}?entry={$id}");
        } else {
          echo 'Something went wrong!';
        }
      } else {
        return false;
      }
    }
  }





