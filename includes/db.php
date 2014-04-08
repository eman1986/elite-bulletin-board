<?php
/*
Filename: db.php
Last Modified: 4/2/2008

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
class db {
  var $run;
  var $standby = false;

  function connect() {

     @mysql_connect(DB_HOST, DB_USER, DB_PASS) or die("Failed to connect to MySQL host.<br />". mysql_error() ."<br /><br /><strong>Line:</strong> ". __LINE__ ."<br /><strong>File:</strong> ". __FILE__);
     @mysql_select_db(DB_NAME) or die("Failed to select mysql DB.<br />". mysql_error() ."<br /><br /><strong>Line:</strong> ". __LINE__ ."<br /><strong>File:</strong> ". __FILE__);
  }

  function close() {
    @mysql_close($this->connect());
  }

  function query() {
  $this->connect();
  $errorq = $this->run;
    $query = @mysql_query($this->run) or die("Failed to query the database<br />". mysql_error() ."<br /><br /><strong>Line:</strong> ". __LINE__ ."<br /><strong>File:</strong> ". __FILE__."<br /><br />SQL Command:</strong><br /><textarea name=\"sqlquery\" rows=\"5\" cols=\"150\" class=\"text\" readonly=readonly>$errorq</textarea><br /><br />");
	if ($this->standby == false) {
	  $this->close();
	}
	return($query);
  }

  function num_results() {
  $this->connect();
    $total = @mysql_num_rows($this->query());
	return($total);
  }

  function result() {
  $this->connect();
    $result = @mysql_fetch_assoc($this->query());
	return($result);
  }
}
?>
