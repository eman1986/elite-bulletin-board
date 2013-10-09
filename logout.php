<?php
define('IN_EBB', true);
/*
Filename: logout.php
Last Modified: 5/22/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";

#see if a guest user is trying to go here for some unknown reason.
if($stat == "guest"){
	header("Location: index.php");
}
//remove user from who's online list.
$db->run = "delete from ebb_online where Username='$logged_user'";
$db->query();
$db->close();

#set the columes needed for now.
$columes = 'Username, Password';
#call user function.
$userpref = user_settings($logged_user, $columes);

#call board setting function.
$colume = 'cookie_path, cookie_domain, cookie_secure';
$settings = board_settings($colume);

//decide which login method to delete.
if(isset($_COOKIE['ebbuser'])){
	//remove cookie.
	$currtime = time() - (2592000);
	setcookie("ebbuser", $userpref['Username'], $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
	setcookie("ebbpass", $userpref['Password'], $currtime, $settings['cookie_path'], $settings['cookie_domain'], $settings['cookie_secure']);
}else{
	//clear session data.
	session_destroy();
}

#close out ACP cookie if needed
if (isset($_SESSION['ebbacpu']) and (isset($_SESSION['ebbacpp']))) {
	//clear session data.
	session_destroy();
}

//direct to index page.
header("Location: index.php");
ob_end_flush();
?>
