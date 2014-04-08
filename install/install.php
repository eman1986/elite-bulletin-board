<?php
ob_start();
define('IN_EBB', true);
/*
Filename: install.php
Last Modified: 7/16/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
if(isset($_GET['step'])){
	$step = $_GET['step'];
}else{
	$step = '';
}

#config data.
$template_path = "../template/clearblue2";
$versionKey = '498b71407ed107b5a3f83951be5b4df4';

#see if config file is already writtened.
$config_path = '../config.php';
$file_size = filesize($config_path);
if($file_size == 0){
	echo '<p class="td2">The config file is blank, please go to the <b><a href="index.php?cmd=create">connection wizard</a></b>.</p>';
	exit();
}
include "../config.php";
require "../includes/db.php";
$db = new db;
require "../template.php";
require "../lang/English.lang.php";
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
<h3 class="td2">Version 2.1.0 Final Installer</h3>';
  switch ($step)  {
  case 'install_1':
	echo '<table border="0" class="table" align="center" cellspacing="1" cellpadding="3">
<tr>
<td align="center" class="td1" width="16%">Welcome</td>
<td align="center" class="td1" width="17%">MySQL Connection Setup</td>
<td align="center" class="td2" width="17%">Create Tables</td>
<td align="center" class="td1" width="17%">Setup Settings</td>
<td align="center" class="td1" width="16%">Create User</td>
<td align="center" class="td1" width="17%">Create Category/Board</td>
</tr>
</table><hr />';
#create tables for this program.
echo "<p class=\"titlebar\">Adding tables to database:</p>";
//ebb_attachment_extlist
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_attachment_extlist` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `ext` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_attachments
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_attachments` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Username` varchar(25) NOT NULL default '',
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `pid` mediumint(8) unsigned NOT NULL default '0',
  `Filename` varchar(100) NOT NULL default '',
  `encryptedFileName` varchar(40) NOT NULL,
  `File_Type` varchar(100) NOT NULL default '',
  `File_Size` int(20) NOT NULL default '0',
  `Download_Count` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_banlist
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_banlist` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `ban_item` varchar(255) NOT NULL default '',
  `ban_type` varchar(5) NOT NULL default '',
  `match_type` varchar(8) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_blacklist
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_blacklist` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `blacklisted_username` varchar(25) NOT NULL default '',
  `match_type` varchar(8) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_board_access
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_board_access` (
  `B_Read` tinyint(1) NOT NULL default '0',
  `B_Post` tinyint(1) NOT NULL default '0',
  `B_Reply` tinyint(1) NOT NULL default '0',
  `B_Vote` tinyint(1) NOT NULL default '0',
  `B_Poll` tinyint(1) NOT NULL default '0',
  `B_Delete` tinyint(1) NOT NULL default '0',
  `B_Edit` tinyint(1) NOT NULL default '0',
  `B_Important` tinyint(1) NOT NULL default '0',
  `B_Attachment` tinyint(1) NOT NULL default '0',
  `B_id` mediumint(8) unsigned NOT NULL default '0'
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_board
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_boards` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Board` varchar(50) NOT NULL default '',
  `Description` tinytext NOT NULL,
  `last_update` varchar(14) default NULL,
  `Posted_User` varchar(25) default NULL,
  `Post_Link` varchar(100) default NULL,
  `type` tinyint(1) NOT NULL default '0',
  `Category` mediumint(8) unsigned NOT NULL default '0',
  `Smiles` tinyint(1) NOT NULL default '0',
  `BBcode` tinyint(1) NOT NULL default '0',
  `Post_Increment` tinyint(1) NOT NULL default '0',
  `Image` tinyint(1) NOT NULL default '0',
  `B_Order` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_censor
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_censor` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Original_Word` varchar(50) NOT NULL default '',
  `action` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_cplog
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_cplog` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `User` varchar(25) NOT NULL default '',
  `Action` text NOT NULL,
  `Date` varchar(14) NOT NULL default '',
  `IP` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_group_users
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_group_users` (
  `Username` varchar(25) NOT NULL default '',
  `gid` mediumint(8) UNSIGNED NOT NULL default '0',
  `Status` varchar(7) NOT NULL default ''
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_grouplist
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_grouplist` (
  `group_id` mediumint(8) UNSIGNED NOT NULL default '0',
  `board_id` mediumint(8) UNSIGNED NOT NULL default '0',
  `type` tinyint(1) NOT NULL default '0'
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_groups
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_groups` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Name` varchar(30) NOT NULL default '',
  `Description` tinytext NOT NULL,
  `Enrollment` tinyint(1) NOT NULL default '0',
  `Level` tinyint(1) NOT NULL default '0',
  `permission_type` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_permission_actions
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_permission_actions` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `permission` varchar(30) NOT NULL default '',
  `type` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_permission_data
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_permission_data` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `profile` mediumint(8) unsigned NOT NULL default '0',
  `permission` mediumint(8) unsigned NOT NULL default '0',
  `set_value` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_permission_profile
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_permission_profile` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `profile` varchar(30) NOT NULL default '',
  `access_level` tinyint(1) NOT NULL default '0',
  `system` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_online
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_online` (
  `Username` varchar(25) NOT NULL default '',
  `ip` varchar(40) NOT NULL default '',
  `time` varchar(40) NOT NULL default '',
  `location` varchar(90) NOT NULL default ''
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_pm
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_pm` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Subject` varchar(25) NOT NULL default '',
  `Sender` varchar(25) NOT NULL default '',
  `Reciever` varchar(25) NOT NULL default '',
  `Folder` varchar(7) NOT NULL,
  `Message` text NOT NULL,
  `Date` varchar(14) NOT NULL default '',
  `Read_Status` char(3) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_pm_banlist
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_pm_banlist` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Banned_User` varchar(25) NOT NULL default '',
  `Ban_Creator` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_poll
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_poll` (
  `Poll_Option` varchar(50) NOT NULL default '',
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `option_id` mediumint(8) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`option_id`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_posts
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_posts` (
  `author` varchar(25) NOT NULL default '',
  `pid` mediumint(8) unsigned NOT NULL auto_increment,
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `bid` mediumint(8) unsigned NOT NULL default '0',
  `Body` text NOT NULL,
  `IP` varchar(40) NOT NULL default '',
  `Original_Date` varchar(14) NOT NULL default '',
  `disable_bbcode` tinyint(1) NOT NULL default '0',
  `disable_smiles` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`pid`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_ranks
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_ranks` (
  `Name` varchar(50) NOT NULL default '',
  `Post_req` mediumint(8) unsigned NOT NULL default '0',
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Star_Image` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_read_board
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_read_board` (
  `Board` mediumint(8) unsigned NOT NULL default '0',
  `User` varchar(25) NOT NULL default ''
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_read_topic
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_read_topic` (
  `Topic` mediumint(8) unsigned NOT NULL default '0',
  `User` varchar(25) NOT NULL default ''
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_settings
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_settings` (
  `Site_Title` varchar(50) NOT NULL default '',
  `Site_Address` varchar(255) NOT NULL default '',
  `Board_Status` tinyint(1) NOT NULL default '0',
  `Board_Address` varchar(255) NOT NULL default '',
  `Board_Email` varchar(255) NOT NULL default '',
  `Off_Message` tinytext NOT NULL,
  `Announcement_Status` tinyint(1) NOT NULL default '0',
  `Announcements` tinytext NOT NULL,
  `Default_Style` mediumint(8) unsigned NOT NULL default '0',
  `Default_Language` varchar(50) NOT NULL default '',
  `TOS_Status` tinyint(1) NOT NULL default '0',
  `TOS_Rules` text NOT NULL,
  `GZIP` tinyint(1) NOT NULL default '0',
  `per_page` tinyint(1) NOT NULL default '0',
  `Image_Verify` tinyint(1) NOT NULL default '0',
  `spell_checker` tinyint(1) NOT NULL default '0',
  `PM_Quota` tinyint(255) NOT NULL default '0',
  `Archive_Quota` tinyint(255) NOT NULL,
  `activation` varchar(5) NOT NULL default '',
  `register_stat` tinyint(1) NOT NULL default '0',
  `userstat` mediumint(8) NOT NULL default '0',
  `coppa` tinyint(1) NOT NULL default '0',
  `Default_Zone` varchar(5) NOT NULL default '',
  `Default_Time` varchar(14) NOT NULL default '',
  `cookie_domain` varchar(255) NOT NULL default '',
  `cookie_path` varchar(255) NOT NULL default '',
  `cookie_secure` tinyint(1) NOT NULL default '0',
  `attachment_quota` varchar(9) NOT NULL default '',
  `download_attachments` tinyint(1) NOT NULL default '0',
  `mx_check` tinyint(1) NOT NULL default '0',
  `proxy_block` tinyint(1) NOT NULL default '0',
  `warning_threshold` tinyint(1) NOT NULL default '0',
  `mail_type` tinyint(1) NOT NULL default '0',
  `smtp_server` varchar(255) NOT NULL default '',
  `smtp_port` varchar(5) NOT NULL default '',
  `smtp_user` varchar(255) NOT NULL default '',
  `smtp_pass` varchar(255) NOT NULL default '',
  `version` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`Site_Title`)
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_smiles
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_smiles` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `code` varchar(30) NOT NULL default '',
  `img_name` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_style
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_style` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Name` varchar(50) NOT NULL default '',
  `Temp_Path` varchar(80) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_topic_watch
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_topic_watch` (
  `username` varchar(25) NOT NULL default '',
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `status` varchar(6) NOT NULL default ''
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_topics
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_topics` (
  `author` varchar(25) NOT NULL default '',
  `tid` mediumint(8) unsigned NOT NULL auto_increment,
  `bid` mediumint(8) unsigned NOT NULL default '0',
  `Topic` varchar(50) NOT NULL default '',
  `Body` text NOT NULL,
  `Type` varchar(5) NOT NULL default '',
  `important` tinyint(1) NOT NULL default '0',
  `IP` varchar(40) NOT NULL default '',
  `Original_Date` varchar(14) NOT NULL default '',
  `last_update` varchar(14) NOT NULL default '',
  `Posted_User` varchar(25) NOT NULL default '',
  `Post_Link` varchar(100) NOT NULL default '',
  `Locked` tinyint(1) NOT NULL default '0',
  `Views` mediumint(8) unsigned NOT NULL default '0',
  `Question` varchar(50) NOT NULL default '',
  `disable_bbcode` tinyint(1) NOT NULL default '0',
  `disable_smiles` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`tid`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_users
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_users` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Username` varchar(25) NOT NULL default '',
  `Password` varchar(32) NOT NULL default '',
  `Email` varchar(255) NOT NULL default '',
  `Status` varchar(11) NOT NULL default '',
  `Custom_Title` varchar(20) NOT NULL default '',
  `last_visit` varchar(14) NOT NULL default '',
  `PM_Notify` tinyint(1) NOT NULL default '0',
  `Hide_Email` tinyint(1) NOT NULL default '0',
  `MSN` varchar(255) NOT NULL default '',
  `AOL` varchar(255) NOT NULL default '',
  `Yahoo` varchar(255) NOT NULL default '',
  `ICQ` varchar(15) NOT NULL default '',
  `WWW` varchar(200) NOT NULL default '',
  `Location` varchar(70) NOT NULL default '',
  `Avatar` varchar(255) NOT NULL default '',
  `Sig` tinytext NULL,
  `Time_format` varchar(14) NOT NULL default '',
  `Time_Zone` varchar(5) NOT NULL default '',
  `Date_Joined` varchar(50) NOT NULL default '',
  `IP` varchar(40) NOT NULL default '',
  `Style` mediumint(8) unsigned NOT NULL default '0',
  `Language` varchar(50) NOT NULL default '',
  `Post_Count` mediumint(8) unsigned NOT NULL default '0',
  `last_post` varchar(14) NOT NULL default '',
  `last_search` varchar(14) NOT NULL default '',
  `failed_attempts` tinyint(1) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '0',
  `act_key` varchar(32) NOT NULL default '',
  `warning_level` tinyint(1) NOT NULL default '0',
  `suspend_length` tinyint(1) NOT NULL default '0',
  `suspend_time` varchar(14) NOT NULL default '',
  `rssfeed1` varchar(200) NULL,
  `rssfeed2` varchar(200) NULL,
  `banfeeds` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=1";
$db->query();
$db->close();
//ebb_votes
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_votes` (
  `Username` varchar(25) NOT NULL default '',
  `tid` mediumint(8) unsigned NOT NULL default '0',
  `Vote` varchar(255) NOT NULL default ''
) ENGINE=MyISAM";
$db->query();
$db->close();
//ebb_warnlog
$db->run = "CREATE TABLE IF NOT EXISTS `ebb_warnlog` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `Username` varchar(25) NOT NULL default '',
  `Authorized` varchar(25) NOT NULL default '',
  `Action` tinyint(1) NOT NULL default '0',
  `Message` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$db->query();
$db->close();
//add in default data.
echo "<p>Tables Added.<br />Insert default data to needed tables:</p>";
//insert extension whitelist
$db->run = "INSERT INTO `ebb_attachment_extlist` (`id`, `ext`) VALUES 
(1, 'gif'),
(2, 'png'),
(3, 'jpg'),
(4, 'jpeg'),
(5, 'tif'),
(6, 'tiff'),
(7, 'tga'),
(8, 'gtar'),
(9, 'zip'),
(10, 'gz'),
(11, 'tar'),
(12, 'rar'),
(13, 'ace'),
(14, 'tgz'),
(15, 'bz2'),
(16, '7z'),
(17, 'txt'),
(18, 'rtf'),
(19, 'doc'),
(20, 'csv'),
(21, 'xls'),
(22, 'cvs'),
(23, 'xlsx'),
(24, 'xlsm'),
(25, 'xlsb'),
(26, 'docx'),
(27, 'docm'),
(28, 'dot'),
(29, 'dotx'),
(30, 'dotm'),
(31, 'pdf'),
(32, 'ai'),
(33, 'psp'),
(34, 'sql'),
(35, 'psd'),
(36, 'ppt'),
(37, 'pptx'),
(38, 'pptm'),
(39, 'odg'),
(40, 'odt'),
(41, 'odp'),
(42, 'rm'),
(43, 'ram'),
(44, 'wma'),
(45, 'wmv'),
(46, 'swf'),
(47, 'mov'),
(48, 'mp3'),
(49, 'mp4'),
(50, 'qt'),
(51, 'mpg'),
(52, 'mpeg'),
(53, 'avi'),
(54, 'asf'),
(55, 'chm')";
$db->query();
$db->close();
//insert profile actions. 
$db->run = "INSERT INTO `ebb_permission_actions` (`id`, `permission`, `type`) VALUES
(1, 'MANAGE_BOARDS', 1),
(2, 'PRUNE_BOARDS', 1),
(3, 'MANAGE_GROUPS', 1),
(4, 'MASS_EMAIL', 1),
(5, 'WORD_CENSOR', 1),
(6, 'MANAGE_SMILES', 1),
(7, 'MODIFY_SETTINGS', 1),
(8, 'MANAGE_STYLES', 1),
(9, 'VIEW_PHPINFO', 1),
(10, 'CHECK_UPDATES', 1),
(11, 'SEE_ACP_LOG', 1),
(12, 'CLEAR_ACP_LOG', 1),
(13, 'MANAGE_BANLIST', 1),
(14, 'MANAGE_USERS', 1),
(15, 'PRUNE_USERS', 1),
(16, 'MANAGE_BLACKLIST', 1),
(17, 'MANAGE_RANKS', 1),
(18, 'MANAGE_WARNLOG', 1),
(19, 'ACTIVATE_USERS', 1),
(20, 'EDIT_TOPICS', 2),
(21, 'DELETE_TOPICS', 2),
(22, 'LOCK_TOPICS', 2),
(23, 'MOVE_TOPICS', 2),
(24, 'VIEW_IPS', 2),
(25, 'WARN_USERS', 2),
(26, 'ATTACH_FILES', 3),
(27, 'PM_ACCESS', 3),
(28, 'SEARCH_BOARD', 3),
(29, 'DOWNLOAD_FILES', 3),
(30, 'CUSTOM_TITLES', 3),
(31, 'VIEW_PROFILE', 3),
(32, 'USE_AVATARS', 3),
(33, 'USE_SIGNATURES', 3),
(34, 'JOIN_GROUPS', 3),
(35, 'CREATE_POLL', 3),
(36, 'VOTE_POLL', 3),
(37, 'NEW_TOPIC', 3),
(38, 'REPLY', 3),
(39, 'IMPORTANT_TOPIC', 3)";
$db->query();
$db->close();
//insert default profile templates.
$db->run = "INSERT INTO `ebb_permission_profile` (`id`, `profile`, `access_level`, `system`) VALUES
(1, 'Full Administrator', 1, 1),
(2, 'Limited Administrator', 1, 1),
(3, 'Moderator', 2, 1),
(4, 'User', 3, 1),
(5, 'Limited User', 3, 1)";
$db->query();
$db->close();
//insert default profile permissions into db.
$db->run = "INSERT INTO `ebb_permission_data` (`id`, `profile`, `permission`, `set_value`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 1),
(6, 1, 6, 1),
(7, 1, 7, 1),
(8, 1, 8, 1),
(9, 1, 9, 1),
(10, 1, 10, 1),
(11, 1, 11, 1),
(12, 1, 12, 1),
(13, 1, 13, 1),
(14, 1, 14, 1),
(15, 1, 15, 1),
(16, 1, 16, 1),
(17, 1, 17, 1),
(18, 1, 18, 1),
(19, 1, 19, 1),
(20, 1, 20, 1),
(21, 1, 21, 1),
(22, 1, 22, 1),
(23, 1, 23, 1),
(24, 1, 24, 1),
(25, 1, 25, 1),
(26, 1, 26, 1),
(27, 1, 27, 1),
(28, 1, 28, 1),
(29, 1, 29, 1),
(30, 1, 30, 1),
(31, 1, 31, 1),
(32, 1, 32, 1),
(33, 1, 33, 1),
(34, 1, 34, 1),
(35, 1, 35, 1),
(36, 1, 36, 1),
(37, 1, 37, 1),
(38, 1, 38, 1),
(39, 1, 39, 1),
(40, 2, 1, 1),
(41, 2, 2, 1),
(42, 2, 3, 1),
(43, 2, 4, 0),
(44, 2, 5, 1),
(45, 2, 6, 0),
(46, 2, 7, 0),
(47, 2, 8, 0),
(48, 2, 9, 0),
(49, 2, 10, 0),
(50, 2, 11, 0),
(51, 2, 12, 0),
(52, 2, 13, 1),
(53, 2, 14, 0),
(54, 2, 15, 0),
(55, 2, 16, 1),
(56, 2, 17, 1),
(57, 2, 18, 0),
(58, 2, 19, 0),
(59, 2, 20, 1),
(60, 2, 21, 1),
(61, 2, 22, 1),
(62, 2, 23, 1),
(63, 2, 24, 1),
(64, 2, 25, 1),
(65, 2, 26, 1),
(66, 2, 27, 1),
(67, 2, 28, 1),
(68, 2, 29, 1),
(69, 2, 30, 1),
(70, 2, 31, 1),
(71, 2, 32, 1),
(72, 2, 33, 1),
(73, 2, 34, 1),
(74, 2, 35, 1),
(75, 2, 36, 1),
(76, 2, 37, 1),
(77, 2, 38, 1),
(78, 2, 39, 1),
(79, 3, 20, 1),
(80, 3, 21, 1),
(81, 3, 22, 1),
(82, 3, 23, 1),
(83, 3, 24, 1),
(84, 3, 25, 1),
(85, 4, 26, 1),
(86, 4, 27, 1),
(87, 4, 28, 1),
(88, 4, 29, 1),
(89, 4, 30, 1),
(90, 4, 31, 1),
(91, 4, 32, 1),
(92, 4, 33, 1),
(93, 4, 34, 1),
(94, 4, 35, 1),
(95, 4, 36, 1),
(96, 4, 37, 1),
(97, 4, 38, 1),
(98, 4, 39, 1),
(99, 5, 26, 0),
(100, 5, 27, 1),
(101, 5, 28, 1),
(102, 5, 29, 1),
(103, 5, 30, 0),
(104, 5, 31, 0),
(105, 5, 32, 1),
(106, 5, 33, 1),
(107, 5, 34, 0),
(108, 5, 35, 1),
(109, 5, 36, 1),
(110, 5, 37, 1),
(111, 5, 38, 1),
(112, 5, 39, 0)";
$db->query();
$db->close();

//insert ranks
$db->run = "INSERT INTO `ebb_ranks` (Name, Post_req, id, Star_Image) VALUES ('Noobie', '0', 1, 'newstar.gif'),
('Jr. Member', '10', 2, 'juniorstar.gif'),
('Member', '40', 3, 'memstar.gif'),
('Full Member', '70', 4, 'fullstar.gif'),
('Sr. Member', '140', 5, 'seniorstar.gif'),
('Lord of the Board', '500', 6, 'lordstar.gif')";
$db->query();
$db->close();

//insert smiles
$db->run = "INSERT INTO `ebb_smiles` (id, code, img_name) VALUES (1, ':S', 'confused.gif'),
(2, '8)', 'cool.gif'),
(3, ':D', 'grin.gif'),
(4, ':@', 'mad.gif'),
(5, ':|', 'ok.gif'),
(6, ':oops:', 'oops.gif'),
(7, ':(', 'sad.gif'),
(8, '8O', 'shocked.gif'),
(9, ':)', 'smile.gif'),
(10, ':P', 'tongue.gif'),
(11, ';)', 'wink.gif'),
(12, ':cry:', 'cry.gif')";
$db->query();
$db->close();

//insert styles
$db->run = "INSERT INTO `ebb_style` (id, Name, Temp_Path) VALUES (1, 'Simple2', 'template/simple2'),
(2, 'Metallic Thunder', 'template/metallicthunder'),
(3, 'Clear Blue2', 'template/clearblue2')";
$db->query();
$db->close();

//finished.
echo '<p class="td2">Database setup complete. <b><a href="install.php?step=install_2">step 2</a></b></p>';
  break;
  case 'install_2':
$timezone = timezone_select(0);
$style_select = style_select(1);
$lang = @acp_lang_select("English");
echo '<table border="0" class="table" align="center" cellspacing="1" cellpadding="3">
<tr>
<td align="center" class="td1" width="16%">Welcome</td>
<td align="center" class="td1" width="17%">MySQL Connection Setup</td>
<td align="center" class="td1" width="17%">Create Tables</td>
<td align="center" class="td2" width="17%">Setup Settings</td>
<td align="center" class="td1" width="16%">Create User</td>
<td align="center" class="td1" width="17%">Create Category/Board</td>
</tr>
</table><hr />';
echo '<h3 class="td2">This is where you set the board\'s settings to make it function correctly. The list below is only the main settings, you may alter the board more once your done with the setup.</h3>';

echo "<form method=\"post\" action=\"install.php?step=install_2b\">
<div class=\"td1\">Board Settings</div>
<div class=\"td2\">
Board Name<br /><input type=\"text\" name=\"board_name\" size=\"30\" class=\"text\">
<hr />
Site Address<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('example: http://mysite.com', this, event, '150px')\">[?]</a><br />
<input type=\"text\" name=\"site_address\" size=\"30\" class=\"text\">
<hr />
Board Address<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('example: http://mysite.com/board', this, event, '150px')\">[?]</a><br />
<input type=\"text\" name=\"board_address\" size=\"30\" class=\"text\">
<hr />
Board Email<br />
<input type=\"text\" name=\"board_email\" size=\"30\" class=\"text\">
<hr />
Announcement Status<br />
<input type=\"radio\" name=\"announce_stat\" value=\"1\">On <input type=\"radio\" name=\"announce_stat\" value=\"0\">Off
<hr />
Announcement Message<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('One announcement per line.', this, event, '150px')\">[?]</a><br />
<textarea name=\"announce_msg\" rows=\"3\" cols=\"50\" class=\"text\"></textarea>
<hr />
Default Style<br />$style_select
<hr />
Default Language<br />$lang
<hr />
Term Status<br />
<input type=\"radio\" name=\"term_stat\" value=\"1\">On <input type=\"radio\" name=\"term_stat\" value=\"0\">Off
<hr />
Terms<br />
<textarea name=\"term_msg\" rows=\"3\" cols=\"50\" class=\"text\"></textarea>
<hr />
GZIP Compression<br />
<input type=\"radio\" name=\"gzip_stat\" value=\"1\">On <input type=\"radio\" name=\"gzip_stat\" value=\"0\">Off
<hr />
CAPTCHA Image<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('GD enabled for low & freetype enabled for high.', this, event, '150px')\">[?]</a><br />
<input type=\"radio\" name=\"imagevert_stat\" value=\"1\">Low setting <input type=\"radio\" name=\"imagevert_stat\" value=\"2\">High Setting <input type=\"radio\" name=\"imagevert_stat\" value=\"0\">Off
<hr />
Spell Checker<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('require PSPELL to be enabled.', this, event, '150px')\">[?]</a><br />
<input type=\"radio\" name=\"spell_stat\" value=\"1\">On <input type=\"radio\" name=\"spell_stat\" value=\"0\">Off
<hr />
PM Quota<br />
<input type=\"text\" name=\"pm_quota\" size=\"5\" maxlength=\"4\" value=\"50\" class=\"text\">
<hr />
Registration Status<br />
<input type=\"radio\" name=\"reg_stat\" value=\"1\" />On <input type=\"radio\" name=\"reg_stat\" value=\"0\" />Off
<hr />
Disallow users on a proxy connection from registering<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('Enabling this will reduce spamming attempts by detecting real IPs.', this, event, '150px')\">[?]</a><br />
<input type=\"radio\" name=\"proxy_stat\" value=\"1\" />Yes <input type=\"radio\" name=\"proxy_stat\" value=\"0\" />No
<hr />
Activation Type<br />
<input type=\"radio\" name=\"active_stat\" value=\"None\">None <input type=\"radio\" name=\"active_stat\" value=\"User\">User <input type=\"radio\" name=\"active_stat\" value=\"Admin\">Admin
<hr />
Warning Threshold<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('Set the number that will determine an automatic ban from the board.', this, event, '150px')\">[?]</a><br />
<select name=\"warnthreshold\" class=\"text\">
<option value=\"30\">30</option>
<option value=\"40\">40</option>
<option value=\"50\" selected=selected>50</option>
<option value=\"60\">60</option>
<option value=\"70\">70</option>
<option value=\"80\">80</option>
<option value=\"90\">90</option>
<option value=\"100\">100</option>
</select>
<hr />
Maximum Size user can upload.<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('Enter this amount in bytes.', this, event, '150px')\">[?]</a><br />
<input type=\"text\" name=\"attach_quota\" size=\"30\" value=\"870400\" class=\"text\" />
<hr />
Can Guest Download the attachments?<br />
<input type=\"radio\" name=\"download_stat\" value=\"1\" />Yes <input type=\"radio\" name=\"download_stat\" value=\"0\" />No
<hr />
Default Time Zone<br />$timezone
<hr />
Default Time format<a href=\"http://us2.php.net/manual/en/function.date.php\" target=\"_blank\" class=\"hintanchor\" onMouseover=\"showhint('select your time format by using the date() syntax from php.', this, event, '150px')\">[?]</a><br />
<input type=\"text\" name=\"default_time\" size=\"30\" value=\"M d Y g:i a\" class=\"text\">
<br /><br />
<div align=\"center\" class=\"td1\">Cookie Options</div>
<br />
Cookie Domain<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('example: mysite.com or board.mysite.com', this, event, '150px')\">[?]</a><br />
<input type=\"text\" name=\"cookie_domain\" size=\"30\" class=\"text\">
<hr />
Cookie Path<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('example: /board/', this, event, '150px')\">[?]</a><br />
<input type=\"text\" name=\"cookie_path\" size=\"30\" class=\"text\">
<hr />
Cookie Secure<a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('Must have SSL on your server to use this.', this, event, '150px')\">[?]</a><br />
<input type=\"radio\" name=\"secure_stat\" value=\"1\">Enable <input type=\"radio\" name=\"secure_stat\" value=\"0\">Disable
<br /><br />
<div align=\"center\" class=\"td1\">Mail Settings</div>
<br />
Mail Type<br />
<input type=\"radio\" name=\"mail_type\" value=\"0\">SMTP <input type=\"radio\" name=\"mail_type\" value=\"1\">Mail() method
<hr />
SMTP Host<br />
<input type=\"text\" name=\"smtp_host\" size=\"30\" class=\"text\">
<hr />
SMTP Port<br />
<input type=\"text\" name=\"smtp_port\" size=\"5\" value=\"25\" class=\"text\" maxlength=\"4\">
<hr />
SMTP Username<br />
<input type=\"text\" name=\"smtp_user\" size=\"30\" class=\"text\">
<hr />
SMTP Password<br />
<input type=\"text\" name=\"smtp_pass\" size=\"30\" class=\"text\">
<hr />
<div align=\"center\"><input type=\"submit\" value=\"Save Settings\" class=\"submit\"></div>
</div>
</form><br />";
  break;
  case 'install_2b':
	//define form results.
	$board_name = var_cleanup($_POST['board_name']);
	$site_address = var_cleanup($_POST['site_address']);
	$board_address = var_cleanup($_POST['board_address']);
	$board_email = var_cleanup($_POST['board_email']);
	$emailvalidate = valid_email($board_email);
	$announce_stat = var_cleanup($_POST['announce_stat']);
	$announce_msg = var_cleanup($_POST['announce_msg']);
	$dstyle = var_cleanup($_POST['style']);
	$default_lang = var_cleanup($_POST['default_lang']);
	$term_stat = var_cleanup($_POST['term_stat']);
	$term_msg = var_cleanup($_POST['term_msg']);
	$gzip_stat = var_cleanup($_POST['gzip_stat']);
	$imagevert_stat = var_cleanup($_POST['imagevert_stat']);
	$spell_stat = var_cleanup($_POST['spell_stat']);
	$pm_quota = var_cleanup($_POST['pm_quota']);
	$active_stat = var_cleanup($_POST['active_stat']);
	$warnthreshold = var_cleanup($_POST['warnthreshold']);
	$attach_quota = var_cleanup($_POST['attach_quota']);
	$download_stat = var_cleanup($_POST['download_stat']);
	$reg_stat = var_cleanup($_POST['reg_stat']);
	$proxy_stat = var_cleanup($_POST['proxy_stat']);
	$default_zone = var_cleanup($_POST['time_zone']);
	$default_time = var_cleanup($_POST['default_time']);
	$cookie_domain = var_cleanup($_POST['cookie_domain']);
	$cookie_path = var_cleanup($_POST['cookie_path']);
	$secure_stat = var_cleanup($_POST['secure_stat']);
	$mail_type = var_cleanup($_POST['mail_type']);
	$smtp_host = var_cleanup($_POST['smtp_host']);
	$smtp_port = var_cleanup($_POST['smtp_port']);
	$smtp_user = var_cleanup($_POST['smtp_user']);
	$smtp_pass = var_cleanup($_POST['smtp_pass']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//error checking.
	if (empty($board_name)){
		$errormsg = "You didn't set a name for the board.\n\n";
		$error = 1;
	}
	if (empty($site_address)){
		$errormsg .= "You didn't set a address to your website.\n\n";
		$error = 1;
	}
	if (empty($board_address)){
		$errormsg .= "You didn't set the link for the board address.\n\n";
		$error = 1;
	}
	if (empty($board_email)){
		$errormsg .= "You didn't set an email address for the board.\n\n";
		$error = 1;
	}
	if($emailvalidate == 1){
		$errormsg .= "You entered an invalid email address.\n\n";
		$error = 1;
	}
	if (($announce_stat == 1) AND (empty($announce_msg))){
		$errormsg .= "You didn't place any announcements in the messagebox.\n\n";
		$error = 1;
	}
	if (empty($dstyle)){
		$errormsg .= "You didn't select a default style.\n\n";
		$error = 1;
	}
	if (empty($default_lang)){
		$errormsg .= "You didn't set a default language.\n\n";
		$error = 1;
	}
	if (($term_stat == 1) AND (empty($term_msg))){
		$errormsg .= "You didn't set any terms in the messagebox.\n\n";
		$error = 1;
	}
	if ($gzip_stat == ""){
		$errormsg .= "You didn't set the GZIP value.\n\n";
		$error = 1;
	}
	if ($imagevert_stat == ""){
		$errormsg .= "You didn't set the image verify value.\n\n";
		$error = 1;
	}
	if ($spell_stat == ""){
		$errormsg .= "You didn't set the spell check value.\n\n";
		$error = 1;
	}
	if ((empty($pm_quota)) OR (!is_numeric($pm_quota))){
		$errormsg .= "You didn't set a quota for the PM Inbox.\n\n";
		$error = 1;
	}
	if($active_stat == ""){
		$errormsg .= "You didn't set an activation type for the registration page.\n\n";
		$error = 1;
	}
	if($warnthreshold == ""){
		$errormsg .= "You did not set the warning threshold.\n\n";
		$error = 1;
	}
	if(($warnthreshold < 30) or ($warnthreshold > 100)){
		$errormsg .= "You set an invalid value for the warning threshold.\n\n";
		$error = 1;
	}
	if($attach_quota > 102400000){
		$errormsg .= "Your quota limit is too big, set it to a smaller size.\n\n";
		$error = 1;
	}
	if(!is_numeric($attach_quota)){
		$errormsg .= "You entered an invalid quota limit.\n\n";
		$error = 1;
	}
	if($attach_quota == ""){
		$errormsg .= "You didn't enter a quota limit.\n\n";
		$error = 1;
	}
	if($download_stat == ""){
		$errormsg .= "You didn't set a rule on downloading the attachments.\n\n";
		$error = 1;
	}
	if($proxy_stat == ""){
		$errormsg .= "You did not the status of banning users on a proxy connection.\n\n";
		$error = 1;
	}
	if($reg_stat == ""){
		$errormsg .= "You did not set the status of registration.\n\n";
		$error = 1;
	}
	if ($default_zone == ""){
		$errormsg .= "You didn't set a default timezone.\n\n";
		$error = 1;
	}
	if (empty($default_time)){
		$errormsg .= "You didn't set a default time format.\n\n";
		$error = 1;
	}
	if(empty($cookie_domain)){
		$errormsg .= "You didn't set a domain for the cookie.\n\n";
		$error = 1;
	}
	if (empty($cookie_path)){
		$errormsg .= "You didn't set a path for the cookie.\n\n";
		$error = 1;
	}
	if ($secure_stat == ""){
		$errormsg .= "You didn't set the secure value for the cookie.\n\n";
		$error = 1;
	}
	if(strlen($board_name) > 50){
		$errormsg .= "The Board name you wish to use is too long, please use a smaller name.\n\n";
		$error = 1;
	}
	if(strlen($site_address) > 50){
		$errormsg .= "The site address you entered is too long, please enter in a smaller link.\n\n";
		$error = 1;
	}
	if (!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $site_address)) {
		$errormsg .= "You entered an invalid address.\n\n";
		$error = 1;
	}
	if(strlen($board_address) > 50){
		$errormsg .= "The path to the board is too long, try to shorten it if possible.\n\n";
		$error = 1;
	}
	if (!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $board_address)) {
		$errormsg .= "You entered an invalid board address.\n\n";
		$error = 1;
	}
	if(strlen($board_email) > 255){
		$errormsg .= "The email you entered is too long, please enter in a shorter one.\n\n";
		$error = 1;
	}
	if(strlen($announce_msg) > 255){
		$errormsg .= "Your announcement is too long, please shorten it.\n\n";
		$error = 1;
	}
	if(strlen($pm_quota) > 4){
		$errormsg .= "Your PM Quota is set too high. Please set it to a lower number.\n\n";
		$error = 1;
	}
	if (preg_match('/http[s]:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $cookie_domain) || preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $cookie_domain)) {
		$errormsg .= "Invalid cookie domain. no http:// or https://\n\n";
		$error = 1;
	}
	if(strlen($cookie_domain) > 50){
		$errormsg .= "Your Domain for the cookie is too long, enter in a shorter domain. Remember: no http:// or anything else just the domain name.\n\n";
		$error = 1;
	}
	if(strlen($cookie_path) > 50){
		$errormsg .= "Your cookie path is too long. enter in a shorter one. Remember: /board/ will work just fine.\n\n";
		$error = 1;
	}
	if($mail_type == ""){
		$errormsg .= "You did not select a mail method.\n\n";
		$error = 1;
	}
	#check for smtp values.
	if($mail_type == 0){
		if(empty($smtp_host)){
			$errormsg .= "You did not enter the address to the smtp server.\n\n";
			$error = 1;
		}
		if(empty($smtp_port)){
			$errormsg .= "No port was entered for the smtp server.\n\n";
			$error = 1;
		}
		if(empty($smtp_user)){
			$errormsg .= "No username was entered for the smtp server.\n\n";
			$error = 1;
		}
		if(empty($smtp_pass)){
			$errormsg .= "no password was entered for the smtp server.\n\n";
			$error = 1;
		}
		if(strlen($smtp_host) > 255){
			$errormsg .= "smtp server address is too long.\n\n";
			$error = 1;
		}
		if(strlen($smtp_port) > 5){
			$errormsg .= "smtp port number is too high.\n\n";
			$error = 1;
		}
		if(strlen($smtp_user) > 255){
			$errormsg .= "smtp username is too long.\n\n";
			$error = 1;
		}
		if(strlen($smtp_pass) > 255){
			$errormsg .= "smtp password is too long.\n\n";
			$error = 1;
		}
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		//process query.
		$db->run = "INSERT INTO ebb_settings (Site_Title, Site_Address, Board_Address, Board_Email, Board_Status, Off_Message, Announcement_Status, Announcements, Default_Style, Default_Language, TOS_Status, TOS_Rules, GZIP, Image_Verify, spell_checker, PM_Quota, Archive_Quota, activation, warning_threshold, per_page, attachment_quota, download_attachments, register_stat, Default_Zone, Default_Time, cookie_domain, cookie_path, cookie_secure, proxy_block, mx_check, userstat, coppa, mail_type, smtp_server, smtp_port, smtp_user, smtp_pass, version) VALUES('$board_name', '$site_address', '$board_address', '$board_email', '1', 'NULL', '$announce_stat', '$announce_msg', '$dstyle', '$default_lang', '$term_stat', '$term_msg', '$gzip_stat', '$imagevert_stat', '$spell_stat', '$pm_quota', '20', '$active_stat', '$warnthreshold', '15', '$attach_quota', '$download_stat', '$reg_stat', '$default_zone', '$default_time', '$cookie_domain', '$cookie_path', '$secure_stat', '$proxy_stat', '0', '0', '0', '$mail_type', '$smtp_host', '$smtp_port', '$smtp_user', '$smtp_pass', '$versionKey')";
		$db->query();
		$db->close();
		echo '<p class="td2">Settings were saved successfully! <b><a href="install.php?step=install_3">Step 3</a></b>.</p>';
	}
  break;
  case 'install_3':
echo '<table border="0" class="table" align="center" cellspacing="1" cellpadding="3">
<tr>
<td align="center" class="td1" width="16%">Welcome</td>
<td align="center" class="td1" width="17%">MySQL Connection Setup</td>
<td align="center" class="td1" width="17%">Create Tables</td>
<td align="center" class="td1" width="17%">Setup Settings</td>
<td align="center" class="td2" width="16%">Create User</td>
<td align="center" class="td1" width="17%">Create Category/Board</td>
</tr>
</table><br />';
$timezone = timezone_select(0);
$style_select = style_select(1);
$lang = @acp_lang_select('English');
echo "<form action=\"install.php?step=install_3b\" method=\"post\">
<div class=\"td1\">Create Administrator</div>
<div class=\"td2\">Email<br />
<input name=\"email\" type=\"text\" size=\"30\" maxlength=\"225\" class=\"text\" />
<hr />
Username <a href=\"#\" class=\"hintanchor\" onMouseover=\"showhint('Must contain only letters and numbers', this, event, '150px')\">[?]</a><br />
<input name=\"username\" type=\"text\" size=\"30\" maxlength=\"25\" class=\"text\" />
<hr />
Password<br />
<input name=\"password\" type=\"password\" size=\"30\" id=\"pass\" class=\"text\" />
<hr />
Confirm Password<br />
<input name=\"vert_password\" type=\"password\" size=\"30\" maxlength=\"25\" class=\"text\" />
<hr />
Timezone<br />$timezone
<hr />
Time Format<a href=\"http://us2.php.net/manual/en/function.date.php\" target=\"_blank\" class=\"hintanchor\" onMouseover=\"showhint('select your time format by using the date() syntax from php.', this, event, '150px')\">[?]</a><br />
<input type=\"text\" name=\"time_format\" value=\"M d Y g:i a\" size=\"30\" class=\"text\">
<hr />
Notify on new Private Messages<br />
<input type=\"radio\" name=\"pm_notice\" value=\"1\" />Yes <input type=\"radio\" name=\"pm_notice\" value=\"0\"/>No
<hr />
Display Email to others<br />
<input type=\"radio\" name=\"show_email\" value=\"0\" />Yes <input type=\"radio\" name=\"show_email\" value=\"1\" />No
<hr />
Style<br />$style_select
<hr />
Language<br />$lang
<hr />
<div align=\"center\"><input type=\"submit\" value=\"Create Administrator\" class=\"submit\" /></div>
</div>
</form><br />";
  break;
  case 'install_3b':
	//get values from form.
	$email = var_cleanup($_POST['email']);
	$username = var_cleanup($_POST['username']);
	$password = var_cleanup($_POST['password']);
	$vert_password = var_cleanup($_POST['vert_password']);
	$time_zone = var_cleanup($_POST['time_zone']);
	$time_format = var_cleanup($_POST['time_format']);
	$pm_notice = var_cleanup($_POST['pm_notice']);
	$show_email = var_cleanup($_POST['show_email']);
	$style = var_cleanup($_POST['style']);
	$default_lang = var_cleanup($_POST['default_lang']);
	$emailvalidate = valid_email($email);
	$rss1 = "http://rss.msnbc.msn.com/id/3032091/device/rss/rss.xml";
	$rss2 = "http://news.google.com/nwshp?hl=en&tab=wn&output=rss";
	$IP = $_SERVER['REMOTE_ADDR'];
	#set error values to default.
	$error = 0;
	$errormsg = '';
	//do some error checking
	if ($style == ""){
		$errormsg = "You didn't select a style.\n\n";
		$error = 1;
	}
	if ($default_lang == ""){
		$errormsg .= "You didn't select a language.\n\n";
		$error = 1;
	}
	if ($time_zone == ""){
		$errormsg .= "You didn't select a timezone.\n\n";
		$error = 1;
	}
	if ($time_format == ""){
		$errormsg .= "You didn't set a format for the time.\n\n";
		$error = 1;
	}
	if ($pm_notice == ""){
		$errormsg .= "You didn't set the PM notify value.\n\n";
		$error = 1;
	}
	if($show_email == ""){
		$errormsg .= "You didn't tell us if you wish to hide your email address.\n\n";
		$error = 1;
	}
	if ($username == ""){
		$errormsg .= "You didn't set a username.\n\n";
		$error = 1;
	}
	if ($email == ""){
		$errormsg .= "You didn't set a email.\n\n";
		$error = 1;
	}
	if($emailvalidate == 1){
		$errormsg .= "You entered an invalid email address.\n\n";
		$error = 1;
	}
	if ($password == ""){
		$errormsg .= "You didn't set a password\n\n";
		$error = 1;
	}
	if ($vert_password == ""){
		$errormsg .= "You didn't confirm the password.\n\n";
		$error = 1;
	}
	if (preg_match('[^A-Za-z0-9]', $username)){
		$errormsg .= "You entered a invalid character in the username.\n\n";
		$error = 1;
	}
	if ($vert_password !== $password){
		$errormsg .= "the passwords don't match.\n\n";
		$error = 1;
	}
	if(strlen($username) > 25){
		$errormsg .= "Your username is too long.\n\n";
		$error = 1;
	}
	if(strlen($username) < 4){
		$errormsg .= "Your Username is too short, you must have at least 5 characters.\n\n";
		$error = 1;
	}
	if(strlen($email) > 255){
		$errormsg .= "Your email address is too long.\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo error($error, "validate");
	}else{
		//create grouplist.
		$db->run = "INSERT INTO `ebb_groups` (id, Name, Description, Enrollment, Level, permission_type) values(1, 'Administrator', 'These are the people who are in charge. They have full power over the board.', '0', '1', '1')";
		$db->query();
		$db->close();
		$db->run = "INSERT INTO `ebb_groups` (id, Name, Description, Enrollment, Level, permission_type) VALUES(2, 'Moderator', 'These are the people who help the administrators manage the board. They have minor power over the board.', '1', '2', '3')";
		$db->query();
		$db->close();
		//process query.
		$time = time();
		$pass = md5($password.PWDSALT);
		$db->run = "INSERT INTO ebb_users (Email,Username, Password, Date_Joined, Status, IP, Time_format, Time_Zone, PM_Notify, Hide_Email, Style, Language, active, rssfeed1, rssfeed2, banfeeds) VALUES('$email', '$username', '$pass', '$time', 'groupmember', '$IP', '$time_format', '$time_zone', '$pm_notice', '$show_email', '$style', '$default_lang', '1', '$rss1', '$rss2', '0')";
		$db->query();
		$db->close();
		$db->run = "INSERT INTO ebb_group_users (Username, gid, Status) values('$username', '1', 'Active')";
		$db->query();
		$db->close();
		echo '<p class="td2">You have successfully registered an account. <b><a href="install.php?step=install_4">step 4</a></b>.</p>';
	}
  break;
  case 'install_4':
#get board type value.
if(isset($_GET['type'])){
	$type = var_cleanup($_GET['type']);
}else{
	$type = '';
}
echo '<table border="0" class="table" align="center" cellspacing="1" cellpadding="3">
<tr>
<td align="center" class="td1" width="16%">Welcome</td>
<td align="center" class="td1" width="17%">MySQL Connection Setup</td>
<td align="center" class="td1" width="17%">Create Tables</td>
<td align="center" class="td1" width="17%">Setup Settings</td>
<td align="center" class="td1" width="16%">Create User</td>
<td align="center" class="td2" width="17%">Create Category/Board</td>
</tr>
</table><hr />';

echo '<p class="td2">In order to complete the install, you will now make one category board and one regular board. After the install you may add in new boards.</p><br />';
#see what type of board to create.
if(empty($type)){
	echo '<form method="post" action="install.php?step=install_4b&amp;type=1">

<div class="td1">Add Category</div>
<div class="td2">Board Name<br />
<input type="text" name="board_name" size="30" class="text" /><br />
<br />
<div class="td1" align="center"><b>Board Permission</b></div>
<br />
Read Access<br />
<select name="readaccess" class="text">
<option value="1">Level 1 Users</option>
<option value="2">Level 1 & 2 Users</option>
<option value="3">Registered Users</option>
<option value="0" selected=selected>Everyone</option>
</select>
<hr />
<div align="center"><input type="submit" value="Add Board" class="submit" /></div>
</div>
</form><br />'; 
}else{
	$cat_select = @acpcategory_select();	
	echo '<form method="post" action="install.php?step=install_4b&amp;type=2">
<div class="td1">Add New Message Board</div>
<div class="td2">Board Name<br />
<input type="text" name="board_name" size="30" class="text" />
<hr />
Description<br />
<textarea name="description" rows="3" cols="50" class="text"></textarea>
<br /><br />
<div class="td1" align="center"><b>Board Permissions</b></div>
<br />
Who can Read this Board?<br />
<select name="readaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3">Registered Users</option>
<option value="0" selected=selected>Everyone</option>
</select>
<hr />
Who can Post in this Board?<br />
<select name="writeaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3" selected=selected>Registered Users</option>
<option value="4">No Access</option>
</select>
<hr />
Who can Reply to topics?<br />
<select name="replyaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3" selected=selected>Registered Users</option>
<option value="4">No Access</option>
</select>
<hr />
Who can vote on the polls?<br />
<select name="voteaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3" selected=selected>Registered Users</option>
<option value="4">No Access</option>
</select>
<hr />
Who can create new polls?<br />
<select name="pollaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3" selected=selected>Registered Users</option>
<option value="4">No Access</option>
</select>
<hr />
Who can edit topics?<br />
<select name="editaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3" selected=selected>Registered Users</option>
<option value="4">No Access</option>
</select>
<hr />
Who can delete topics?<br />
<select name="deleteaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3" selected=selected>Registered Users</option>
<option value="4">No Access</option>
</select>
<hr />
Who can add attachments to Topics?<br />
<select name="attachmentaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2">Level 2</option>
<option value="3" selected=selected>Registered Users</option>
<option value="4">No Access</option>
</select>
<hr />
Who can post Important Topics?<br />
<select name="importantaccess" class="text">
<option value="5">Private Access</option>
<option value="1">Level 1</option>
<option value="2" selected=selected>Level 2</option>
<option value="3">Registered Users</option>
<option value="4">No Access</option>
</select>
<br /><br />
<div class="td1" align="center"><b>Board Settings</b></div>
<br />
Category<br />'.$cat_select.'
<hr />
Allow post in board count towards user\'s post count<br />
<input type="radio" name="increment" value="1"  />Yes <input type="radio" name="increment" value="0" />No
<hr />
BBCode<br />
<input type="radio" name="bbcode" value="1" />On <input type="radio" name="bbcode" value="0" />Off
<hr />
Smiles<br />
<input type="radio" name="smiles" value="1" />On <input type="radio" name="smiles" value="0" />Off
<hr />
[img] Tag<br />
<input type="radio" name="img" value="1" />On <input type="radio" name="img" value="0" />Off
<hr />
<div align="center"><input type="submit" value="Create Board" class="submit" /></div>
</div>
</form><br />'; 
	}
  break;
  case 'install_4b':
$type = var_cleanup($_GET['type']);
#set error values to default.
$error = 0;
$errormsg = '';
#see where to go based on type variable.
if($type == 1){
#get form values.
$board_name = var_cleanup($_POST['board_name']);
$description = 'null';
$readaccess = var_cleanup($_POST['readaccess']);
$writeaccess = 4;
$replyaccess = 4;
$voteaccess = 4;
$pollaccess = 4;
$editaccess = 4;
$deleteaccess = 4;
$importantaccess = 4;
$attachmentaccess = 4;
$catsel = 0;
$bbcode = 0;
$increment = 0;
$smiles = 0;
$img = 0;
$board_order = 1;
$successmsg = 'Category created. Time to go make a <a href="install.php?step=install_4&type=2">board</a>.';
#error check.
if(empty($board_name)){
$errormsg = "You did not enter a name for the board.\n\n";
$error = 1;
}
if(strlen($board_name) > 50){
$errormsg .= "The name you chose is too long, pick a shorter name.\n\n";
$error = 1;
}
if ($readaccess == ""){
$errormsg .= "You did not enter a access rule to the board.\n\n";
$error = 1;
}
 
}else{
$board_name = var_cleanup($_POST['board_name']);
$description = var_cleanup($_POST['description']);
$readaccess = var_cleanup($_POST['readaccess']);
$writeaccess = var_cleanup($_POST['writeaccess']);
$replyaccess = var_cleanup($_POST['replyaccess']);
$voteaccess = var_cleanup($_POST['voteaccess']);
$pollaccess = var_cleanup($_POST['pollaccess']);
$editaccess = var_cleanup($_POST['editaccess']);
$deleteaccess = var_cleanup($_POST['deleteaccess']);
$importantaccess = var_cleanup($_POST['importantaccess']);
$attachmentaccess = var_cleanup($_POST['attachmentaccess']);
$catsel = var_cleanup($_POST['catsel']);
$increment = var_cleanup($_POST['increment']);
$bbcode = var_cleanup($_POST['bbcode']);
$smiles = var_cleanup($_POST['smiles']);
$img = var_cleanup($_POST['img']);
$board_order = 1;
$successmsg = 'Board created. Go to <a href="install.php?step=install_5">step 5</a> to complete install.';
//do some error checking.
if (empty($board_name)){
$errormsg .= $cp['boardnameerror']."\n\n";
$error = 1;
}
if (empty($description)){
$errormsg .= $cp['descriptionerror']."\n\n";
$error = 1;
}
if ($readaccess == ""){
$errormsg .= $cp['noreadsetting']."\n\n";
$error = 1;
}
if ($writeaccess == ""){
$errormsg .= $cp['nowritesetting']."\n\n";
$error = 1;
}
if ($replyaccess == ""){
$errormsg .= $cp['noreplysetting']."\n\n";
$error = 1;
}
if ($voteaccess == ""){
$errormsg .= $cp['novotesetting']."\n\n";
$error = 1;
}
if ($pollaccess == ""){
$errormsg .= $cp['nopollsetting']."\n\n";
$error = 1;
}
if ($editaccess == ""){
$errormsg .= $cp['noeditsetting']."\n\n";
$error = 1;
}
if ($deleteaccess == ""){
$errormsg .= $cp['nodeletesetting']."\n\n";
$error = 1;
}
if($attachmentaccess == ""){
$errormsg .= "You did not set a rule for attachments.\n\n";
$error = 1;
}
if ($importantaccess == ""){
$errormsg .= $cp['noimportantsetting']."\n\n";
$error = 1;
}
if (empty($catsel)){
$errormsg .= $cp['categoryerror']."\n\n";
$error = 1;
}
if($increment == ""){
$errormsg .= $cp['incrementerror']."\n\n";
$error = 1; 
}
if ($bbcode == ""){
$errormsg .= $cp['bbcodeerror']."\n\n";
$error = 1;
}
if ($smiles == ""){
$errormsg .= $cp['smileserror']."\n\n";
$error = 1;
}
if ($img == ""){
$errormsg .= $cp['imgerror']."\n\n";
$error = 1;
}
if(strlen($board_name) > 50){
$errormsg .= $cp['longboardname']."\n\n";
$error = 1;
}  
}
#see if any errors occured and if so report it.
if ($error == 1){
$error = nl2br($errormsg);
echo error($error, "validate");
}else{
//process the query.
$db->run = "insert into ebb_boards (Board, Description, type, Category, Smiles, Post_Increment, BBcode, Image, B_Order) values ('$board_name', '$description', '$type', '$catsel', '$smiles', '$increment', '$bbcode', '$img', '$board_order')";
$db->query();
$db->close();
//insert the permission rules into the permission table.
$db->run = "SELECT id FROM ebb_boards order by id DESC LIMIT 1";
$r_id = $db->result();
$db->close();
// process query.
$db->run = "insert into ebb_board_access (B_Read, B_Post, B_Reply, B_Vote, B_Poll, B_Delete, B_Edit, B_Attachment, B_Important, B_id) values ('$readaccess', '$writeaccess', '$replyaccess', '$voteaccess', '$pollaccess', '$deleteaccess', '$editaccess', '$attachmentaccess', '$importantaccess', '$r_id[id]')";
$db->query();
$db->close();
#print out message..
echo '<p class="td2">'.$successmsg.'</p>';
}
  break;
  case 'install_5':
echo '<p class="td2"><b>Step 5:</b>&nbsp;Removing Install files & finishing installation</p>';
$delinstall = @unlink ('install.php');
$delinstall2 = @unlink ('index.php');
@unlink('upgrade.php');
if (($delinstall) AND ($delinstall2)){
echo "<p class=\"td2\">Deleting install files...Success!</p>
<p class=\"td2\">Elite Bulletin Board is now set-up and ready to go.</p>";
}else{
echo "<p class=\"td2\">Deleting install files...<b>Failed!</b> didn't CHMOD folder 777 or 755.</p>
<p class=\"td2\"><b>Delete install.php & install.css <u>immediately</u> to prevent someone from overwriting this install!!!!</b><br /><br />
Elite Bulletin Board is now set-up and ready to go.</p>";
}
  break;
  default:
#go to index page of install folder.
header("Location: index.php");
}
//display footer
?>
</body>
</html>
<?php
ob_end_flush();
?>
