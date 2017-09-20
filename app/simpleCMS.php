<?php

class simpleCMS {
  var $host;
  var $username;
  var $password;
  var $table;

  const TABLE_NAME = 'entries';

  public function display_entry($id) {
    $query = "SELECT * FROM entries WHERE id = $id";
    $resource = mysql_query($query);

    $entry = mysql_fetch_assoc($resource);
    $title = stripslashes($entry['title']);
    $bodytext = stripslashes($entry['bodytext']);

    $entryDisplay .= <<<ENTRY_DISPLAY

      <h3><a href="{$_SERVER['PHP_SELF']}">Back To View All Entries</a></h3>

      <h2>$title</h2>
      <p>
        $bodytext
      </p>

      <form action="{$_SERVER['PHP_SELF']}" method="post">
        <label for="title">Title:</label>
        <input hidden value="$id" name="id" id="id" type="text" />
        <input value="$title" name="title" id="title" type="text" maxlength="150" />
        <label for="bodytext">Body Text:</label>
        <textarea name="bodytext" id="bodytext">$bodytext</textarea>
        <input type="submit" value="Edit This Entry!" />
      </form>

ENTRY_DISPLAY;

    return $entryDisplay;
  }

  public function display_public() {
    $query = "SELECT * FROM entries ORDER BY created DESC LIMIT 3";
    $resource = mysql_query($query);

    if ($resource !== false && mysql_num_rows($resource) > 0) {
      while ($entry = mysql_fetch_assoc($resource)) {
        $title = stripslashes($entry['title']);
        $bodytext = stripslashes($entry['bodytext']);
        $id = stripslashes($entry['id']);

        $entryDisplay .= <<<ENTRY_DISPLAY

          <h2><a href="{$_SERVER['PHP_SELF']}?entry={$id}">$title</a></h2>

ENTRY_DISPLAY;
      }
    } else {
      $entryDisplay = <<<ENTRY_DISPLAY

        <h2>This Page Is Under Construction</h2>
        <p>
          No entries have been made on this page.
          Please check back soon, or click the link below to add an entry!
        </p>

ENTRY_DISPLAY;
    }

    $entryDisplay .= <<<ADMIN_OPTION

      <p class="admin_link">
        <a href="{$_SERVER['PHP_SELF']}?admin=1">Add a New Entry</a>
      </p>

ADMIN_OPTION;

    return $entryDisplay;
  }

  public function display_admin() {
    return <<<ADMIN_FORM

      <form action="{$_SERVER['PHP_SELF']}" method="post">
        <label for="title">Title:</label>
        <input name="title" id="title" type="text" maxlength="150" />
        <label for="bodytext">Body Text:</label>
        <textarea name="bodytext" id="bodytext"></textarea>
        <input type="submit" value="Create This Entry!" />
      </form>

ADMIN_FORM;
  }

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

  public function connect() {
    mysql_connect(
      $this->host,
      $this->username
    ) or die("Could not connect." . mysql_error());

    mysql_select_db(
      $this->table
    ) or die("Could not select database." . mysql_error());

    return $this->buildDB();
  }

  private function buildDB() {
    $sql = <<<MySQL_QUERY
      CREATE TABLE IF NOT EXISTS %s (
        title VARCHAR(150),
        bodytext TEXT,
        created VARCHAR(100),
        id MEDIUMINT NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (id)
    )
MySQL_QUERY;

    $sql = sprintf($sql, self::TABLE_NAME);

    return mysql_query($sql);
  }
}

?>
