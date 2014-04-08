<?php
if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: function.php
Last Modified: 1/23/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
#anti-sql injection function
function anti_injection($string){
	global $db;
	$db->connect();
	$db->close();
	//strip any slahes found.
	if (get_magic_quotes_gpc()){
		$string = stripslashes($string);
	}
	//if not a number or a numeric string
	if (!is_numeric($string)){
		$string = mysql_real_escape_string(trim($string));
	}
	return $string;
}
#html-cleaning functions
function removeEvilAttributes($string){
	$stripAttrib = "' (style|class)=\"(.*?)\"'i";
	$string = stripslashes($string);
	$string = preg_replace($stripAttrib, '', $string);
	return $string;
}
#total variable cleaning function.
function var_cleanup($string){

	$var = anti_injection($string);
	$var = removeEvilAttributes($string);
	$var = htmlentities($string, ENT_QUOTES); 

return ($var);
}
#settings function.
function board_settings($var){

	global $db;
	#error check.
	if(empty($var)){
		echo "Board Setting Function not called correctly!";
		exit(); 
	}
	$db->run = "SELECT $var FROM ebb_settings";
	$settings = $db->result();
	$db->close();

	return ($settings);
}
#pagination
function pagination($actions){

	global $num, $txt, $settings, $pg, $count, $count2;

	// Figure out the total number of pages. Always round up using ceil()
	$total_pages = ceil($num / $settings['per_page']);
	$pagination = "<div class=\"pagination\"><p>$txt[pages]</p><ul>";
	// Build page number
	if($pg > 1){
		$prev = ($pg - 1);
		$pagination .= "<li class=\"disablepage\"><a href=\"$_SERVER[PHP_SELF]?".$actions."pg=$prev\">$txt[prev]</a></li>";
	}
	//output numbers.
	for($i = 1; $i <= $total_pages; $i++){
		#see if this is the current page.
		if($pg == $i){
			$pagination .= "<li class=\"currentpage\"><b>$i</b></li>";
		}else{
			#dot out a few page numbers to prevent rows of links. 
			if($pg > 4 && $i > 3 && $i < ($pg - 1) && $i < ($total_pages - 3)){
				$count ++;
				$pagination .= ($count == 1)? "<li>...</li>" : "";
			}elseif($i > ($pg + 2) && $i < ($total_pages - 2)){
				$count2 ++;
				$pagination .= ($count2 == 1)? "<li>...</li>" : "";
			}else{
				$pagination .= "<li><a href=\"$_SERVER[PHP_SELF]?".$actions."pg=$i\">$i</a></li>";
			}
		}
	}
	// Build Next Link
	if($pg < $total_pages){
		$next = ($pg + 1);
		$pagination .= "<li class=\"nextpage\"><a href=\"$_SERVER[PHP_SELF]?".$actions."pg=$next\">$txt[next]</a></li>";
	}
	$pagination .= "</ul></div>";

	return ($pagination);
}
#filetype lookup.
function filetype_lookup($filetype){

	global $db;

	if(empty($filetype)){
		echo "Filetype Lookup Function not called correctly!";
		exit(); 
	}
	$db->run = "SELECT ext FROM ebb_attachment_extlist WHERE ext='$filetype'";
	$compare = $db->num_results();
	$db->close();

    #see if the filetype matches the one in the db.
    if($compare == 1){
        return true; //the extension is allowed.
    }else{
        return false; //the extension is NOT allowed.
    }
}
#board status.
function board_stats(){

	global $db;

	//get member count.
	$db->run = "select id from ebb_users";
	$user_num = $db->num_results();
	$db->close();
	//get topic count.
	$db->run = "select tid from ebb_topics";
	$topic_num = $db->num_results();
	$db->close();
	//get post count.
	$db->run = "select pid from ebb_posts";
	$post_num = $db->num_results();
	$db->close();

	$b_stats = array($user_num, $topic_num, $post_num);

	return ($b_stats);
}
#check for setup files.
function checkinstall(){

	if (file_exists("install/install.php")){
		$setupexist = 1;
	}else{
		$setupexist = 0;
	}
	return ($setupexist);
}
#ban function
function check_ban(){

	global $stat, $db, $txt, $suspend_length, $suspend_date;

	#see if user is marked as banned.
	if($stat == "Banned"){
		$error = $txt['banned'];
		echo error($error, "error");
	}
	#see if user is suspended.
	if($suspend_length > 0){
		#see if user is still suspened.
		$math = 3600 * $suspend_length;
		$suspend_time = $suspend_date + $math;
		$today = time() - $math;
		if($suspend_time > $today){
			$error = $txt['suspended'];
			echo error($error, "error");
		}
	}
	#see if the IP of the user is banned.
	$db->run = "SELECT ban_item FROM ebb_banlist WHERE ban_type='IP'";
	$ban_q = $db->query();
	$db->close();
	while ($row = mysql_fetch_assoc ($ban_q)){
		$uip = $_SERVER['REMOTE_ADDR'];
		if ($uip == $row['ban_item']){
			$error = $txt['banned'];
			echo error($error, "error");
		}
	}
}
#ban email check.
function check_email($string){
	global $db;

	#domain check.
	$checkDomain = explode("@", $string);

	$db->run = "SELECT match_type, ban_item FROM ebb_banlist WHERE ban_type='Email' AND ban_item like '$checkDomain[1]' or ban_item='$string'";
	$emailmatch_chk = $db->num_results();
	$emailban_q = $db->query();
	$db->close();

	if ($emailmatch_chk == 0){
		$emailban = 0;
	}else{
		while ($row = mysql_fetch_assoc($emailban_q)) {
			if ($row['match_type'] == "Wildcard") {
				$emailban = 1;
			}else{
				if ($row['ban_item'] == $string) {
					$emailban = 1;
				}
			}
		}
	}
	return ($emailban);
}
#validate email MX record function.
function validate_email_mx($email){

	if(checkdnsrr(array_pop(explode("@",$email)),"MX")){
		return true;
	}else{
		return false;
	}
}
#blacklisted username check.
function blacklisted_usernames($value){

	global $db;

	$db->run = "SELECT blacklisted_username FROM ebb_blacklist";
	$result = $db->query();
	$db->close();
	
	$blklist = '';
	while($row = mysql_fetch_assoc($result)) {
		if (stristr($value, $row['blacklisted_username']) === true) {
			$blklist = 1;
		}else{
			$blklist = 0;
		}
	}
	return ($blklist);
}
#php parsing function for information ticker.
function nl2p($string) {
	return "<p align=\"center\">" . str_replace("\n", "</p><p align=\"center\">", $string) . "</p>";
}
#random password generator
function makeRandomPassword() {
	$salt = "abchefghjkmnpqrstuvwxyz0123456789";
	$pass = "";
	srand((double)microtime()*1000000);
  	$i = 0;
  	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($salt, $num, 1);
		$pass = $pass . $tmp;
		$i++;
  	}
  	return $pass;
}
#newpost counter.
function newpost_counter(){

	global $search_result, $search_result2, $logged_user, $db;
	//output any topics
	$count = 0;	 
	#get topic count.
	while ($r = mysql_fetch_assoc($search_result)) {
		$db->run = "select * from ebb_read_topic WHERE Topic='$r[tid]' and User='$logged_user'";
		$read_stat = $db->num_results();
		$db->close();
		if ($read_stat == 0){
			//increment count
			$count++;
		}
	}
	#post count
	while ($r2 = mysql_fetch_assoc($search_result2)){
		//see if post is new.
		$db->run = "select * from ebb_read_topic WHERE Topic='$r2[tid]' and User='$logged_user'";
		$read_stat2 = $db->num_results();
		$db->close();
		if ($read_stat2 == 0){
			//increment count
			$count++;
		}
	}
	return ($count);
}
#ip-viewer code
function ip_checker(){

	global $u, $ip, $index, $db;

	$db->run = "select Username from ebb_users where Username='$u' or IP='$ip'";
	$query = $db->query();
	$db->close();
	$iplist = '';
	while ($row = mysql_fetch_assoc ($query)){
		//get number of times the ip was used by this user.
		$db->run = "select * from ebb_topics where author='$row[Username]' and IP='$ip'";
		$count1 = $db->num_results();
		$db->close();
		
		$db->run = "select * from ebb_posts where author='$row[Username]' and IP='$ip'";
		$count2 = $db->num_results();
		$db->close();
		$total_count = $count1 + $count2;

		$iplist .= "$row[Username] - $total_count $index[posts]<br />";
	}
	return $iplist;
}
#get other ips the poster used before.
function other_ip_check(){

	global $ip, $index, $u, $db;

	#topic IP check.
   	$db->run = "select DISTINCT IP from ebb_topics where author='$u'";
	$q = $db->query();
	$ip_ct = $db->num_results();
	$db->close();
	$ipcheck = '';
	if($ip_ct > 0){
		while ($row = mysql_fetch_assoc ($q)){
			//get number of times the ip was used by this user.
			$ipcheck .= "$row[IP]<br />";
		}
	}
	#post IP check.
   	$db->run = "select DISTINCT IP from ebb_posts where author='$u'";
	$q2 = $db->query();
	$ip2_ct = $db->num_results();
	$db->close();

	if($ip2_ct > 0){
		while ($row2 = mysql_fetch_assoc ($q2)){
			//get number of times the ip was used by this user.
			$ipcheck .= "$row[IP]<br />";
		}
	}
	return ($ipcheck);
}
#error logging
function error($error, $type, $errorq = 'N/A'){

	global $title, $txt, $db, $template_path;
	if (($error == "") and ($type == "")){
		echo "Function not defined correctly!";
		exit();
	}
	switch($type){
	case 'error':
		$page = new template($template_path ."/error.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$txt[error]",
		"ERRORMSG" => "$error"));
		$page->output();
		exit();
	break;
	case 'general':
		$page = new template($template_path ."/error.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$txt[info]",
		"ERRORMSG" => "$error"));
		$page->output();
	break;
	case 'validate':
		$page = new template($template_path ."/error-validate.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$txt[error]",
		"LANG-GOBACK" => "$txt[goback]",
		"LANG-ERRORMSG" => "$txt[errormsg]",
		"ERRORMSG" => "$error"));
		$page->output();
	break;
	}
}
#ACP error logging
function acp_error($error, $type, $errorq = 'N/A'){

	global $title, $txt, $db, $template_path, $board_address;
	if (($error == "") and ($type == "")){
		echo "Function not defined correctly!";
		exit();
	}
	switch($type){
	case 'error':
		$page = new template("../".$template_path ."/error.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$txt[error]",
		"ERRORMSG" => "$error"));
		$page->output();
		exit();
	break;
	case 'general':
		$page = new template("../".$template_path ."/error.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$txt[info]",
		"ERRORMSG" => "$error"));
		$page->output();
	break;
	case 'validate':
		$page = new template("../".$template_path ."/error-validate.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$txt[error]",
		"LANG-GOBACK" => "$txt[goback]",
		"LANG-ERRORMSG" => "$txt[errormsg]",
		"ERRORMSG" => "$error"));
		$page->output();
	break;
	}
}
?>
