<?php
ob_start();
define('IN_EBB', true);
/*
Filename: upgrade.php
Last Modified: 6/7/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
if(isset($_GET['action'])){
	$action = $_GET['action'];
}else{
	$action = '';
}

#config data.
$template_path = "../template/clearblue2";
$versionKey = 'c2e7b5bb0ec8bb7e2aaf8a5516ca5387';

#see if config file is already writtened.
$config_path = '../config.php';
$file_size = filesize($config_path);
if($file_size == 0){
	echo '<p class="attachment">The config file is blank, please go to the <b><a href="index.php?cmd=create">connection wizard</a></b>.</p>';
	exit();
}
include "../config.php";
require "../includes/db.php";
$db = new db;
require "../template.php";
require "../lang/english.lang.php";
require "../includes/function.php";
require "../includes/template_function.php";
require "../includes/user_function.php";
require "../includes/admin_function.php";
#header.
$page = new template("../template/clearblue2/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "Elite Bulletin Board",
  "PAGETITLE" => "Version 2.1 Final Installer"));

$page->output();
#get top.
echo '<div class="td1"><a href="index.php"><img src="../template/clearblue2/images/logo.gif" alt="" /></a></div>
<h3 class="td2">Version 2.1 Final Installer</h3>';
switch ($action){
case 'upgrade':
	echo '<p class="td2">Updating Database...</p><br />';

	#alter ebb_posts
	$db->run = "ALTER TABLE `ebb_posts` CHANGE `re_author` `author` VARCHAR ( 25 ) NOT NULL DEFAULT ''";
	$db->query();
	$db->close();

	#alter ebb_users
	$db->run = "ALTER TABLE `ebb_users` ADD `rssfeed1` VARCHAR( 200 ) NULL ,
	ADD `rssfeed2` VARCHAR( 200 ) NOT NULL";
	$db->query();
	$db->close();
	$db->run = "ALTER TABLE `ebb_users` ADD `banfeeds` TINYINT( 1 ) NULL";
	$db->query();
	$db->close();

	#update user's profile.
	$rss1 = "http://rss.msnbc.msn.com/id/3032091/device/rss/rss.xml";
	$rss2 = "http://news.google.com/nwshp?hl=en&tab=wn&output=rss";
	
	$db->run = "select id from `ebb_users`";
	$user_q = $db->query();
	$db->close();
	while($r = mysql_fetch_assoc($user_q)){
		$db->run = "update `ebb_users` SET rssfeed1='$rss1', rssfeed2='$rss2', banfeeds='0', Style='1' WHERE id='$r[id]'";
		$db->query();
		$db->close();
	}
	#alter ebb_pm
	$db->run = "ALTER TABLE `ebb_pm` ADD `Folder` VARCHAR(7) NOT NULL AFTER `Reciever`";
	$db->query();
	$db->close();
	#alter ebb_settings
	$db->run = "ALTER TABLE `ebb_settings` ADD `Archive_Quota` TINYINT(255) NOT NULL AFTER `PM_Quota`";
	$db->query();
	$db->close();
	#update settings table.
	$db->run = "UPDATE ebb_settings SET Default_Style='3', Archive_Quota='20', version='$versionKey'";
	$db->query();
	$db->close();
	//update ebb_poll.
	$db->run="ALTER TABLE `ebb_poll` CHANGE `tid` `tid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'";
	$db->query();
	$db->close();
	#clear  all current styles on db.
	$db->run = "TRUNCATE TABLE `ebb_style`";
	$db->query();
	$db->close();
	//insert new style
	$db->run = "INSERT INTO `ebb_style` (id, Name, Temp_Path) VALUES (1, 'Simple2', 'template/simple2'),
	(2, 'Metallic Thunder', 'template/metallicthunder'),
	(3, 'Clear Blue2', 'template/clearblue2')";
	$db->query();
	$db->close();
	#finished.
	echo '<p class="td2">Database updating Complete. <b><a href="upgrade.php?action=finalize">Finish Upgrade</a></b>.</p>';
break;
case 'finalize':
	echo '<p class="td2">Finalize Upgrade</p><br />';

	//delete installer files.
	$delinstall = @unlink ('upgrade.php');
	$delinstall2 = @unlink ('index.php');
	@unlink('install.php');
	if (($delinstall) AND ($delinstall2)){
		echo "<p>Deleting install files...Success!</p>
		<p>Elite Bulletin Board is now set-up and ready to go.</p>";
	}else{
		echo "<p>Deleting install files...<b>Failed!</b> didn't CHMOD folder 777 or 755.</p>
		<p><b>Delete install.php & install.css <u>immediately</u> to prevent someone from overwriting this install!!!!</b><br /><br />
		Elite Bulletin Board is now set-up and ready to go.</p>";
	}
break;
default:
	echo '<p class="td2">Thank you for trying out Elite Bulletin Board v2.1. This will upgrade your version 2.0 board over to a version 2.1 setup.<br /><br />
	Before you begin, it is <b>strongly</b> recommended that you backup everything.</p><br />
	<p class="td2"><b>Permission Stats</b><br /><br />
	The installer has detected the permission values of the install folder and the results are listed below.</p>';	
	
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
	#config creation.
	echo '<hr><div align="center"><b><a href="upgrade.php?action=upgrade" class="td2">Begin Upgrade</a></b></div>';
}
//display footer
?>
</body>
</html>
<?php
ob_end_flush();
?>
