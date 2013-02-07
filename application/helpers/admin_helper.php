<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * admin_helper.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 01/21/2013
*/

/**
 * See if there is a newer copy of Elite Bulletin Board.
 * @return string JSON string
*/
function versionCheck() {
	#obtain codeigniter object.
	$ci =& get_instance();
	
    $connectionTimeout = 3;
    $url = 'http://elite-board.sourceforge.net/ebb_update.php';
	
	// try to get it by @file_get_contents first
	// if we can't due to server settings, we'll
	// use cURL instead.
    if (@ini_get('allow_url_fopen') == 0 || strtolower(@ini_get('allow_url_fopen')) == 'off') {
        if (function_exists('curl_init')) {
			// Configuring curl options
			$options = array(
			  CURLOPT_HEADER => false,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			  CURLOPT_TIMEOUT => $connectionTimeout
			  );
			
            $ch = curl_init($url); // initialize connection to server
			curl_setopt_array($ch, $options); // Setup curl options
			$data =  curl_exec($ch); // Getting jSON result string
            curl_close($ch); // Close connection to server
        } else {
			log_message('error', 'cURL library & http:// wrapper is disabled in the server configuration.'); //log error in error log.
            return array(
			  'status' => 'error',
			  'msg' => $ci->lang->line('updateerr'),
			  'notes' => NULL,
			  'patch_link' => NULL,
			  'severity' => NULL
            );
        }
    } else {
		//@TODO turn on allow_url_fopen to test file_get_contents() method of connecting to update server.
		$context = stream_context_create(array(
		  'http' => array(
			'timeout' => $connectionTimeout),
			'header' => "Content-type: application/json"
			)
		 );
		$data = @file_get_contents($url, null, $context);
		if ($data === false) {
			log_message('error', 'failure to connect to update server.'); //log error in error log.
			return array(
			  'status' => 'error',
			  'msg' => $ci->lang->line('updateerr'),
			  'notes' => NULL,
			  'patch_link' => NULL,
			  'severity' => NULL
            );
		}
		
	}

    if (empty($data)) {
		log_message('error', 'Server returned an empty result set, possible failure to connect to update server.'); //log error in error log.
		return array(
		  'status' => 'error',
		  'msg' => $ci->lang->line('updateerr'),
		  'notes' => NULL,
		  'patch_link' => NULL,
		  'severity' => NULL
		);
    }

	//convert data from json to php object
    $versionData = json_decode($data);
	$latestVersion = $versionData->version_major.'.'.$versionData->version_minor.'.'.$versionData->version_patch.' '.$versionData->version_build;
	
	/*
	 {
		"status": "success",
		"version_major": "3",
		"version_minor": "0",
		"version_patch": "0",
		"version_build": "RC2",
		"version_notes": null,
		"version_releasedate": "01\/30\/2013",
		"version_upgradelink": null,
		"version_severity": null
	  }
	 */
	
	#check major release number.
	if ($versionData->version_major < $ci->config->item("version_major")) {
		$versionStatus = false;
	} else {
		#check minor release number.
		if ($versionData->version_minor < $ci->config->item("version_minor")) {
			$versionStatus = false;
		} else {
			#check patch release.
			if ($versionData->version_patch < $ci->config->item("version_patch")) {
				$versionStatus = false;
			} else {
				#check build.
				if ($versionData->version_build != $ci->config->item("version_build")) {
					$versionStatus = false;
				} else {
					$versionStatus = true;
				} #END BUILD CHECK.
			} #END PATCH CHECK.
		} #END MINOR CHECK.
	} #END MAJOR CHECK.
	
	if (!$versionStatus) {
		return array(
		  'status' => 'success',
		  'msg' => sprintf($ci->lang->line('update_available'), $latestVersion, $versionData->version_releasedate),
		  'notes' => $versionData->version_notes,
		  'patch_link' => $versionData->version_upgradelink,
		  'severity' => $versionData->version_severity
		);
	} else {
		return array(
		  'status' => 'success',
		  'msg' => $ci->lang->line('update_notavailable'),
		  'notes' => NULL,
		  'patch_link' => NULL,
		  'severity' => NULL
		);
	}
}


/**
	 * List the current announcements in the db..
	 * @version 6/25/2011
 */
function ListAnnouncements(){
	global $style, $lang, $db;

	$db->SQL = "SELECT id, information FROM ebb_information_ticker";
	$announceQ = $db->query();

	while ($announceLst = mysql_fetch_assoc($announceQ)){
		#smile listing data.
		$tpl = new templateEngine($style, "cp-announcements");
		$tpl->parseTags(array(
		"ID" => "$announceLst[id]",
		"LANG-DELETE" => "$lang[del]",
		"ANNOUNCEMENT" => "$announceLst[information]"));
		echo $tpl->outputHtml();
	}
}


/**
*attachment_whitelist
*
*Obtains attachment extension whitelist.
*
*@modified 6/26/10
*
*/
function attachment_whitelist(){

	global $db;

	#create query.
	$db->SQL = "SELECT id, ext FROM ebb_attachment_extlist";
	$attach_q = $db->query();

	#start output.
	$attachment_list = '<select name="attachsel" class="text">';
	while ($whtLst = mysql_fetch_assoc($attach_q)){
		$attachment_list .= '<option value="'.$whtLst['id'].'">'.$whtLst['ext'].'</option>';
	}
	$attachment_list .= '</select>';
	return ($attachment_list);
}

/**
*admin_smilelisting
*
*Obtains smiles list.
*
*@modified 7/13/10
*
*/
function admin_smilelisting(){

	global $style, $title, $lang, $db;
	
	$db->SQL = "SELECT id, img_name, code FROM ebb_smiles";
	$smileQ = $db->query();

	#smile listing header.
	$tpl = new templateEngine($style, "cp-smiles_head");
	$tpl->parseTags(array(
  	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[admincp]",
	"LANG-SMILES" => "$lang[smiles]",
	"LANG-DELPROMPT" => "$lang[condel]",
	"LANG-ADDSMILES" => "$lang[addsmiles]",
	"LANG-SMILE" => "$lang[smiletbl]",
	"LANG-CODE" => "$lang[codetbl]",
	"LANG-FILENAME" => "$lang[filename]"));
	echo $tpl->outputHtml();

	while ($smilesLst = mysql_fetch_assoc($smileQ)){
		#smile listing data.
		$tpl = new templateEngine($style, "cp-smiles");
		$tpl->parseTags(array(
		"SMILEID" => "$smilesLst[id]",
		"LANG-MODIFY" => "$lang[modify]",
		"LANG-DELETE" => "$lang[del]",
		"SMILEFILENAME" => "$smilesLst[img_name]",
		"SMILECODE" => "$smilesLst[code]"));
		echo $tpl->outputHtml();
	}

	#show list of smiles installers.
	$installer = new EBBInstaller();
	$smiles = $installer->acpSmileInstaller();
	
	#smile listing footer.
	$tpl = new templateEngine($style, "cp-smiles_foot");
	$tpl->parseTags(array(
	"LANG-SMILEINSTALL" => "$lang[smileinstall]",
	"SMILEINSTALL" => "$smiles"));
	echo $tpl->outputHtml();
}

/**
*warn_log
*
*Obtains detailed list of warned users.
*
*@modified 6/26/10
*
*/
function warn_log(){

	global $db, $title, $lang, $warn_log_q, $style, $boardAddr;

	#warn log header.
	$tpl = new templateEngine($style, "cp-warnlog_head");
	$tpl->parseTags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[admincp]",
	"LANG-WARNLOG" => "$lang[warninglist]",
	"BOARDDIR" => "$boardAddr",
	"LANG-CLEARPROMPT" => "$lang[deletewarnlogtxt]",
	"LANG-TEXT" => "$lang[warnlogtxt]",
	"LANG-DELETE" => "$lang[deletewarnlog]",
	"LANG-PROFORMEDBY" => "$lang[warnperformed]",
	"LANG-PROFORMEDTO" => "$lang[warneffecteduser]",
	"LANG-ACTION" => "$lang[warnaction]",
	"LANG-REASON" => "$lang[warnreason]"));
	echo $tpl->outputHtml();

	while($warnLst = mysql_fetch_assoc($warn_log_q)){
		#get message based on action id.
		if($warnLst['Action'] == 1){
			$action = $lang['actionraise'];
		}elseif($warnLst['Action'] == 2){
			$action = $lang['actionlowered'];
		}elseif($warnLst['Action'] == 3){
			$action = $lang['actionbanned'];
		}elseif($warnLst['Action'] == 4){
			$action = $lang['actionsuspend'];
		}else{
			$action = $lang['actionblank'];
		}
		#warn log data.
		$tpl = new templateEngine($style, "cp-warnlog");
		$tpl->parseTags(array(
		"BOARDDIR" => "$boardAddr",
		"LANG-DELCONFIRM" => "$lang[revoketext]",
		"ID" => "$warnLst[id]",
		"LANG-REVOKE" => "$lang[revokeaction]",
		"PERFORMEDBY" => "$warnLst[Authorized]",
		"PERFORMEDTO" => "$warnLst[Username]",
		"ACTION" => "$action",
		"REASON" => "$warnLst[Message]"));
		echo $tpl->outputHtml();
	}

	#warn log footer.
	$tpl = new templateEngine($style, "cp-warnlog_foot");
	echo $tpl->outputHtml();
}

/**
*admin_censorlist
*
*Obtains list of censored words or banned words.
*
*@modified 7/13/10
*
*/
function admin_censorlist(){

	global $style, $title, $lang, $db, $boardAddr;

    $db->SQL = "SELECT id, Original_Word, action FROM ebb_censor";
	$censorQ = $db->query();
	$censorCt = $db->affectedRows();

	#censorlist header.
	$tpl = new templateEngine($style, "cp-censorlist_head");
	$tpl->parseTags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[admincp]",
	"LANG-CENSORLIST" => "$lang[censor]",
	"LANG-CENSORACTION" => "$lang[censoraction]",
	"LANG-ORIGINALWORD" => "$lang[originalword]",
	"LANG-NOCENSOR" => "$lang[nocensor]",
	"LANG-LONGCENSOR" => "$lang[longcensor]",
	"LANG-NOCENSORACTION" => "$lang[nocensoraction]",
	"LANG-EMPTYCENSOR" => "$lang[emptycensorlist]"));

	#do some decision making.
	if($censorCt > 0){
		$tpl->removeBlock("noResults");
	}
	echo $tpl->outputHtml();

    if($censorCt > 0){
		while ($censorLst = mysql_fetch_assoc($censorQ)){
			#output action.
			if($censorLst['action'] == 1){
				$censor_action = $lang['censorban'];
			}else{
				$censor_action = $lang['censorspam'];
			}

			#censorlist data.
			$tpl = new templateEngine($style, "cp-censorlist");
			$tpl->parseTags(array(
			"BOARDDIR" => "$boardAddr",
			"LANG-DELPROMPT" => "$lang[condel]",
			"CENSORID" => "$censorLst[id]",
			"LANG-DELETE" => "$lang[del]",
			"ORGINALWORD" => "$censorLst[Original_Word]",
			"DESIREDACTION" => "$censor_action"));

			echo $tpl->outputHtml();
		}
	}

	#censorlist footer.
	$tpl = new templateEngine($style, "cp-censorlist_foot");
	$tpl->parseTags(array(
	"LANG-CENSORLIST" => "$lang[censor]",
	"LANG-ADDCENSOR" => "$lang[addcensor]",
	"LANG-CENSORACTION" => "$lang[censoraction]",
	"LANG-CENSORACTIONHINT" => "$lang[censoractionhint]",
	"LANG-CENSORBAN" => "$lang[censorban]",
	"LANG-CENSORSPAM" => "$lang[censorspam]",
	"LANG-SUBMIT" => "$lang[submit]"));

	echo $tpl->outputHtml();
}

/**
*inactive_users
*
*Obtains list of inactive users.
*
*@modified 2/18/11
*
*/
function inactive_users(){

	global $style, $lang, $title, $inactive_q, $user_ct, $timeFormat, $gmt;
	
	#inactive userlist header.
	$tpl = new templateEngine($style, "cp-activateusers_head");
	$tpl->parseTags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[admincp]",
	"LANG-ACTIVATEUSER" => "$lang[activateacct]",
	"LANG-STYLENAME" => "$lang[stylename]",
	"LANG-USERNAME" => "$lang[username]",
	"LANG-JOINDATE" => "$lang[joindate]",
	"LANG-NOINACTIVEUSER" => "$lang[noinactiveusers]"));

	#do some decision making.
	if($user_ct > 0){
		$tpl->removeBlock("noResults");
	}
	echo $tpl->outputHtml();

	#display ianctive userlist(if anything exists.
	if($user_ct > 0){
		while($iusrLst = mysql_fetch_assoc($inactive_q)){
        	#date formatting.
			$joinDate = formatTime($timeFormat, $iusrLst['Date_Joined'], $gmt);

			#inactive userlist data.
			$tpl = new templateEngine($style, "cp-activateusers");
			$tpl->parseTags(array(
			"USERID" => "$iusrLst[id]",
			"LANG-ACCEPTUSER" => "$lang[pendingaccept]",
			"LANG-DENYUSER" => "$lang[pendingdeny]",
			"USERNAME" => "$iusrLst[Username]",
			"JOINDATE" => "$joinDate"));

			echo $tpl->outputHtml();
		}
	}

	#inactive userlist footer.
	$tpl = new templateEngine($style, "cp-activateusers_foot");
	echo $tpl->outputHtml();
}

/**
*admin_stylelisting
*
*Obtains list of installed styles.
*
*@modified 7/5/10
*
*/
function admin_stylelisting(){

	global $style, $title, $lang, $db, $boardAddr;

	$db->SQL = "SELECT id, Name FROM ebb_style";
	$stylesQ = $db->query();

	#style listing header.
	$tpl = new templateEngine($style, "cp-style_head");
	$tpl->parseTags(array(
	"TITLE" => "$title",
	"LANG-TITLE" => "$lang[admincp]",
	"LANG-STYLES" => "$lang[managestyle]",
	"LANG-STYLENAME" => "$lang[stylename]"));

	echo $tpl->outputHtml();

	#get list of isntalled styles.
	while ($styles = mysql_fetch_assoc($stylesQ)){
		#style listing data.
		$tpl = new templateEngine($style, "cp-style");
		$tpl->parseTags(array(
		"BOARDDIR" => "$boardAddr",
		"LANG-DELPROMPT" => "$lang[confrmuninstall]",
		"STYLEID" => "$styles[id]",
		"LANG-UNINSTALL" => "$lang[styleuninstaller]",
		"STYLENAME" => "$styles[Name]"));

		echo $tpl->outputHtml();
	}
	#style listing footer.
	$tpl = new templateEngine($style, "cp-style_foot");

	echo $tpl->outputHtml();
}

/**
*admin_banlist_ip
*
*Obtains list of banned IPs.
*
*@modified 6/26/10
*
*/
function admin_banlist_ip(){

	global $lang, $db;

   	$db->SQL = "SELECT id, ban_item FROM ebb_banlist WHERE ban_type='IP'";
	$ipQ = $db->query();
	$ipCt = $db->affectedRows();


	$admin_banlist_ip = '<select name="ipsel" class="text">';

    #see if anything exists.
	if ($ipCt == 0){
		$admin_banlist_ip .= '<option value="">'.$lang['nobanlistip'].'</option>';
	}else{
		while ($ipLst = mysql_fetch_assoc($ipQ)){
			$admin_banlist_ip .= '<option value="'.$ipLst['id'].'">'.$ipLst['ban_item'].'</option>';
		}
	}
	$admin_banlist_ip .= '</select>';
	return ($admin_banlist_ip);
}

/**
*admin_banlist_email
*
*Obtains list of banned Emails.
*
*@modified 6/26/10
*
*/
function admin_banlist_email(){

	global $lang, $db;

   	$db->SQL = "SELECT id, ban_item FROM ebb_banlist WHERE ban_type='Email'";
	$query = $db->query();
	$emlCt = $db->affectedRows();


	$admin_banlist_email = '<select name="emailsel" class="text">';

    #see if anything exists.
	if ($emlCt == 0){
		$admin_banlist_email .= '<option value="">'.$lang['nobanlistemail'].'</option>';
	}else{
		while ($emlLst = mysql_fetch_assoc($query)){
			$admin_banlist_email .= '<option value="'.$emlLst['id'].'">'.$emlLst['ban_item'].'</option>';
		}
	}
	$admin_banlist_email .= '</select>';
	return ($admin_banlist_email);
}

/**
*admin_blacklist
*
*Obtains list of blacklisted usernames.
*
*@modified 6/26/10
*
*/
function admin_blacklist(){

	global $lang, $db;

   	$db->SQL = "SELECT id, blacklisted_username FROM ebb_blacklist";
	$blkUsrQ = $db->query();
	$blkUsrCt = $db->affectedRows();

    
	$username_blacklist = "<select name=\"blkusersel\" class=\"text\">";

	#see if anything exists.
	if ($blkUsrCt == 0){
		$username_blacklist .= '<option value="">'.$lang['noblacklistednames'].'</option>';
	}else{
		while ($blkUsrLst = mysql_fetch_assoc($blkUsrQ)){
			$username_blacklist .= '<option value="'.$blkUsrLst['id'].'">'.$blkUsrLst['blacklisted_username'].'</option>';
		}
	}
	$username_blacklist .= "</select>";
	return ($username_blacklist);
}
