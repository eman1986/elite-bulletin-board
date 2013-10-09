<?php
if (!defined('IN_EBB') ) {
die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: posting_function.php
Last Modified: 06/30/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
#Smiles function - converts smiles bbcode
function smiles($string) {

	global $db;

	$db->run = "SELECT code, img_name FROM ebb_smiles";
	$smiles_q = $db->query();
	$db->close();	
	while ($row = mysql_fetch_assoc ($smiles_q)) {
		$smilecode = array ($row['code']);
		foreach ($smilecode as $smiles) {

			$string = str_replace($smiles,"<img src=\"images/smiles/$row[img_name]\" alt=\"\" />",$string);
		}
	}
	return ($string);
}
#smiles output for the form
function form_smiles($val){

	global $allowsmile, $db;

	if ($allowsmile == 0){
		$smile = '';
	}else{
		$smile = '';
		$x=0; // we will use this to count to four later
		$db->run = "SELECT code, img_name FROM ebb_smiles limit 12";
		$smiles = $db->query();
		$db->close();
		while($row = mysql_fetch_assoc($smiles)){
			if (($x % 4) == 0) {
				$smile .= "<br />";  // $x == 4 so we start the line again
				$x=0; // $x is now 4 so we reset it here to start the next line
			}
			$smile .= "<a href=\"javascript:smile(' $row[code] ', '$val')\"><img src=\"images/smiles/$row[img_name]\" border=\"0\" alt=\"smiles\" /></a>&nbsp;";
			$x++; // increment $x by 1 so we get our 4
		}
	}
	return ($smile);
}
#show all smiles output for the form.
function showall_smiles(){

	global $allowsmile, $db;
	
	$allsmile = '';
	$x=0; // we will use this to count to eight later

	$db->run = "SELECT code, img_name FROM ebb_smiles";
	$smiles = $db->query();
	$db->close();

	while($row = mysql_fetch_assoc($smiles)){
		if (($x % 3) == 0) {
			$allsmile .= "</tr><tr>";  // $x == 8 so we start the line again
			$x=0; // $x is now 8 so we reset it here to start the next line
		}
		$allsmile .= "<td class=\"td1\" align=\"center\" width=\"20%\"><img src=\"images/smiles/$row[img_name]\" alt=\"\" /></td>
		<td class=\"td2\" width=\"20%\">$row[code]</td>";
		$x++; // increment $x by 1 so we get our 8

	}
	return ($allsmile);
}

#BBCode function - converts bbcode
function BBCode($string, $allowimgs = false) {

	$string = preg_replace('~\[i\](.*?)\[\/i\]~is', '<i>\\1</i>', $string);
    $string = preg_replace('~\[b\](.*?)\[\/b\]~is', '<b>\\1</b>', $string);
    $string = preg_replace('~\[u\](.*?)\[\/u\]~is', '<u>\\1</u>', $string);
    $string = preg_replace('~\[url\](.*?)\[\/url\]~is', '<a href="\\1">\\1</a>', $string);    
	//get back to this task later...
	$string = preg_replace('#([^\'"=\]]|^)(http[s]?|ftp[s]?|gopher|irc){1}://([:a-z_\-\\./0-9%~]+){1}(\?[a-z=0-9\-_&;]*)?(\#[a-z0-9]+)?#mi', '\1<a href="\2://\3\4\5" target="_blank">\2://\3\4\5</a>', $string);
	
	$string = preg_replace('~\[list\](.*?)\[\/list\]~is', '<li>\\1</li>', $string);
	$string = preg_replace('~\[center\](.*?)\[\/center\]~is', '<div align="center">\\1</div>', $string);
    $string = preg_replace('~\[right\](.*?)\[\/right\]~is', '<div align="right">\\1</div>', $string);
    $string = preg_replace('~\[left\](.*?)\[\/left\]~is', '<div align="left">\\1</div>', $string);
    $string = preg_replace('~\[sub\](.*?)\[\/sub\]~is', '<sub>\\1</sub>', $string);
    $string = preg_replace('~\[sup\](.*?)\[\/sup\]~is', '<sup>\\1</sup>', $string);
    $string = preg_replace('~\[marque\](.*?)\[\/marque\]~is', '<marquee>\\1</marquee>', $string);
    $string = preg_replace('~\[quote\](.*?)\[\/quote\]~is', "<div class=\"quoteheader\">Quote:</div><div class=\"quote\">\\1</div>", $string);
    $string = preg_replace('~\[quote=(.*?)\](.*?)\[\/quote\]~is', "<div class=\"quoteheader\"> \\1 Wrote:</div><div class=\"quote\">\\2</div>", $string);
    $string = preg_replace('~\[code\](.*?)\[\/code\]~is', "<div class=\"codeheader\">Code:</div><div class=\"code\">\\1</div>", $string);

    //we don't want to allow imgs all the time!
    if ($allowimgs == true) {
		$string = preg_replace('~\[img\](.*?)\[\/img\]~is', '<img src="\\1" border="0" alt="" />', $string);
    }
    return ($string);
}
#printable-version bbcode
function BBCode_print($string) {

	$string = preg_replace('~\[i\](.*?)\[\/i\]~is', '<i>\\1</i>', $string);
    $string = preg_replace('~\[b\](.*?)\[\/b\]~is', '<b>\\1</b>', $string);
    $string = preg_replace('~\[u\](.*?)\[\/u\]~is', '<u>\\1</u>', $string);
    $string = preg_replace('~\[url\](.*?)\[\/url\]~is', '<a href="\\1">\\1</a>', $string);
    $string = preg_replace('#([^\'"=\]]|^)(http[s]?|ftp[s]?|gopher|irc){1}://([:a-z_\-\\./0-9%~]+){1}(\?[a-z=0-9\-_&;]*)?(\#[a-z0-9]+)?#mi', '\1<a href="\2://\3\4\5" target="_blank">\2://\3\4\5</a>', $string);
    $string = preg_replace('~\[list\](.*?)\[\/list\]~is', '<li>\\1</li>', $string);
    $string = preg_replace('~\[center\](.*?)\[\/center\]~is', '<div align="center">\\1</div>', $string);
    $string = preg_replace('~\[right\](.*?)\[\/right\]~is', '<div align="right">\\1</div>', $string);
    $string = preg_replace('~\[left\](.*?)\[\/left\]~is', '<div align="left">\\1</div>', $string);
    $string = preg_replace('~\[sub\](.*?)\[\/sub\]~is', '<sub>\\1</sub>', $string);
    $string = preg_replace('~\[sup\](.*?)\[\/sup\]~is', '<sup>\\1</sup>', $string);
    $string = preg_replace('~\[marque\](.*?)\[\/marque\]~is', '<marquee>\\1</marquee>', $string);
    $string = preg_replace('~\[quote\](.*?)\[\/quote\]~is', "<div class=\"quoteheader\">Quote:</div><div class=\"quote\">\\1</div>", $string);
    $string = preg_replace('~\[quote=(.*?)\](.*?)\[\/quote\]~is', "<div class=\"quoteheader\"> \\1 Wrote:</div><div class=\"quote\">\\2</div>", $string);
    $string = preg_replace('~\[code\](.*?)\[\/code\]~is', "<div class=\"codeheader\">Code:</div><div class=\"code\">\\1</div>", $string);

	return ($string);
}
#bbcode button output
function bbcode_form($val){

	global $allowbbcode, $allowimg;

	if ($allowbbcode == 0){
		$bbcode = '';
	}else{
		$bbcode = "<input type=\"button\" value=\"B\" onclick=\"javascript:bold('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"I\" onclick=\"javascript:italic('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"U\" onclick=\"javascript:underline('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Url\" onclick=\"javascript:url('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Quote\" onclick=\"javascript:quote('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Code\" onclick=\"javascript:code('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Marque\" onclick=\"javascript:marque('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Superscript\" onclick=\"javascript:sup('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Subscript\" onclick=\"javascript:sub('$val')\" class=\"submit\" />&nbsp;
		<input type=\"button\" value=\"List\" onclick=\"javascript:list('$val')\" class=\"submit\" />&nbsp;";
		if ($allowimg == 1){
			$bbcode .= "<input type=\"button\" value=\"Image\" onclick=\"javascript:img('$val')\" class=\"submit\" />";
		}
		$bbcode .= "<input type=\"button\" value=\"Left\" onclick=\"javascript:left('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Center\" onclick=\"javascript:center('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Right\" onclick=\"javascript:right('$val')\" class=\"submit\" />";
	}
    return ($bbcode);
}
#language filter function - filters words
function language_filter($string, $type) {

	global $db, $cp;
	
	if((!isset($string)) or (empty($string))){
		die('spam check is null.');
	}
	if((!isset($type)) or (empty($type))){
		die($cp['invalidcensoraction']);
	}
	#determine type action.
   	if($type == 1){
		$db->run = "SELECT Original_Word FROM `ebb_censor` where action='1'";
		$words = $db->query();
		$db->close();
		#see what to do based on action type.
		$stars = '';
		while ($row = mysql_fetch_assoc ($words)) {
			$obscenities = array ($row['Original_Word']);
			foreach ($obscenities as $curse_word) {
				if (stristr(trim($string), $curse_word)) {
					$length = strlen($curse_word);
					for ($i = 1; $i <= $length; $i++) {
						$stars .= "*";
					}
					$string = eregi_replace($curse_word,$stars,trim($string));
					$stars = "";
				}
			}
		}
	}else{
		$db->run = "SELECT Original_Word FROM `ebb_censor` where action='2'";
		$words = $db->query();
		$db->close();
		while ($row = mysql_fetch_assoc ($words)) {
			//see if anything matches the spam word list.
			if (preg_match("/\b".$row['Original_Word']."\b/i", $string)) {
				die('SPAMMING ATTEMPT!');
			}
		}
	}
   return ($string);
}
#flood check
function flood_check($string, $type){

	global $db;

   	if((!isset($string)) or (empty($string))){
		die('No string found.');
	}
	if((!isset($type)) or (empty($type))){
		die('No Type found.');
	}

	#see what action to perform based on type.
	switch($type){
	case 'posting':
		$currtime = time() - 30;
		$db->run = "SELECT last_post FROM ebb_users WHERE Username='$string'";
		$get_time_r = $db->result();
		$db->close();
		#see if user is posting too quickly.
		if ($get_time_r['last_post'] > $currtime){
			$flood = 1;
		}else{
			$flood = 0;
		}
	break;
	case 'search':
		$currtime = time() - 20;
		$db->run = "SELECT last_search FROM ebb_users WHERE Username='$string'";
		$get_time_r = $db->result();
		$db->close();
		#see if user is posting too quickly.
		if ($get_time_r['last_search'] > $currtime){
			$flood = 1;
		}else{
			$flood = 0;
		}	
	break;
	}
	return ($flood);
}
#increase user's post count.
function post_count($string){

	global $db;

   	if((!isset($string)) or (empty($string))){
		die('No string found.');
	}
	//get current post count then add on to it.
	$db->run = "select Post_Count from ebb_users where Username='$string'";
	$get_num = $db->result();
	$db->close();
	$increase_count = $get_num['Post_Count'] + 1;
	$db->run = "UPDATE ebb_users SET Post_Count='$increase_count' WHERE Username='$string'";
	$db->query();
	$db->close();
}
#update board table. error here!!!
function update_board($bid, $newlink, $user){

	global $db, $time; 
	#update lasy post details for the selected board.
	$db->run = "update ebb_boards SET last_update='$time' WHERE id='$bid'";
	$db->query();
	$db->close();
	//update post link for board.
	$db->run = "Update ebb_boards SET Post_Link='$newlink', Posted_User='$user' WHERE id='$bid'";
	$db->query();
	$db->close();
	#clear data from read table for the board selected.
	$db->run = "DELETE FROM ebb_read_board WHERE Board='$bid'";
	$db->query();
	$db->close();
}
#update topic table. error here!!!
function update_topic($tid, $newlink, $user){

	global $db, $time;
	#update lasy post details for the selected topic.
	$db->run = "update ebb_topics SET last_update='$time' WHERE tid='$tid'";
	$db->query();
	$db->close();
	#clear data from read table for the topic selected.
	$db->run = "DELETE FROM ebb_read_topic WHERE Topic='$tid'";
	$db->query();
	$db->close(); 
	//update post link for topic.
	$db->run = "Update ebb_topics SET Post_Link='$newlink' WHERE tid='$tid'";
	$db->query();
	$db->close();
	//update last poster for topic.
	$db->run = "Update ebb_topics SET Posted_User='$user' WHERE tid='$tid'";
	$db->query();
	$db->close();
}

/**
 * Subscribe or unsubscribe from a topic.
 * @global db $db
 * @global string $logged_user
 * @param integer $tid the Topic ID we want this to affect.
 * @param string $mode are we subscribing or unsubscribing?
 * @version 06/30/12
 */
function SubscriptionManager($tid, $mode) {

	global $db, $logged_user;
	
	//get a check to see if they are a part the topic defined.
	$db->run = "SELECT tid from ebb_topic_watch WHERE username='$logged_user'";
	$subscription_status = $db->num_results();
	$db->close();
	
	//see if they want to subscribe or unsubscribe to a topic.
	if ($mode == "subscribe" && $subscription_status == 0) {
		$db->run = "insert into ebb_topic_watch (username, tid, status) values ('$logged_user', '$tid', 'Unread')";
		$db->query();
		$db->close();
	}elseif ($mode == "unsubscribe" && $subscription_status > 0) {
		$db->run = "DELETE FROM ebb_topic_watch WHERE username='$logged_user'";
		$db->query();
		$db->close();
	} 
}
?>
