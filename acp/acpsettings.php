<?php
define('IN_EBB', true);
/*
Filename: acpsettings.php
Last Modified: 5/22/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "../config.php";
require "../header.php";
require "../includes/admin_function.php";
if(isset($_GET['section'])){
	$section = var_cleanup($_GET['section']);
}else{
	$section = ''; 
}
#see if user has access to this portion of the script.
$permission_chk = access_vaildator($permission_type, 7);
if($permission_chk == 0){
	die($cp['noaccess']);
}
#get title.
switch($section){
case 'user':
case 'save_user':
	$usercptitle = $cp['settings'].' - '.$cp['usersettings'];
	$helpTitle = $cp['usersettings'];
	$helpBody = $help['usercpbody'];
break;
case 'board':
case 'save_board':
	$usercptitle = $cp['settings'].' - '.$cp['boardsettings'];
	$helpTitle = $cp['boardsettings'];
	$helpBody = $help['boardcpbody'];
break;
case 'mail':
case 'save_mail':
	$usercptitle = $cp['settings'].' - '.$cp['mailsettings'];
	$helpTitle = $cp['mailsettings'];
	$helpBody = $help['mailcpbody'];
break;
case 'cookie':
case 'save_cookies':
	$usercptitle = $cp['settings'].' - '.$cp['cookiesettings'];
	$helpTitle = $cp['cookiesettings'];
	$helpBody = $help['cookiecpbody'];
break;
case 'attachment':
case 'save_attachment':
case 'save_attachmentlist':
	$usercptitle = $cp['settings'].' - '.$cp['attachmentsettings'];
	$helpTitle = $cp['attachmentsettings'];
	$helpBody = $help['attachcpbody'];
break;
default:
	$usercptitle = $cp['settings'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
}
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$usercptitle",
  "LANG-HELP-TITLE" => "$helpTitle",
  "LANG-HELP-BODY" => "$helpBody"));
$page->output();
//check to see if the install file is stil on the user's server.
$setupexist = checkinstall();
if ($setupexist == 1){
	$error = $txt['installadmin'];
	echo acp_error($error, "error");
}
//check to see if the user can access this board.
echo check_ban();
//check to see if this user is able to access this area.
if (($access_level == 2) or ($stat == "Member") or ($stat == "guest") or ($access_level == 3)){
	header("Location: $board_address/index.php");
}

//detect new PMs.
$pm_msg = DetectNewPM($logged_user);

//output top
if ($access_level == 1){
	#output.
	$page = new template("../". $template_path ."/top-acp.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-WELCOME" => "$txt[welcome]",
	"LOGGEDUSER" => "$logged_user",
	"LANG-LOGOUT" => "$txt[logout]",
	"NEWPM" => "$pm_msg",
	"LANG-CP" => "$menu[cp]",
	"LANG-NEWPOSTS" => "$index[newposts]",
	"ADDRESS" => "$address",
	"LANG-HOME" => "$menu[home]",
	"LANG-SEARCH" => "$menu[search]",
	"LANG-CLOSE" => "$txt[closewindow]",
	"LANG-QUICKSEARCH" => "$search[quicksearch]",
	"LANG-ADVANCEDSEARCH" => "$search[advsearch]",
	"LANG-FAQ" => "$menu[faq]",
	"LANG-MEMBERLIST" => "$menu[members]",
	"LANG-GROUPLIST"=> "$menu[groups]",
	"LANG-PROFILE" => "$menu[profile]"));
	$page->output();
	//update user's activity.
	echo update_whosonline_reg($logged_user);
}
#see if user confirm login.
if (isset($_SESSION['ebbacpu']) and (isset($_SESSION['ebbacpp']) and (isset($_SESSION['ebbacp_expire'])))) {

	#see if session expired.
	if ($_SESSION['ebbacp_expire'] <= time()) {
		unset($_SESSION['ebbacp_expire']);
		unset($_SESSION['ebbacpu']);
		unset($_SESSION['ebbacpp']);
	
		#go to login page.
		header("Location: acp_login.php");
	} else {
		#see if cookie value belongs to a user on the roster.
		$chk_user = user_check(var_cleanup($_SESSION['ebbacpu']), var_cleanup($_SESSION['ebbacpp']));
		$admin_check = admin_verify(var_cleanup($_SESSION['ebbacpu']), var_cleanup($_SESSION['ebbacpp']));
		if(($chk_user == 0) or ($admin_check == false)){
			$error = "INVALID COOKIE OR SESSION!";
			echo acp_error($error, "error");
		}
	}
} else {
	#go to login page.
	header("Location: acp_login.php");
}

//display admin CP
switch ( $section ){
	case 'user':
	#call board setting function.
	$colume = 'TOS_Status, TOS_Rules, register_stat, activation, userstat, coppa, Image_Verify, proxy_block, mx_check, warning_threshold, PM_Quota, Archive_Quota';
	$settings = board_settings($colume);
	//tos status detection
	if ($settings['TOS_Status'] == 1){
		$tos_stat = "<input type=\"radio\" name=\"term_stat\" value=\"1\" class=\"text\" id=\"termstat\" checked=checked />$cp[on] <input type=\"radio\" name=\"term_stat\" value=\"0\" class=\"text\" />$cp[off]";
	}else{
		$tos_stat = "<input type=\"radio\" name=\"term_stat\" value=\"1\" class=\"text\" id=\"termstat\" />$cp[on] <input type=\"radio\" name=\"term_stat\" value=\"0\" class=\"text\" checked=checked />$cp[off]";
	}  
	#registration detection.
	if($settings['register_stat'] == 1){
		$reg_stat = "<input type=\"radio\" name=\"reg_stat\" value=\"1\" class=\"text\" checked=checked />$cp[on] <input type=\"radio\" name=\"reg_stat\" value=\"0\" class=\"text\" />$cp[off]";
	}else{
		$reg_stat = "<input type=\"radio\" name=\"reg_stat\" value=\"1\" class=\"text\" />$cp[on] <input type=\"radio\" name=\"reg_stat\" value=\"0\" class=\"text\" checked=checked />$cp[off]";
	}
	//activation detection
	if($settings['activation'] == "User"){
		$activate_status = "<input type=\"radio\" name=\"active_stat\" value=\"None\" class=\"text\" />$cp[none] <input type=\"radio\" name=\"active_stat\" value=\"User\" class=\"text\" checked=checked />$cp[activeusers] <input type=\"radio\" name=\"active_stat\" value=\"Admin\" class=\"text\" />$cp[activeadmin]";
	}elseif($settings['activation'] == "Admin"){
		$activate_status = "<input type=\"radio\" name=\"active_stat\" value=\"None\" class=\"text\" />$cp[none] <input type=\"radio\" name=\"active_stat\" value=\"User\" class=\"text\" />$cp[activeusers] <input type=\"radio\" name=\"active_stat\" value=\"Admin\" class=\"text\" checked=checked />$cp[activeadmin]";
	}else{
		$activate_status = "<input type=\"radio\" name=\"active_stat\" value=\"None\" class=\"text\" checked=checked />$cp[none] <input type=\"radio\" name=\"active_stat\" value=\"User\" class=\"text\" />$cp[activeusers] <input type=\"radio\" name=\"active_stat\" value=\"Admin\" class=\"text\" />$cp[activeadmin]";
	}
	#auto group selectbox.
	$ustat = group_lister();
	//security image detection
	if ($settings['Image_Verify'] == 1){
		$imagevert_status = "<input type=\"radio\" name=\"imagevert_stat\" value=\"1\" class=\"text\" checked=checked />$cp[lowset] <input type=\"radio\" name=\"imagevert_stat\" value=\"2\" class=\"text\" />$cp[highset] <input type=\"radio\" name=\"imagevert_stat\" value=\"0\" class=\"text\" />$cp[off]";
	}elseif($settings['Image_Verify'] == 2){
		$imagevert_status = "<input type=\"radio\" name=\"imagevert_stat\" value=\"1\" class=\"text\" />$cp[lowset] <input type=\"radio\" name=\"imagevert_stat\" value=\"2\" class=\"text\" checked=checked />$cp[highset] <input type=\"radio\" name=\"imagevert_stat\" value=\"0\" class=\"text\" />$cp[off]";
	}else{
		$imagevert_status = "<input type=\"radio\" name=\"imagevert_stat\" value=\"1\" class=\"text\" />$cp[lowset] <input type=\"radio\" name=\"imagevert_stat\" value=\"2\" class=\"text\" />$cp[highset] <input type=\"radio\" name=\"imagevert_stat\" value=\"0\" class=\"text\" checked=checked />$cp[off]";
	}
	#proxy block detect.
	if($settings['proxy_block'] == 0){
		$proxy_stat = "<input type=\"radio\" name=\"proxy_stat\" value=\"1\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"proxy_stat\" value=\"0\" class=\"text\" checked=checked />$txt[no]";
	}else{
		$proxy_stat = "<input type=\"radio\" name=\"proxy_stat\" value=\"1\" class=\"text\" checked=checked />$txt[yes] <input type=\"radio\" name=\"proxy_stat\" value=\"0\" class=\"text\" />$txt[no]";
	}
	#MX Record detect.
	if($settings['mx_check'] == 0){
		$mx_stat = "<input type=\"radio\" name=\"mx_stat\" value=\"1\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"mx_stat\" value=\"0\" class=\"text\" checked=checked />$txt[no]";
	}else{
		$mx_stat = "<input type=\"radio\" name=\"mx_stat\" value=\"1\" class=\"text\" checked=checked />$txt[yes] <input type=\"radio\" name=\"mx_stat\" value=\"0\" class=\"text\" />$txt[no]";
	}	
	#get threshold of warning setting.
	if($settings['warning_threshold'] == 30){
		$warn_threshold = '<option value="30" selected=selected>30</option>';
	}else{
		$warn_threshold = '<option value="30">30</option>';
	}
	if($settings['warning_threshold'] == 40){
		$warn_threshold .= '<option value="40" selected=selected>40</option>';	 
	}else{
		$warn_threshold .= '<option value="40">40</option>';
	}
	if($settings['warning_threshold'] == 50){
		$warn_threshold .= '<option value="50" selected=selected>50</option>';
	}else{
		$warn_threshold .= '<option value="50">50</option>';
	}
	if($settings['warning_threshold'] == 60){
		$warn_threshold .= '<option value="60" selected=selected>60</option>';
	}else{
		$warn_threshold .= '<option value="60">60</option>';
	}
	if($settings['warning_threshold'] == 70){
		$warn_threshold .= '<option value="70" selected=selected>70</option>';
	}else{
		$warn_threshold .= '<option value="70">70</option>';
	}
	if($settings['warning_threshold'] == 80){
		$warn_threshold .= '<option value="80" selected=selected>80</option>';
	}else{
		$warn_threshold .= '<option value="80">80</option>';
	}
	if($settings['warning_threshold'] == 90){
		$warn_threshold .= '<option value="90" selected=selected>90</option>';
	}else{
		$warn_threshold .= '<option value="90">90</option>';
	}
	if($settings['warning_threshold'] == 100){
		$warn_threshold .= '<option value="100" selected=selected>100</option>';
	}else{
		$warn_threshold .= '<option value="100">100</option>';
	}
	#COPPA rule
	if($settings['coppa'] == 0){
		$coppa_sel = "<option value=\"0\" selected=selected>$cp[none]</option>";
	}else{
		$coppa_sel = "<option value=\"0\">$cp[none]</option>";
	}
	if($settings['coppa'] == 13){
		$coppa_sel .= "<option value=\"13\" selected=selected>$cp[al13]</option>";	 
	}else{
		$coppa_sel .= "<option value=\"13\">$cp[al13]</option>";
	}
	if($settings['coppa'] == 16){
		$coppa_sel .= "<option value=\"16\" selected=selected>$cp[al16]</option>";
	}else{
		$coppa_sel .= "<option value=\"16\">$cp[al16]</option>";
	}
	if($settings['coppa'] == 18){
		$coppa_sel .= "<option value=\"18\" selected=selected>$cp[al18]</option>";
	}else{
		$coppa_sel .= "<option value=\"18\">$cp[al18]</option>";
	}
	if($settings['coppa'] == 21){
		$coppa_sel .= "<option value=\"21\" selected=selected>$cp[al21]</option>";
	}else{
		$coppa_sel .= "<option value=\"21\">$cp[al21]</option>";
	}
	#output html.
	$page = new template("../". $template_path ."/cp-usersettings.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-USERSETTINGS" => "$cp[usersettings]",
	"LANG-NOTERM" => "$cp[notos]",
	"LANG-NOPMQUOTA" => "$cp[nopmquota]",
	"LANG-INVALIDPMQUOTA" => "$cp[invalidpmquota]",
	"LANG-TOSSTAT" => "$cp[tosstat]",
	"TOSSTAT" => "$tos_stat",
	"LANG-TOS" => "$cp[tos]",
	"TOS" => "$settings[TOS_Rules]",
	"LANG-REG-STAT" => "$cp[registerstat]",
	"REG-STAT" => "$reg_stat",
	"LANG-ACTIVE-TYPE" => "$cp[activation]",
	"ACTIVE-TYPE" => "$activate_status",
	"LANG-USERSTAT" => "$cp[autogroupsel]",
	"USERSTAT" => "$ustat",
	"LANG-COPPAVALIDATION" => "$cp[copparule]",
	"COPPAVALIDATION" => "$coppa_sel",
	"LANG-IMGVERT" => "$cp[securityimage]",
	"LANG-GDREQ" => "$cp[gdreq]",
	"IMGVERT" => "$imagevert_status",
	"LANG-PROXY-STAT" => "$cp[blockproxy]",
	"LANG-PROXY-HINT" => "$cp[proxytxt]",
	"PROXY-STAT" => "$proxy_stat",
	"LANG-MXCHECK" => "$cp[mxcheck]",
	"LANG-MXCHECKHINT" => "$cp[mxcheckhint]",
	"MXCHECK" => "$mx_stat",
	"LANG-WARNINGTHRESHOLD" => "$cp[warnthreshold]",
	"LANG-WARNINGTHRESHOLD-HINT" => "$cp[warnthresholdhint]",
	"WARNINGTHRESHOLD" => "$warn_threshold",
	"LANG-PMQUOTA" => "$cp[pmquota]",
	"PMQUOTA" => "$settings[PM_Quota]",
	"LANG-ARCHIVEQUOTA" => "$pm[archivequota]",
	"ARCHIVEQUOTA" => "$settings[Archive_Quota]",
	"LANG-SAVESETTINGS" => "$cp[savesettings]"));
	$page->output();
	break;
	case 'save_user':
	#get form values.
	$term_stat = var_cleanup($_POST['term_stat']);
	$term_msg = var_cleanup($_POST['term_msg']);
	$coppa = var_cleanup($_POST['coppa']);
	$ustat = var_cleanup($_POST['ustat']);
	$imagevert_stat = var_cleanup($_POST['imagevert_stat']);
	$proxy_stat = var_cleanup($_POST['proxy_stat']);
	$mx_stat = var_cleanup($_POST['mx_stat']);
	$warn_threshold = var_cleanup($_POST['warnthreshold']);
	$reg_stat = var_cleanup($_POST['reg_stat']);
	$active_stat = var_cleanup($_POST['active_stat']);
	$pm_quota = var_cleanup($_POST['pm_quota']);
	$archive_quota = var_cleanup($_POST['archive_quota']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if($term_stat == ""){
		$errormsg = $cp['noterm']."\n\n";
		$error = 1; 
	}
	if (($term_stat == 1) AND (empty($term_msg))){
		$errormsg .= $cp['notos']."\n\n";
		$error = 1;
	}
	if ($imagevert_stat == ""){
		$errormsg .= $cp['noimgagevert']."\n\n";
		$error = 1;
	}
	if($proxy_stat == ""){
		$errormsg .= $cp['noproxy']."\n\n";
		$error = 1;
	}
	if($mx_stat == ""){
		$errormsg .= $cp['nomxcheck']."\n\n";
		$error = 1;
	}
	if($reg_stat == ""){
		$errormsg .= $cp['noreg']."\n\n";
		$error = 1;
	}
	if($active_stat == ""){
		$errormsg .= $cp['noactivation']."\n\n"; 
		$error = 1;
	}
	if($ustat == ""){
		$errormsg .= $cp['noustat']."\n\n";
		$error = 1;
	}
	if($coppa == ""){
		$errormsg .= $cp['nocoppa']."\n\n";
		$error = 1;	
	}
	if($warn_threshold == ""){
		$errormsg .= $cp['nowarnthreshold']."\n\n"; 
		$error = 1;
	}
	if((!is_numeric($warn_threshold)) or ($warn_threshold < 30) or ($warn_threshold > 100)){
		$errormsg .= $cp['invalidwarnthreshold']."\n\n"; 
		$error = 1;
	}
	if (empty($pm_quota)){
		$errormsg .= $cp['nopmquota']."\n\n";
		$error = 1;
	}
	if(strlen($pm_quota) > 4){
		$errormsg .= $cp['longpmquota']."\n\n";
		$error = 1;
	}
	if (empty($archive_quota)){
		$errormsg .= $cp['nopmquota']."\n\n";
		$error = 1;
	}
	if(strlen($archive_quota) > 4){
		$errormsg .= $cp['longpmquota']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "UPDATE ebb_settings SET TOS_Status='$term_stat', TOS_Rules='$term_msg', PM_Quota='$pm_quota', Archive_Quota='$archive_quota', register_stat='$reg_stat', activation='$active_stat', userstat='$ustat', coppa='$coppa', Image_Verify='$imagevert_stat', proxy_block='$proxy_stat', mx_check='$mx_stat', warning_threshold='$warn_threshold'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified User Settings", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: acpsettings.php?section=user"); 
	}
	break;
	case 'board':
	$colume = 'Default_Zone, Default_Style, per_page, Default_Language, Board_Status, Announcement_Status, Announcements, GZIP, spell_checker, Site_Title, Board_Address, Site_Address, Off_Message, Board_Email, Default_Time';
	$settings = board_settings($colume);
	#load functions.
	$timezone = timezone_select($settings['Default_Zone']);
	$style_select = style_select($settings['Default_Style']);
	$lang = acp_lang_select($settings['Default_Language']);
	//board status detection
	if($settings['Board_Status'] == 1){
		$board_status = "<input type=\"radio\" name=\"board_stat\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"board_stat\" value=\"0\" />$cp[off]";
	}else{
		$board_status = "<input type=\"radio\" name=\"board_stat\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"board_stat\" value=\"0\" checked=checked />$cp[off]";
	}
	//announcement status detection
	if($settings['Announcement_Status'] == 1){
		$announce_status = "<input type=\"radio\" name=\"announce_stat\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"announce_stat\" value=\"0\" />$cp[off]";
	}else{
		$announce_status = "<input type=\"radio\" name=\"announce_stat\" value=\"1\"  />$cp[on] <input type=\"radio\" name=\"announce_stat\" value=\"0\" checked=checked />$cp[off]";
	}
	//gzip status detection
	if ($settings['GZIP'] == 1){
		$gzip_status = "<input type=\"radio\" name=\"gzip_stat\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"gzip_stat\" value=\"0\" />$cp[off]";
	}else{
		$gzip_status = "<input type=\"radio\" name=\"gzip_stat\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"gzip_stat\" value=\"0\" checked=checked />$cp[off]";
	}
	//spell checker detection
	if ($settings['spell_checker'] == 1){
		$spell_status = "<input type=\"radio\" name=\"spell_stat\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"spell_stat\" value=\"0\" />$cp[off]";
	}else{
		$spell_status = "<input type=\"radio\" name=\"spell_stat\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"spell_stat\" value=\"0\" checked=checked />$cp[off]";
	}
	$page = new template("../". $template_path ."/cp-boardsettings.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-BOARDSETTINGS" => "$cp[boardsettings]",
	"LANG-NOBNAME" => "$cp[noboardname]",
	"LANG-LONGBNAME" => "$cp[longboardname]",
	"LANG-INVALIDBADDR" => "$cp[invalidboardaddr]",
	"LANG-LONGBADDR" => "$cp[longboardaddress]",
	"LANG-INVALIDSITEADDR" => "$cp[invalidsiteaddr]",
	"LANG-LONGSITEADDR" => "$cp[longsiteaddress]",
	"LANG-NOBMSG" => "$cp[noclosemsg]",
	"LANG-INVALIDEMAIL" => "$reg[invalidemail]",
	"LANG-LONGBEMAIL" => "$cp[longboardemail]",
	"LANG-NOAMSG" => "$cp[noannounce]",
	"LANG-NOTIMEFORM" => "$cp[notimeformat]",
	"LANG-LONGTIMEFORM" => "$cp[longtimeformat]",
	"LANG-BOARDNAME" => "$cp[boardname]",
	"LANG-NOPERPG" => "$cp[noperpg]",
	"LANG-INVALIDPERPG" => "$cp[invalidperpg]",
	"BOARDNAME" => "$settings[Site_Title]",
	"LANG-SITEADDRESS" => "$cp[sitelink]",
	"LANG-SITEADDESSTXT" => "$cp[sitelink_txt]",
	"SITEADDRESS" => "$settings[Site_Address]",
	"LANG-PERPAGE" => "$cp[perpg]",
	"LANG-PERPAGEHINT" => "$cp[perpghint]",
	"PERPAGE" => "$settings[per_page]",
	"LANG-BOARDSTATUS" => "$index[boardstatus]",
	"BOARDSTATUS" => "$board_status",
	"LANG-OFFMSG" => "$cp[boardoffmsg]",
	"OFFMSG" => "$settings[Off_Message]",
	"LANG-BOARDADDRESS" => "$cp[boardlink]",
	"LANG-BOARDADDRESSTXT" => "$cp[boardlink_txt]",
	"BOARDADDRESS" => "$settings[Board_Address]",
	"LANG-BOARDEMAIL" => "$cp[boardemail]",
	"BOARDEMAIL" => "$settings[Board_Email]",
	"LANG-ANNOUNCEMENTSTATUS" => "$cp[announcestat]",
	"ANNOUNCEMENTSTATUS" => "$announce_status",
	"LANG-ANNOUNCEMENT" => "$cp[announce]",
	"ANNOUNCEMENT" => "$settings[Announcements]",
	"LANG-ANNOUNCERULE" => "$cp[onelineannounce]",
	"LANG-DEFAULTSTYLE" => "$cp[defaultstyle]",
	"DEFAULTSTYLE" => "$style_select",
	"LANG-DEFAULTLANGUAGE" => "$cp[defaultlang]",
	"DEFAULTLANGUAGE" => "$lang",
	"LANG-GZIP" => "$cp[gzip]",
	"LANG-GZIPREQ" => "$cp[gzipreq]",
	"GZIP" => "$gzip_status",
	"LANG-SPELLCHECKER" => "$cp[spellchecker]",
	"LANG-PSPELL" => "$cp[pspell]",
	"SPELLCHECKER" => "$spell_status",
	"LANG-TIMEZONE" => "$cp[defaulttimezone]",
	"TIMEZONE" => "$timezone",
	"LANG-TIMEFORMAT" => "$cp[defaultimtformat]",
	"LANG-TIMERULE" => "$cp[timeformat]",
	"TIMEFORMAT" => "$settings[Default_Time]",
	"LANG-SAVESETTINGS" => "$cp[savesettings]"));
	$page->output();
	break;
	case 'save_board':
	#get form values.
	$board_name = var_cleanup($_POST['board_name']);
	$site_address = var_cleanup($_POST['site_address']);
	$perpg = var_cleanup($_POST['perpg']);
	$board_stat = var_cleanup($_POST['board_stat']);
	$off_msg = var_cleanup($_POST['off_msg']);
	$board_address = var_cleanup($_POST['board_address']);
	$board_email = var_cleanup($_POST['board_email']);
	$announce_stat = var_cleanup($_POST['announce_stat']);
	$announce_msg = var_cleanup($_POST['announce_msg']);
	$dstyle = var_cleanup($_POST['style']);
	$default_lang = var_cleanup($_POST['default_lang']);
	$gzip_stat = var_cleanup($_POST['gzip_stat']);
	$spell_stat = var_cleanup($_POST['spell_stat']);
	$default_zone = var_cleanup($_POST['time_zone']);
	$default_time = var_cleanup($_POST['default_time']);
	$emailvalidate = valid_email($board_email);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if (empty($board_name)){
		$errormsg = $cp['noboardname']."\n\n";
		$error = 1;
	}
	if (empty($site_address)){
		$errormsg .= $cp['nositeaddress']."\n\n";
		$error = 1;
	}
	if (!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $site_address)) {
		$errormsg .= $cp['invalidsiteaddr']."\n\n";
		$error = 1;
	}
	if(empty($perpg)){
		$errormsg .= $cp['noperpg']."\n\n";
		$error = 1;
	}
	if(!is_numeric($perpg)){
		$errormsg .= $cp['invalidperpg']."\n\n";
		$error = 1;
	}
	if(strlen($perpg) > 3){
		$errormsg .= $cp['longperpg']."\n\n";
		$error = 1;
	}
	if (($board_stat == 0) AND (empty($off_msg))){
		$errormsg .= $cp['noclosemsg']."\n\n";
		$error = 1;
	}
	if (empty($board_address)){
		$errormsg .= $cp['noboardaddress']."\n\n";
		$error = 1;
	}
	if (!preg_match('/http:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/', $board_address)) {
		$errormsg .= $cp['invalidboardaddr']."\n\n";
		$error = 1;
	}
	if (empty($board_email)){
		$errormsg .= $cp['noemail']."\n\n";
		$error = 1;
	}
	if($emailvalidate == 1){
		$errormsg .= $reg['invalidemail']."\n\n";
		$error = 1;
	}
	if (($announce_stat == 1) AND (empty($announce_msg))){
		$errormsg .= $cp['noannounce']."\n\n";
		$error = 1;
	}
	if (empty($dstyle)){
		$errormsg .= $cp['nostyle']."\n\n";
		$error = 1;
	}
	if (empty($default_lang)){
		$errormsg .= $cp['nolang']."\n\n";
		$error = 1;
	}
	if ($gzip_stat == ""){
		$errormsg .= $cp['nogzip']."\n\n";
		$error = 1;
	}
	if ($spell_stat == ""){
		$errormsg .= $cp['nospellchecker']."\n\n";
		$error = 1;
	}
	if ($default_zone == ""){
		$errormsg .= $cp['notimezone']."\n\n";
		$error = 1;
	}
	if (empty($default_time)){
		$errormsg .= $cp['notimeformat']."\n\n";
		$error = 1;
	}
	if(strlen($board_name) > 50){
		$errormsg .= $cp['longboardname']."\n\n";
		$error = 1;
	}
	if(strlen($site_address) > 255){
		$errormsg .= $cp['longsiteaddress']."\n\n";
		$error = 1;
	}
	if(strlen($board_address) > 255){
		$errormsg .= $cp['longboardaddress']."\n\n";
		$error = 1;
	}
	if(strlen($board_email) > 255){
		$errormsg .= $cp['longboardemail']."\n\n";
		$error = 1;
	}
	if(strlen($off_msg) > 255){
		$errormsg .= $cp['longoffmsg']."\n\n";
		$error = 1;
	}
	if(strlen($announce_msg) > 255){
		$errormsg .= $cp['longannouce']."\n\n";
		$error = 1;
	}
	if(strlen($default_time) > 14){
		$errormsg .= $cp['longtimeformat']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "UPDATE ebb_settings SET Site_Title='$board_name', Site_Address='$site_address', per_page='$perpg', Board_Status='$board_stat', Board_Address='$board_address', Board_Email='$board_email', Off_Message='$off_msg', Announcement_Status='$announce_stat', Announcements='$announce_msg', Default_Style='$dstyle', Default_Language='$default_lang', GZIP='$gzip_stat', spell_checker='$spell_stat', Default_Zone='$default_zone', Default_Time='$default_time'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified Board Settings", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: acpsettings.php?section=board");
	}
	break;
	case 'mail':
	$colume = 'mail_type, smtp_server, smtp_user, smtp_pass, smtp_port';
	$settings = board_settings($colume);
	//mail type detection.
	if($settings['mail_type'] == 0){
		$mail_status = "<input type=\"radio\" name=\"mail_type\" value=\"0\" class=\"text\" id=\"smtpstat\" checked=checked />$cp[mailsmtp] <input type=\"radio\" name=\"mail_type\" value=\"1\" class=\"text\" />$cp[mailreg]";
	}else{
		$mail_status = "<input type=\"radio\" name=\"mail_type\" value=\"0\" class=\"text\" id=\"smtpstat\" />$cp[mailsmtp] <input type=\"radio\" name=\"mail_type\" value=\"1\" class=\"text\" checked=checked />$cp[mailreg]";
	}
	$page = new template("../". $template_path ."/cp-mailsettings.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-MAILSETTINGS" => "$cp[mailsettings]",
	"LANG-NOSMTPHOST" => "$cp[nosmtphost]",
	"LANG-LONGSMTPHOST" => "$cp[longsmtphost]",
	"LANG-NOSMTPPORT" => "$cp[nosmtpport]",
	"LANG-NOSMTPUSER" => "$cp[nosmtpuser]",
	"LANG-LONGSMTPUSER" => "$reg[longusername]",
	"LANG-NOSMTPPWD" => "$cp[nosmtppass]",
	"LANG-LONGSMTPPWD" => "$reg[longpassword]",
	"LANG-MAILTYPE" => "$cp[mailtype]",
	"MAILTYPE" => "$mail_status",
	"LANG-HOST" => "$cp[smtphost]",
	"HOST" => "$settings[smtp_server]",
	"LANG-PORT" => "$cp[smtpport]",
	"PORT" => "$settings[smtp_port]",
	"LANG-USERNAME" => "$cp[smtpuser]",
	"USERNAME" => "$settings[smtp_user]",
	"LANG-PASSWORD" => "$cp[smtppass]",
	"PASSWORD" => "$settings[smtp_pass]",
	"LANG-SAVESETTINGS" => "$cp[savesettings]"));
	$page->output();
	break;
	case 'save_mail':
	#get form values.
	$mail_type = var_cleanup($_POST['mail_type']);
	$smtp_host = var_cleanup($_POST['smtp_host']);
	$smtp_port = var_cleanup($_POST['smtp_port']);
	$smtp_user = var_cleanup($_POST['smtp_user']);
	$smtp_pass = var_cleanup($_POST['smtp_pass']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if($mail_type == ""){
		$errormsg .= $cp['nomailrule']."\n\n";
		$error = 1; 
	}
	if($mail_type == 0){
		if(empty($smtp_host)){
			$errormsg = $cp['nosmtphost']."\n\n";
			$error = 1;
		}
		if(empty($smtp_port)){
			$errormsg .= $cp['nosmtpport']."\n\n";
			$error = 1;
		}
		if(empty($smtp_user)){
			$errormsg .= $cp['nosmtpuser']."\n\n";
			$error = 1;
		}
		if(empty($smtp_pass)){
			$errormsg .= $cp['nosmtppass']."\n\n";
			$error = 1;
		}
		if(strlen($smtp_host) > 255){
			$errormsg .= $cp['longsmtphost']."\n\n";
			$error = 1;
		}
		if(strlen($smtp_port) > 4){
			$errormsg .= $cp['longsmtpport']."\n\n";
			$error = 1;
		}
		if(strlen($smtp_user) > 255){
			$errormsg .= $reg['longusername']."\n\n";
			$error = 1;
		}
		if(strlen($smtp_pass) > 255){
			$errormsg .= $cp['longsmtppass']."\n\n";
			$error = 1;
		}
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "UPDATE ebb_settings SET mail_type='$mail_type', smtp_server='$smtp_host', smtp_port='$smtp_port', smtp_user='$smtp_user', smtp_pass='$smtp_pass'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified Mail Settings", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: acpsettings.php?section=mail");
	}
	break;
	case 'cookie':
	$colume = 'cookie_secure, cookie_path, cookie_domain';
	$settings = board_settings($colume);
	//ssl detection status
	if ($settings['cookie_secure'] == 1){
		$secure_status = "<input type=\"radio\" name=\"secure_stat\" value=\"1\" class=\"text\" checked=checked />$cp[enable] <input type=\"radio\" name=\"secure_stat\" value=\"0\" class=\"text\" />$cp[disable]";
	}else{
		$secure_status = "<input type=\"radio\" name=\"secure_stat\" value=\"1\" class=\"text\" />$cp[enable] <input type=\"radio\" name=\"secure_stat\" value=\"0\" class=\"text\" checked=checked />$cp[disable]";
	}
	$page = new template("../". $template_path ."/cp-cookiesettings.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-COOKIESETTINGS" => "$cp[cookiesettings]",
	"LANG-NOCOOKIEDOMAIN" => "$cp[nocookiedomain]",
	"LANG-LONGCOOKIEDOMAIN" => "$cp[longcookiedomain]",
	"LANG-NOCOOKIEPATH" => "$cp[nocookiepath]",
	"LANG-LONGCOOKIEPATH" => "$cp[longcookiepath]",
	"LANG-COOKIEDOMAIN" => "$cp[cookiedomain]",
	"LANG-COOKIEDOMAINTXT" => "$cp[cookiedomain_txt]",
	"COOKIEDOMAIN" => "$settings[cookie_domain]",
	"LANG-COOKIEPATH" => "$cp[cookiepath]",
	"LANG-COOKIEPATHTXT" => "$cp[cookiepath_txt]",
	"COOKIEPATH" => "$settings[cookie_path]",
	"LANG-COOKIESECURE" => "$cp[cookiesecure]",
	"LANG-SSL" => "$cp[ssl]",
	"COOKIESECURE" => "$secure_status",
	"LANG-SAVESETTINGS" => "$cp[savesettings]"));
	$page->output();
	break;
	case 'save_cookies':
	#get form values.
	$cookie_domain = var_cleanup($_POST['cookie_domain']);
	$cookie_path = var_cleanup($_POST['cookie_path']);
	$secure_stat = var_cleanup($_POST['secure_stat']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if(empty($cookie_domain)){
		$errormsg = $cp['nocookiedomain']."\n\n";
		$error = 1;
	}
	if (empty($cookie_path)){
		$errormsg .= $cp['nocookiepath']."\n\n";
		$error = 1;
	}
	if ($secure_stat == ""){
		$errormsg .= $cp['nocookiesecure']."\n\n";
		$error = 1;
	}
	if(strlen($cookie_domain) > 255){
		$errormsg .= $cp['longcookiedomain']."\n\n";
		$error = 1;
	}
	if(strlen($cookie_path) > 255){
		$errormsg .= $cp['longcookiepath']."\n\n";
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "UPDATE ebb_settings SET cookie_domain='$cookie_domain', cookie_path='$cookie_path', cookie_secure='$secure_stat'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified Cookie Settings", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: acpsettings.php?section=cookie");
	}
	break;
	case 'attachment':
	$colume = 'attachment_quota, download_attachments';
	$settings = board_settings($colume);
	$attachment_list = attachment_whitelist();
	//guest download status
	if ($settings['download_attachments'] == 1){
		$download_status = "<input type=\"radio\" name=\"download_stat\" value=\"1\" class=\"text\" checked=checked />$txt[yes] <input type=\"radio\" name=\"download_stat\" value=\"0\" class=\"text\" />$txt[no]";
	}else{
		$download_status = "<input type=\"radio\" name=\"download_stat\" value=\"1\" class=\"text\" />$txt[yes] <input type=\"radio\" name=\"download_stat\" value=\"0\" class=\"text\" checked=checked />$txt[no]";
	}
	$page = new template("../". $template_path ."/cp-attachmentsettings.htm");
	$page->replace_tags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$cp[title]",
	"LANG-ATTACHMENTSETTINGS" => "$cp[attachmentsettings]",
	"LANG-NOMAXUPLOAD" => "$cp[noattachquota]",
	"LANG-LONGMAXUPLOAD"=> "$cp[attachquotahigh]",
	"LANG-NOMAXUPLOAD" => "$cp[invalidattach]",
	"LANG-NOENTENSION" => "$cp[noext]",
	"LANG-LONGEXTENSION" => "$cp[longext]",
	"LANG-ATTACHMENTQUOTA" => "$cp[attachmentquota]",
	"LANG-ATTACHMENTQUOTATXT" => "$cp[attachmentquotahint]",
	"ATTACHMENTQUOTA" => "$settings[attachment_quota]",
	"LANG-DOWNLOADRULE" => "$cp[guestdownload]",
	"DOWNLOADRULE" => "$download_status",
	"LANG-SAVESETTINGS" => "$cp[savesettings]",
	"LANG-ATTACHMENTWHITELIST" => "$cp[attachmentwhitelist]",
	"LANG-ATTACHMENTWHITELISTHINT" => "$cp[extensionhint]",
	"LANG-ATTACHMENTWHITELISTTXT" => "$cp[attachmentwhitelisthint]",
	"LANG-ADDEXTENSION" => "$cp[addextension]",
	"LANG-REMOVEATTACHMENTWHITELIST" => "$cp[removeattachwhitelist]",
	"LANG-REMOVEATTACHMENTWHITELISTHINT" => "$cp[removeattachwhitelisthint]",
	"EXTENSIONLIST" => "$attachment_list",
	"LANG-REMOVEEXTENSION" => "$cp[removeextension]"));
	$page->output();
	break;
	case 'save_attachment':
	#get form values.
	$attach_quota = var_cleanup($_POST['attach_quota']);
	$download_stat = var_cleanup($_POST['download_stat']);
	#set error values to default.
	$error = 0;
	$errormsg = '';
	#error check.
	if(empty($attach_quota)){
		$errrormsg = $cp['noattachquota']."\n\n";
		$error = 1;
	}
	if($download_stat == ""){
		$errrormsg .= $cp['nodwnloadrule']."\n\n";
		$error = 1;
	}
	if($attach_quota > 102400000){
		$errrormsg .= $cp['attachquotahigh']."\n\n";
		$error = 1; 
	}
	if(!is_numeric($attach_quota)){
		$errormsg .= $cp['invalidattach']."\n\n"; 
		$error = 1;
	}
	#see if any errors occured and if so report it.
	if ($error == 1){
		$error = nl2br($errormsg);
		echo acp_error($error, "validate");
	}else{
		//process query
		$db->run = "UPDATE ebb_settings SET attachment_quota='$attach_quota', download_attachments='$download_stat'";
		$db->query();
		$db->close();
		#log action in database.
		$acp_date = time();
		$ip = $_SERVER['REMOTE_ADDR'];
		echo acp_log_add("Modified Attachment Settings", "$logged_user", "$acp_date", "$ip");
		//bring user back
		header("Location: acpsettings.php?section=attachment");
	}
	break;
	case 'save_attachmentlist';
	#see if cmd is called.
	if(!isset($_POST['cmd'])){
		$error = $cp['nocmdid'];
		echo acp_error($error, "error"); 
	}else{
		$cmd = var_cleanup($_POST['cmd']); 
	}
	#see how to proces the data.
	if($cmd == "addext"){
		#get form values.
		$add_ext = var_cleanup($_POST['add_ext']); 
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if(empty($add_ext)){
			$errormsg = $cp['noext'];
			$error = 1; 
		}
		if(strlen($add_ext) > 100){
			$errormsg = $cp['longext'];
			$error = 1; 
		}
		#check for exact matches.
		$db->run = "select ext from ebb_attachment_extlist where ext='$add_ext'";
		$ext_match = $db->num_results();
		$db->close();
		if($ext_match == 1){
			$errormsg = $cp['extexist'];
			$error = 1; 
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//process query
			$db->run = "insert into ebb_attachment_extlist (ext) values('$add_ext')";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Added $add_ext to Attachment Whitelist", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: acpsettings.php?section=attachment");
		}
	}elseif($cmd == "removeext"){
		$attachsel = var_cleanup($_POST['attachsel']); 
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#error check.
		if($attachsel == ""){
			$errormsg = $cp['noextselected'];
			$error = 1; 
		}
		$db->run = "select ext from ebb_attachment_extlist";
		$ext_ct = $db->num_results();
		$db->close();
		if($ext_ct <= 5){
			$errormsg = $cp['extlow'];
			$error = 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//process query
			$db->run = "delete from ebb_attachment_extlist where id='$attachsel'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Deleted an extension from Attachment Whitelist", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: acpsettings.php?section=attachment");
		}
	}else{
		$error = $cp['invalidopt'];
		echo acp_error($error, "error"); 
	}
	break;
	default:
	#go to main menu.
	header("Location: index.php");
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
