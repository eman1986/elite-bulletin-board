-- Elite Bulletin Board v3.0.0 RC2 SQL Dump
-- Date: 04/12/2013
-- http://elite-board.us

--
-- Table structure for table `ebb_attachments`
--

CREATE TABLE IF NOT EXISTS `ebb_attachments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Username` mediumint(8) unsigned NOT NULL,
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `pid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Filename` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `encryptedFileName` varchar(40) COLLATE utf8_bin NOT NULL,
  `encryptionSalt` varchar(8) COLLATE utf8_bin NOT NULL,
  `File_Type` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `File_Size` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `Download_Count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_attachment_extlist`
--

CREATE TABLE IF NOT EXISTS `ebb_attachment_extlist` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ext` varchar(100) CHARACTER SET ucs2 COLLATE ucs2_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=56 ;

--
-- Dumping data for table `ebb_attachment_extlist`
--

INSERT INTO `ebb_attachment_extlist` (`id`, `ext`) VALUES
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
(55, 'chm');

-- --------------------------------------------------------

--
-- Table structure for table `ebb_banlist_email`
--

CREATE TABLE IF NOT EXISTS `ebb_banlist_email` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ban_email` varchar(255) COLLATE utf8_bin NOT NULL,
  `ban_wildcard` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_banlist_ip`
--

CREATE TABLE IF NOT EXISTS `ebb_banlist_ip` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ban_ip` varchar(45) COLLATE utf8_bin NOT NULL,
  `ban_wildcard` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_banlist_user`
--

CREATE TABLE IF NOT EXISTS `ebb_banlist_user` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ban_user` varchar(20) COLLATE utf8_bin NOT NULL,
  `ban_wildcard` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_boards`
--

CREATE TABLE IF NOT EXISTS `ebb_boards` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Board` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Description` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `last_update` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `Posted_User` mediumint(8) unsigned DEFAULT NULL,
  `tid` mediumint(8) unsigned DEFAULT NULL,
  `pid` mediumint(8) unsigned DEFAULT NULL,
  `last_page` tinyint(1) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `Category` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Smiles` tinyint(1) NOT NULL DEFAULT '0',
  `BBcode` tinyint(1) NOT NULL DEFAULT '0',
  `Post_Increment` tinyint(1) NOT NULL DEFAULT '0',
  `Image` tinyint(1) NOT NULL DEFAULT '0',
  `B_Order` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin PACK_KEYS=0 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ebb_boards`
--

INSERT INTO `ebb_boards` (`id`, `Board`, `Description`, `last_update`, `Posted_User`, `tid`, `pid`, `last_page`, `type`, `Category`, `Smiles`, `BBcode`, `Post_Increment`, `Image`, `B_Order`) VALUES
(1, 'testing', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 1),
(2, 'Demo', 'Test Board', NULL, NULL, NULL, NULL, NULL, 2, 1, 1, 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_board_access`
--

CREATE TABLE IF NOT EXISTS `ebb_board_access` (
  `B_Read` tinyint(1) NOT NULL DEFAULT '0',
  `B_Post` tinyint(1) NOT NULL DEFAULT '0',
  `B_Reply` tinyint(1) NOT NULL DEFAULT '0',
  `B_Vote` tinyint(1) NOT NULL DEFAULT '0',
  `B_Poll` tinyint(1) NOT NULL DEFAULT '0',
  `B_Delete` tinyint(1) NOT NULL DEFAULT '0',
  `B_Edit` tinyint(1) NOT NULL DEFAULT '0',
  `B_Attachment` tinyint(1) NOT NULL DEFAULT '0',
  `B_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`B_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ebb_board_access`
--

INSERT INTO `ebb_board_access` (`B_Read`, `B_Post`, `B_Reply`, `B_Vote`, `B_Poll`, `B_Delete`, `B_Edit`, `B_Attachment`, `B_id`) VALUES
(0, 4, 4, 4, 4, 4, 4, 4, 1),
(0, 2, 2, 3, 2, 2, 2, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_censor`
--

CREATE TABLE IF NOT EXISTS `ebb_censor` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Original_Word` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `action` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_cplog`
--

CREATE TABLE IF NOT EXISTS `ebb_cplog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `User` mediumint(8) unsigned NOT NULL,
  `Action` varchar(255) COLLATE utf8_bin NOT NULL,
  `Date` varchar(14) COLLATE utf8_bin NOT NULL DEFAULT '',
  `IP` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_grouplist`
--

CREATE TABLE IF NOT EXISTS `ebb_grouplist` (
  `group_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `board_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ebb_grouplist`
--

INSERT INTO `ebb_grouplist` (`group_id`, `board_id`, `type`) VALUES
(1, 2, 1),
(2, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_groups`
--

CREATE TABLE IF NOT EXISTS `ebb_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Description` tinytext COLLATE utf8_bin NOT NULL,
  `Enrollment` tinyint(1) NOT NULL DEFAULT '0',
  `Level` tinyint(1) NOT NULL DEFAULT '0',
  `permission_type` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ebb_groups`
--

INSERT INTO `ebb_groups` (`id`, `Name`, `Description`, `Enrollment`, `Level`, `permission_type`) VALUES
(1, 'Administrator', 'These are the people who are in charge. They have full power over the board.', 0, 1, 1),
(2, 'Moderator', 'These are the people who help the administrators manage the board. They have minor power over the board.', 1, 2, 3),
(3, 'Regular Member', 'Regular Member Status.', 2, 3, 4),
(4, 'Banned User', 'Users has no rights, lowest possible rank.', 0, 0, 6);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_group_member_request`
--

CREATE TABLE IF NOT EXISTS `ebb_group_member_request` (
  `username` mediumint(8) unsigned NOT NULL,
  `gid` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`gid`),
  UNIQUE KEY `gid` (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_information_ticker`
--

CREATE TABLE IF NOT EXISTS `ebb_information_ticker` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `information` varchar(50) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ebb_information_ticker`
--

INSERT INTO `ebb_information_ticker` (`id`, `information`) VALUES
(1, 'version 3.0.0 RC2 is under work'),
(2, 'username is admin and password is password'),
(3, 'This is NOT production code, use at your own risk!');

-- --------------------------------------------------------

--
-- Table structure for table `ebb_login_session`
--

CREATE TABLE IF NOT EXISTS `ebb_login_session` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` mediumint(8) unsigned NOT NULL,
  `last_active` varchar(14) COLLATE utf8_bin NOT NULL,
  `login_key` varchar(40) COLLATE utf8_bin NOT NULL,
  `admin_key` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `acp_activity` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_online`
--

CREATE TABLE IF NOT EXISTS `ebb_online` (
  `Username` mediumint(8) unsigned DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `time` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '',
  `location` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_permission_actions`
--

CREATE TABLE IF NOT EXISTS `ebb_permission_actions` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `permission` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=42 ;

--
-- Dumping data for table `ebb_permission_actions`
--

INSERT INTO `ebb_permission_actions` (`id`, `permission`, `type`) VALUES
(1, 'MANAGE_BOARDS', 1),
(2, 'PRUNE_BOARDS', 1),
(3, 'MANAGE_GROUPS', 1),
(4, 'MASS_EMAIL', 1),
(5, 'WORD_CENSOR', 1),
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
(18, 'MANAGE_WARNLOG', 1),
(19, 'ACTIVATE_USERS', 1),
(20, 'MOD_EDIT_TOPICS', 2),
(21, 'MOD_DELETE_TOPICS', 2),
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
(39, 'IMPORTANT_TOPIC', 3),
(40, 'EDIT_TOPICS', 3),
(41, 'DELETE_TOPICS', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_permission_data`
--

CREATE TABLE IF NOT EXISTS `ebb_permission_data` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `profile` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `permission` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `set_value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=145 ;

--
-- Dumping data for table `ebb_permission_data`
--

INSERT INTO `ebb_permission_data` (`id`, `profile`, `permission`, `set_value`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 1),
(3, 1, 3, 1),
(4, 1, 4, 1),
(5, 1, 5, 1),
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
(112, 5, 39, 0),
(113, 4, 40, 1),
(114, 4, 41, 1),
(115, 5, 40, 1),
(116, 5, 41, 1),
(131, 3, 26, 1),
(132, 3, 27, 1),
(133, 3, 28, 1),
(134, 3, 29, 1),
(135, 3, 30, 1),
(136, 3, 31, 1),
(137, 3, 32, 1),
(138, 3, 33, 1),
(139, 3, 34, 1),
(140, 3, 35, 1),
(141, 3, 36, 1),
(142, 3, 37, 1),
(143, 3, 38, 1),
(144, 3, 39, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_permission_profile`
--

CREATE TABLE IF NOT EXISTS `ebb_permission_profile` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `profile` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  `access_level` tinyint(1) NOT NULL DEFAULT '0',
  `system` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=7 ;

--
-- Dumping data for table `ebb_permission_profile`
--

INSERT INTO `ebb_permission_profile` (`id`, `profile`, `access_level`, `system`) VALUES
(1, 'Full Administrator', 1, 1),
(2, 'Limited Administrator', 1, 1),
(3, 'Moderator', 2, 1),
(4, 'Regular User', 3, 1),
(5, 'Limited User', 3, 1),
(6, 'Banned User', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_pm`
--

CREATE TABLE IF NOT EXISTS `ebb_pm` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Subject` varchar(25) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Sender` mediumint(8) unsigned NOT NULL,
  `Receiver` mediumint(8) unsigned NOT NULL,
  `Folder` varchar(7) COLLATE utf8_bin NOT NULL,
  `Message` text COLLATE utf8_bin NOT NULL,
  `Date` varchar(14) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Read_Status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_poll`
--

CREATE TABLE IF NOT EXISTS `ebb_poll` (
  `option_value` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `option_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`option_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_posts`
--

CREATE TABLE IF NOT EXISTS `ebb_posts` (
  `author` mediumint(8) unsigned NOT NULL,
  `pid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `bid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Body` text COLLATE utf8_bin NOT NULL,
  `IP` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Original_Date` varchar(14) COLLATE utf8_bin NOT NULL DEFAULT '',
  `disable_bbcode` tinyint(1) NOT NULL DEFAULT '0',
  `disable_smiles` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_preference`
--

CREATE TABLE IF NOT EXISTS `ebb_preference` (
  `pref_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `pref_value` varchar(255) COLLATE utf8_bin NOT NULL,
  `pref_type` tinyint(1) NOT NULL,
  UNIQUE KEY `pref_name` (`pref_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `ebb_preference`
--

INSERT INTO `ebb_preference` (`pref_name`, `pref_value`, `pref_type`) VALUES
('board_name', 'EBB Version 3 Development Board', 1),
('board_status', '1', 3),
('board_email', 'demo@email.com', 1),
('board_directory', 'ebbv3', 1),
('offline_msg', '', 1),
('infobox_status', '1', 3),
('default_style', '1', 2),
('default_language', 'english', 1),
('rules_status', '1', 3),
('rules', 'No Spamming please!', 1),
('per_page', '20', 2),
('captcha', '1', 3),
('pm_quota', '50', 2),
('archive_quota', '10', 2),
('activation', 'User', 1),
('allow_newusers', '1', 3),
('userstat', '3', 2),
('coppa', '21', 2),
('timezone', 'America/New_York', 2),
('timeformat', '0', 2),
('attachment_quota', '3072', 2),
('allow_guest_downloads', '0', 3),
('mx_check', '0', 3),
('warning_threshold', '50', 2),
('upload_limit', '5', 2),
('avatar_type', 'gravatar', 1),
('gravatar_noresults', 'patterns', 1),
('gravatar_rating', 'ignore', 1),
('gravatar_secure', '0', 3),
('gravatar_size', 'large', 1),
('dateformat', '0', 2);

-- --------------------------------------------------------

--
-- Table structure for table `ebb_read_topic`
--

CREATE TABLE IF NOT EXISTS `ebb_read_topic` (
  `Topic` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `User` mediumint(8) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_relationship`
--

CREATE TABLE IF NOT EXISTS `ebb_relationship` (
  `rid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` mediumint(8) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_sessions`
--

CREATE TABLE IF NOT EXISTS `ebb_sessions` (
  `session_id` varchar(40) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `ip_address` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `user_agent` varchar(120) COLLATE utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_spam_list`
--

CREATE TABLE IF NOT EXISTS `ebb_spam_list` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `spam_word` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_style`
--

CREATE TABLE IF NOT EXISTS `ebb_style` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Temp_Path` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin PACK_KEYS=0 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ebb_style`
--

INSERT INTO `ebb_style` (`id`, `Name`, `Temp_Path`) VALUES
(1, 'ClearBlue2', 'ClearBlue2');

-- --------------------------------------------------------

--
-- Table structure for table `ebb_topics`
--

CREATE TABLE IF NOT EXISTS `ebb_topics` (
  `author` mediumint(8) unsigned NOT NULL,
  `tid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Topic` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Body` text COLLATE utf8_bin NOT NULL,
  `topic_type` tinyint(1) NOT NULL DEFAULT '0',
  `important` tinyint(1) NOT NULL DEFAULT '0',
  `IP` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Original_Date` varchar(14) COLLATE utf8_bin NOT NULL DEFAULT '',
  `last_update` varchar(14) COLLATE utf8_bin NOT NULL DEFAULT '',
  `last_page` tinyint(1) DEFAULT NULL,
  `posted_user` varchar(25) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pid` mediumint(8) unsigned DEFAULT NULL,
  `Locked` tinyint(1) NOT NULL DEFAULT '0',
  `Views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Question` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `disable_bbcode` tinyint(1) NOT NULL DEFAULT '0',
  `disable_smiles` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin PACK_KEYS=0 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_topic_watch`
--

CREATE TABLE IF NOT EXISTS `ebb_topic_watch` (
  `username` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `read_status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_users`
--

CREATE TABLE IF NOT EXISTS `ebb_users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(25) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Password` varchar(60) COLLATE utf8_bin NOT NULL,
  `gid` mediumint(8) unsigned NOT NULL,
  `Email` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Custom_Title` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `last_visit` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `PM_Notify` tinyint(1) NOT NULL DEFAULT '0',
  `Hide_Email` tinyint(1) NOT NULL DEFAULT '0',
  `MSN` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `AOL` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Yahoo` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ICQ` varchar(15) COLLATE utf8_bin DEFAULT NULL,
  `WWW` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `Location` varchar(70) COLLATE utf8_bin DEFAULT NULL,
  `Avatar` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `Sig` tinytext COLLATE utf8_bin,
  `Time_format` tinyint(1) NOT NULL,
  `date_format` tinyint(1) NOT NULL,
  `Time_Zone` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Date_Joined` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `IP` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Style` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Language` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Post_Count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_post` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `last_search` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `failed_attempts` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `act_key` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `password_recovery_date` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  `warning_level` tinyint(1) NOT NULL DEFAULT '0',
  `suspend_length` tinyint(1) NOT NULL DEFAULT '0',
  `suspend_time` varchar(14) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ebb_users`
--

INSERT INTO `ebb_users` (`id`, `Username`, `Password`, `gid`, `Email`, `Custom_Title`, `last_visit`, `PM_Notify`, `Hide_Email`, `MSN`, `AOL`, `Yahoo`, `ICQ`, `WWW`, `Location`, `Avatar`, `Sig`, `Time_format`, `date_format`, `Time_Zone`, `Date_Joined`, `IP`, `Style`, `Language`, `Post_Count`, `last_post`, `last_search`, `failed_attempts`, `active`, `act_key`, `password_recovery_date`, `warning_level`, `suspend_length`, `suspend_time`) VALUES
(1, 'admin', '$2a$12$3XRGfvqFi51WiUEM3mMxd.zGEZwGjMTRYgI3w2bsUh9CQ0W3tkpQK', 1, 'admin@email.com', NULL, NULL, 0, 0, '', '', '', '', '', '', '', '', 1, 13, 'America/New_York', '1172859723', '127.0.0.1', 1, 'english', 0, '', '', 0, 1, '', NULL, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `ebb_votes`
--

CREATE TABLE IF NOT EXISTS `ebb_votes` (
  `Username` mediumint(8) unsigned NOT NULL,
  `tid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `Vote` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `ebb_warnlog`
--

CREATE TABLE IF NOT EXISTS `ebb_warnlog` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Username` mediumint(8) unsigned NOT NULL,
  `Authorized` mediumint(8) unsigned NOT NULL,
  `Action` tinyint(1) NOT NULL DEFAULT '0',
  `Message` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
