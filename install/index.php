<?php
define('IN_EBB', true);
/*
Filename: index.php
Last Modified: 10/17/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
require "../template.php";

function makeRandomHash() {
  $salt = "/*-+^&~abchefghjkmnpqrstuvwxyz0123456789";
  srand((double)microtime()*1000000);
  	$i = 0;
  	$pass = '';
  	while ($i <= 7) {
    		$num = rand() % 33;
    		$tmp = substr($salt, $num, 1);
    		$pass = $pass . $tmp;
    		$i++;
  	}
  	return ($pass);
}
$page = new template("../template/clearblue2/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "Elite Bulletin Board",
  "PAGETITLE" => "Version 2.1 Final Installer"));

$page->output();
if(isset($_GET['cmd'])){
	$cmd = $_GET['cmd'];
}else{
	$cmd = '';
}
#get top.
echo '<div class="td1"><a href="index.php"><img src="../template/clearblue2/images/logo.gif" alt="" /></a></div>
<h3 class="td2">Version 2.1 Final Installer</h3>';

switch($cmd){
case 'create':
#see if config file is already writtened.
$config_path = '../config.php';
$file_size = filesize($config_path);
if($file_size > 0){
	echo '<h2 class="td2">ERROR!</h2>
	<h3 class="td1">The config file already contains database data.</h3>';
	exit();
}
	echo '<table border="0" class="table" align="center" cellspacing="1" cellpadding="3">
	<tr>
	<td align="center" class="td1" width="16%">Welcome</td>
	<td align="center" class="td2" width="17%">MySQL Connection Setup</td>
	<td align="center" class="td1" width="17%">Create Tables</td>
	<td align="center" class="td1" width="17%">Setup Settings</td>
	<td align="center" class="td1" width="16%">Create User</td>
	<td align="center" class="td1" width="17%">Create Category/Board</td>
	</tr>
	</table><hr />
	
	<form method="post" action="index.php?cmd=write">
	<div class="td1">MySQL Connection Setup</div><br />
	<div class="td2">MySQL Host<br />
	<input type="text" name="host" value="localhost" class="text" size="25" />
	<hr />
	MySQL Database<br />
	<input type="text" name="database" class="text" size="25" />
	<hr />
	MySQL Username<br />
	<input type="text" name="user" class="text" size="25" />
	<hr />
	MySQL Password<br />
	<input type="password" name="password" class="text" size="25" />
	<hr />
	<div align="center"><input type="submit" value="Create Connection File" class="submit" /></div>
	</div></form><br />';	
break; 
case 'write':
#see if config file is already writtened.
$config_path = '../config.php';
$file_size = filesize($config_path);
if($file_size > 0){
	echo '<div class="attachheader">ERROR!</div>
	<p class="titlebar">The config file already contains database data.</p>';
	exit();
}
	echo '<table border="0" class="table" align="center" cellspacing="1" cellpadding="3">
	<tr>
	<td align="center" class="td1" width="16%">Welcome</td>
	<td align="center" class="td2" width="17%">MySQL Connection Setup</td>
	<td align="center" class="td1" width="17%">Create Tables</td>
	<td align="center" class="td1" width="17%">Setup Settings</td>
	<td align="center" class="td1" width="16%">Create User</td>
	<td align="center" class="td1" width="17%">Create Category/Board</td>
	</tr>
	</table><br />';
	#get form values.
	$host = stripslashes($_POST['host']);
	$database = stripslashes($_POST['database']);
	$user = stripslashes($_POST['user']);
	$password = stripslashes($_POST['password']);
	#error check.
	if((empty($host)) or (empty($database)) or (empty($user)) or (empty($password))){
		echo '<p class="td2">You did not fill out the database connection correctly. Go back and fill in the missing fields.</p>';
		echo '[<b class="td2"><a href="javascript:history.back()">Go Back</a></b>]'; 
	}else{
		#unique password salt.
		$pass = makeRandomHash();
		#config data.
$data = "<?php
if (!defined('IN_EBB') ) {
	die('<b>!!ACCESS DENIED HACKER!!</b>');
}

#Elite Bulletin Board installer created this file.

// Database Connection Settings.

define('DB_HOST', '$host'); //usually this is localhost if it isnt ask your provider
define('DB_NAME', '$database'); //Name of your Database
define('DB_USER', '$user'); //Username of Database
define('DB_PASS', '$password'); //Password of database

//password encryption salt. This was created for you during the install. DO NOT ALTER THIS VALUE!!
define('PWDSALT', '$pass');
?>";
		#write the file.
		$filename = '../config.php';
		// Let's make sure the file exists and is writable first.
		if (is_writable($filename)) {
			if (!$handle = fopen($filename, 'a')){
		         echo "Cannot open file ($filename)";
		         exit();
			}

			// Write data to config file.
			if (fwrite($handle, $data) === false){
		       echo "Cannot write to file ($filename)";
		       exit();
			}
			echo '<p class="td2">Successfully created config file. onto <a href=\"install.php?step=install_1\">step 2</a>.</p>';
			fclose($handle);
		}else{
			echo '<p class="td2">The file $filename is not writable</p>';
		}
	}
	break;
	default:
	echo '<table border="0" class="table" align="center" cellspacing="1" cellpadding="3">
<tr>
<td align="center" class="td2" width="16%">Welcome</td>
<td align="center" class="td1" width="17%">MySQL Connection Setup</td>
<td align="center" class="td1" width="17%">Create Tables</td>
<td align="center" class="td1" width="17%">Setup Settings</td>
<td align="center" class="td1" width="16%">Create User</td>
<td align="center" class="td1" width="17%">Create Category/Board</td>
</tr>
</table>

<div class="td2">
<h4>This is the install file that will install Elite Bulletin Board version 2.1 Final onto your website. once this is complete, you will be able to enjoy this board on your website.</h4>
<h4><b>Clean Installation of Version 2.1 Final</b></h4>
<h4>If you plan to do a clean installation of Elite Bulletin Board, <b><a href="index.php?cmd=create">click here</a></b> to begin the clean installation.</h4>
<hr>
<h4>Upgrading from Version 2</h4>
<h4>If you installed version 2 of Elite Bulletin Board, please use the <b><a href="upgrade.php">version 2 upgrade</a></b> file to keep your current setup.</h4>
<hr>
<p><b>Permission Stats</b></p>';
#check install folder.
	if(!is_writable("../")){
		echo '<p class="raisewarn">The install folder is not chmoded. this will force you to delete the install file yourself and the other install files you\'ll use to install other items to the board.</p>'; 
	}else{
		echo '<p class="lowerwarn">install folder is detected as writable.</p>'; 
	}
	#check base folder.
	if(!is_writable("../config.php")){
		echo '<p class="raisewarn">The config file is not chmoded. You <u>MUST</u> chmod the file 777 or 755(ask your host for which one to use) in order to continue.</p>';
	}else{                                       
		echo '<p class="lowerwarn">The config file is detected as writable.</p>';
	}
	echo '<hr><p class="td2"><b>PHP Version Checker</b></p>';
	#see if user has a new enough version of php.
	if(phpversion() < "4.3.9"){
		echo '<p class="raisewarn">You do not have a current enough version of PHP to run Elite Bulletin Board, please ask your host to upgrade their version of PHP to at least 4.3.9</p>';
	}else{
		echo '<p class="lowerwarn">Your version of PHP is current enough to run Elite Bulletin Board.</p>';
	}
	echo '<hr><p class="td2"><b>Update Checker Status</b></p>';
	#see if user can check updates from the ACP.
	if ((@ini_get('allow_url_fopen') == 0) or (strtolower(@ini_get('allow_url_fopen')) == 'off')) {
		echo '<p class="raisewarn">You will NOT be able to check for update from the Administrative Panel</p>';	
	}else{
		echo '<p class="lowerwarn">You will be able to check for update from the Administrative Panel.</p>';
	}
echo '</div>';
}
//display footer
?>
</body>
</html>
