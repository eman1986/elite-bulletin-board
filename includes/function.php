<?php
if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: function.php
Last Modified: 9/11/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

#html-cleaning functions
function removeEvilAttributes($string){
	$stripAttrib = "' (style|class)=\"(.*?)\"'i";
	$string = stripslashes($string);
	$string = preg_replace($stripAttrib, '', $string);
	return $string;
}

/**
 * Determines if connection is secure.
 * @return bool
*/
function isSecure() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off');
}

/**
 * Will direct user to a secure connection if SSL is setup.
*/
function redirectToHttps(){
    if (isSecure()) {
        redirect("https://" . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']);
    }
}

/**
 * Will direct user to a defined location.
 * @param string url link to direct user to.
 * @param bool delay should the user wait to be redirected?
 * @param int sec  seconds that user should wait before being redirected.
*/
function redirect($url, $delay=false, $sec=5) {

    global $boardDir;

    #see if user will need wait for redirecting.
    if ($delay) {
        #convert & to &amp; for HTML-valid reasons.
        $url = str_replace('&', '&amp;', $url);

        #direct user using the META tag.
        echo '<meta http-equiv="refresh" content="'.$sec.';url=/'.$boardDir."/".$url.'" />';
    } else {
        #convert &amp; to &.
        $url = str_replace('&amp;', '&', $url);

        #direct user using HTTP/1.1 headers.
        header("Location: /".$boardDir."/".$url);
    }
}

/**
 * removes any extra / that some web server setup add to their DOCUMENT_ROOT settings.
 * @param string $str string
 * @return string
*/
function trailingSlashRemover($str) {
    #trim any unwanted things first.
    $str = trim($str);

    #ensure we don't remove all / in some cases.
    return $str == '/' ? $str : rtrim($str, '/');
}

/**
 * Will format time based on user preference.
 * @param string $format format in use.
 * @param int $time timestamp generated by time()
 * @param int $GMT GMT timezone to offset by.
 * @return string
*/
function formatTime($format, $time, $GMT){
    $gmtTime = gmdate ($format, $time);
    return date($format, strtotime("$GMT hours", strtotime($gmtTime)));
}

/**
 * Set flash data.
 * @param string $title name of flash data
 * @param string $msg content of flash data
*/
function set_flashdata($title, $msg) {
    if (is_array($msg)) {
        $concat = '';
        foreach ($msg as $i => $val) {
            $concat .= $i=0 ?  $val : $val.'<br />';
        }

        $_SESSION['flashData_'.$title] = $concat;
    } else {
        $_SESSION['flashData_'.$title] = $msg;
    }
}

/**
 * Get flash data.
 * @param string $title flash data to output.
 * @param bool $extendLife extend life to one more action?
 * @return string
*/
function get_flashdata($title, $extendLife=false) {
    if (isset($_SESSION['flashData_'.$title])) {
        $flashData = var_cleanup($_SESSION['errors']);

        #destroy flash session data, if no longer needed.
        if ($extendLife)
        {
            unset($_SESSION['flashData_'.$title]);
        }
        return $flashData;
    }
}

/**
 * Generates Captcha and saves it to session for validation.
 * @return string
 * @todo possibly set a challenge option to make the math more complex.
 */
function GenerateCaptchaQuestion() {

    #randomize two sets of numbers ranging from 0 to 9.
    $math1 = rand("0", "9");
    $math2 = rand("0", "9");

    //do some basic math.
    $question = "%d + %d = ?";
    $answer = sha1($math1 + $math2);

    //save encrypted answer in session.
    set_flashdata('CAPTCHA_Ans', $answer);

    return sprintf($question, $math1, $math2);
}

/**
 * Cleans the variable of various nastiness.
 * @param string $string the dirty data.
 * @return string
*/
function var_cleanup($string){
    return trim(removeEvilAttributes(htmlentities($string, ENT_QUOTES)));
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

/**
 * generate pagination
 * @param string $actions
 * @return string
*/
function pagination($actions){

	global $num, $txt, $settings, $pg, $count, $count2;

	// Figure out the total number of pages. Always round up using ceil()
	$total_pages = ceil($num / $settings['per_page']);
	$pagination = '<div class="pagination"><p>'.$txt['pages'].'</p><ul>';

    if ($num == 0) {
        $pagination .= '<li class="currentpage"><b>1</b></li>';
    }

	// Build page number
	if($pg > 1){
		$prev = ($pg - 1);
		$pagination .= '<li class="disablepage"><a href="'.$_SERVER['SCRIPT_NAME'].'?'.$actions.'pg='.$prev.'">'.$txt['prev'].'</a></li>';
	}
	//output numbers.
	for($i = 1; $i <= $total_pages; $i++){
		#see if this is the current page.
		if($pg == $i){
			$pagination .= '<li class="currentpage"><b>'.$i.'</b></li>';
		}else{
			#dot out a few page numbers to prevent rows of links. 
			if($pg > 4 && $i > 3 && $i < ($pg - 1) && $i < ($total_pages - 3)){
				$count ++;
				$pagination .= ($count == 1)? "<li>...</li>" : "";
			}elseif($i > ($pg + 2) && $i < ($total_pages - 2)){
				$count2 ++;
				$pagination .= ($count2 == 1)? "<li>...</li>" : "";
			}else{
				$pagination .= '<li><a href="'.$_SERVER['SCRIPT_NAME'].'?'.$actions.'pg='.$i.'">'.$i.'</a></li>';
			}
		}
	}
	// Build Next Link
	if($pg < $total_pages){
		$next = ($pg + 1);
		$pagination .= '<li class="nextpage"><a href="'.$_SERVER['SCRIPT_NAME'].'?'.$actions.'pg='.$next.'">'.$txt['next'].'</a></li>';
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