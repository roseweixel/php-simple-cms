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
        <input hidden value="$id" name="id" id="id" type="text" />

        <div class="form-group">
          <label class="control-label" for="title">Title:</label>
          <input class="form-control" value="$title" name="title" id="title" type="text" maxlength="150" />
        </div>
        <div class="form-group">
          <label class="control-label" for="bodytext">Body Text:</label>
          <textarea class="form-control" name="bodytext" id="bodytext">$bodytext</textarea>
        </div>

        <input class="btn btn-primary" type="submit" value="Save Changes" />
      </form>

ENTRY_DISPLAY;

    return $entryDisplay;
  }

  public function display_entries() {
    $query = "SELECT * FROM entries ORDER BY created DESC LIMIT 3";
    $resource = mysql_query($query);

    if ($resource !== false && mysql_num_rows($resource) > 0) {

      $entryDisplay = "<h1>Your Entries</h1>";

      while ($entry = mysql_fetch_assoc($resource)) {
        $title = stripslashes($entry['title']);
        $bodytext = stripslashes($entry['bodytext']);
        $created = stripslashes($entry['created']);
        $id = stripslashes($entry['id']);
        $date = gmdate("M j, Y H:i:s ", $created);

        $entryDisplay .= <<<ENTRY_DISPLAY

          <div class="entry">
            <div>$date</div>
            <h2>
              <a href="{$_SERVER['PHP_SELF']}?entry={$id}">$title</a>
              <form class="delete-form" action="{$_SERVER['PHP_SELF']}" method="post">
                <input name="_method" type="hidden" value="delete" />
                <input hidden value="$id" name="id" id="id" type="text" />
                <input class="btn btn-default" id="delete" name="delete" type="submit" value="Delete" />
              </form>
            </h2>
          </div>


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

    $entryDisplay .= <<<ADD_ENTRY_OPTION

      <p class="admin_link">
        <a class="btn btn-primary" href="{$_SERVER['PHP_SELF']}?addEntry=1">Add a New Entry</a>
      </p>

ADD_ENTRY_OPTION;

    return $entryDisplay;
  }

  public function display_entry_form() {
    return <<<ENTRY_FORM
      <h1>Create An Entry</h1>
      <form action="{$_SERVER['PHP_SELF']}" method="post">
        <div class="form-group">
          <label class="control-label" for="title">Title:</label>
          <input class="form-control" name="title" id="title" type="text" maxlength="150" />
        </div>
        <div class="form-group">
          <label class="control-label" for="bodytext">Body Text:</label>
          <textarea class="form-control" name="bodytext" id="bodytext"></textarea>
        </div>
        <input class="btn btn-primary" type="submit" value="Create This Entry!" />
      </form>

ENTRY_FORM;
  }

  public function delete($d) {
    if ($d['id']) {
      $sql = <<<MySQL_QUERY
        DELETE FROM %s
        WHERE id={$d['id']};
MySQL_QUERY;
      $sql = sprintf($sql, self::TABLE_NAME);

      if(mysql_query($sql)) {
        header("Location: {$_SERVER['PHP_SELF']}");
      } else {
        echo 'Something went wrong!';
      }
    } else {
      echo "Something went wrong!";
    }
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
    $createEntries = <<<createEntries
      CREATE TABLE IF NOT EXISTS entries (
        title VARCHAR(150),
        bodytext TEXT,
        created VARCHAR(100),
        id INTEGER NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (id)
    )
createEntries;

    $createTags = <<<createTags
      CREATE TABLE IF NOT EXISTS tags (
        name VARCHAR(150) NOT NULL,
        id INTEGER NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (id)
    )
createTags;

    $createEntryTags = <<<createEntryTags
      CREATE TABLE IF NOT EXISTS entry_tags (
        entry_id INTEGER NOT NULL,
        tag_id INTEGER NOT NULL,
        id INTEGER NOT NULL AUTO_INCREMENT,
        FOREIGN KEY (entry_id)REFERENCES entries(id)ON DELETE CASCADE
        FOREIGN KEY (tag_id)REFERENCES tags(id) ON DELETE CASCADE
        PRIMARY KEY (id)
    )
createEntryTags;

    $insertPHP = <<<insertPHP
      INSERT INTO tags(name)
      SELECT 'php' FROM dual WHERE NOT EXISTS (SELECT * from tags where name='php');
insertPHP;

    $insertRuby = <<<insertRuby
      INSERT INTO tags(name)
      SELECT 'ruby' FROM dual WHERE NOT EXISTS (SELECT * from tags where name='ruby');
insertRuby;

    $insertJavaScript = <<<insertJavaScript
      INSERT INTO tags(name)
      SELECT 'javascript' FROM dual WHERE NOT EXISTS (SELECT * from tags where name='javascript');
insertJavaScript;

    mysql_query($createEntries);
    mysql_query($createTags);
    mysql_query($createEntryTags);
    mysql_query($insertPHP);
    mysql_query($insertRuby);
    mysql_query($insertJavaScript);
  }
}

?>
