<?php
if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: groupAdministration.class.php
Last Modified: 11/10/2010

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class groupAdministration{


    /**
	*groupManager
	*
	*where the groups are managed.
	*
	*@modified 7/13/10
	*
	*@access public
	*/
	public function groupManager(){

		global $style, $title, $lang, $db;

	    $db->SQL = "select id, Name, Enrollment, Level from ebb_groups";
		$groupQ = $db->query();

		#grouplist manager header.
        $tpl = new templateEngine($style, "cp-groupsetup_head");
		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[admincp]",
		"LANG-GROUPSETUP" => "$cp[groupsetup]",
		"LANG-DELPROMPT" => "$lang[condel]",
		"LANG-TEXT" => "$lang[grouptxt]",
		"LANG-NEWGROUP" => "$lang[addgroup]",
		"LANG-MANAGEPROFILE" => "$lang[manageprofile]",
		"LANG-GROUPNAME" => "$lang[name]",
		"LANG-GROUPSTATUS" => "$lang[groupstat]",
		"LANG-GROUPACCESS" => "$lang[groupaccess]",
		"LANG-GROUPOPTIONS" => "$lang[groupopts]"));
		echo $tpl->outputHtml();

		while ($groupMgr = mysql_fetch_assoc ($groupQ)){

			#see what the status are for the group.
			if ($groupMgr['Enrollment'] == 1){
				$group_status = $lang['open'];
			}elseif($groupMgr['Enrollment'] == 0){
				$group_status = $lang['closed'];
			}else{
				$group_status = $lang['grouphidden'];
			}

			#grouplist manager data.
            $tpl = new templateEngine($style, "cp-groupsetup");
			$tpl->parseTags(array(
		   "GROUPID" => "$groupMgr[id]",
			"LANG-MODIFY" => "$lang[modify]",
			"LANG-DELETE" => "$lang[del]",
			"GROUPNAME" => "$groupMgr[Name]",
			"GROUPSTATUS" => "$group_status",
			"GROUPACCESSLEVEL" => "$groupMgr[Level]",
			"VIEWMEMBERLIST" => "$lang[viewlist]",
			"LANG-PENDINGLIST" => "$lang[pendinglist]"));
			echo $tpl->outputHtml();
		}

		#grouplist manager footer.
        $tpl = new templateEngine($style, "cp-groupsetup_foot");
  		echo $tpl->outputHtml();
	}

    /**
	*groupUserlistManager
	*
	*where the group users are managed.
	*
	*@modified 5/10/10
	*
	*@access public
	*/
	public function groupUserlistManager(){

		global $title, $lang, $id, $db, $gmt, $timeFormat, $style;

	    $db->SQL = "SELECT Username, gid FROM ebb_group_users WHERE gid='$id' AND Status='Active'";
		$query = $db->query();
		$gnum = $db->affectedRows();

		#Group memberlist header.
		$tpl = new templateEngine($style, "cp-groupmemberlist_head");
		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[admincp]",
		"LANG-GROUPMEMBERLIST" => "$lang[viewlist]",
		"LANG-DELPROMPT" => "$lang[condel]",
		"LANG-TEXT" => "$lang[groupmemberlist]",
		"LANG-GROUPMEMBERS" => "$lang[groupmembers]",
		"LANG-USERNAME" => "$lang[username]",
		"LANG-POSTCOUNT" => "$lang[posts]",
		"LANG-REGISTRATIONDATE" => "$lang[joindate]",
		"LANG-NOMEMBERS" => "$lang[nomembers]"));
			
		#see if no results are found.
		if($gnum > 0){
			$tpl->removeBlock("noresults");
		}
			
		echo $tpl->outputHtml();

		while ($gUserlist = mysql_fetch_assoc ($query)){
			$db->SQL = "SELECT Post_Count, Date_Joined FROM ebb_users WHERE Username='$gUserlist[Username]'";
			$getUserlist = $db->fetchResults();

			#setup date formatting.
			$joinDate = formatTime($timeFormat, $getUserlist['Date_Joined'], $gmt);

			#Group memberlist data.
            $tpl = new templateEngine($style, "cp-groupmemberlist");
			$tpl->parseTags(array(
			"GROUPID" => "$gUserlist[gid]",
			"GROUPMEMBER" => "$gUserlist[Username]",
			"LANG-REMOVEGROUPMEMBER" => "$lang[removefromgroup]",
			"LANG-PMALT" => "$lang[postpmalt]",
			"LANG-POSTCOUNT" => "$lang[posts]",
			"POSTCOUNT" => "$getUserlist[Post_Count]",
			"JOINDATE" => "$joinDate"));
			echo $tpl->outputHtml();
		}
		
		#group memberlist footer.
		$tpl = new templateEngine($style, "cp-groupmemberlist_foot");
		echo $tpl->outputHtml();
	}

    /**
	*groupProfileSelctor
	*
	*fills the selectboc with all group profiles in the DB.
	*
	*@modified 5/10/10
	*
	*@access public
	*/
	public function groupProfileSelctor(){

		global $db, $group_r;

	    $db->SQL = "SELECT id, profile FROM ebb_permission_profile";
		$gprofileQ = $db->query();

		$gProfileSel = '<select name="gprofile" class="text">';

		while ($gProfile = mysql_fetch_assoc ($gprofileQ)){
			#auto select data.
	    	if($group_r['permission_type'] == $gProfile['id']){
				$gProfileSel .= '<option value="'.$gProfile['id'].'" selected=selected>'.$gProfile['profile'].'</option>';
			}else{
				$gProfileSel .= '<option value="'.$gProfile['id'].'">'.$gProfile['profile'].'</option>';
			}
		}
		$gProfileSel .= '</select>';
		return ($gProfileSel);
	}

    /**
	*groupProfileManager
	*
	*The control area for the group profiles.
	*
	*@modified 5/11/10
	*
	*@access public
	*/
	public function groupProfileManager(){

		global $style, $title, $lang, $db;

	    $db->SQL = "select id, profile, access_level from ebb_permission_profile";
		$gProfileQ = $db->query();

		#group profile manager header.
		$tpl = new templateEngine($style, "cp-manageprofile-head");
		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[admincp]",
		"LANG-MANAGEPROFILE" => "$lang[manageprofile]",
		"LANG-DELPROMPT" => "$lang[condel]",
		"LANG-TEXT" => "$lang[profilemanagetxt]",
		"LANG-NEWPROFILE" => "$lang[newprofile]",
		"LANG-ADMINPROFILE" => "$lang[adminprofile]",
		"LANG-MODERATORPROFILE" => "$lang[moderatorprofile]",
		"LANG-MEMBERPROFILE" => "$lang[memberprofile]",
		"LANG-PROFILE" => "$lang[profilename]",
		"LANG-ACCESSLEVEL" => "$lang[accesslevel]"));
		echo $tpl->outputHtml();

        #grouplist profile data.
		while ($grProfile = mysql_fetch_assoc ($gProfileQ)) {
			$tpl = new templateEngine($style, "cp-manageprofile");
			$tpl->parseTags(array(
			"PROFILEID" => "$grProfile[id]",
			"LANG-MODIFY" => "$lang[modify]",
			"LANG-DELETE" => "$lang[del]",
			"PROFILENAME" => "$grProfile[profile]",
			"LANG-ACCESSLEVEL" => "$lang[accesslevel]",
			"ACCESSLEVEL" => "$grProfile[access_level]"));
			echo $tpl->outputHtml();
		}
		#grouplist profile footer.
		$tpl = new templateEngine($style, "cp-manageprofile-foot");
		echo $tpl->outputHtml();
	}

    /**
	*groupProfileNew
	*
	*The form that allows administrator to create a new group profile.
	*
	*@modified 5/11/10
	*
	*@param int $type - 1=admin;2-moderator;3-user.
	*
	*@access public
	*/
	public function groupProfileNew($type){

		global $style, $title, $lang, $db;

		#see if profile type was used.
		if($type == ""){
			$error = new notifySys($lang['noprofiletype'], true, true, __FILE__, __LINE__);
			$error->displayError();
		}

		#sql query.
	    $db->SQL = "SELECT id, permission FROM ebb_permission_actions WHERE type='$type'";
		$permissionQ = $db->query();

		#new profile form header.
		$tpl = new templateEngine($style, "cp-newprofile");
		$tpl->parseTags(array(
		"TYPE" => "$type",
		"ERROR-PROFILE" => "$lang[profileerr]",
		"ERROR-LONGPROFILENAME" => "$lang[longprofilenameerr]",
		"ERROR-ACTIONS" => "$lang[actionerr]",
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[admincp]",
		"LANG-NEWPROFILE" => "$lang[newprofile]",
		"LANG-TEXT" => "$lang[profilemanagetxt]",
		"LANG-PROFILENAME" => "$lang[profilename]",
		"LANG-ACTIONLIST" => "$lang[availableactions]"));
		echo $tpl->outputHtml();

		#new profile form data.
		while ($gPActions = mysql_fetch_assoc ($permissionQ)) {
	 		#get language text from db.
	 		$lowercaseAction = strtolower($gPActions['permission']);

			#output data.
			$tpl = new templateEngine($style, "cp-profileactions");
			$tpl->parseTags(array(
			"LANG-ACTION" => "$lang[$lowercaseAction]",
			"LANG-ACTIONCODE" => "$lowercaseAction",
			"LANG-YES" => "$lang[yes]",
			"LANG-NO" => "$lang[no]"));
			echo $tpl->outputHtml();
		}

		#new profile form footer.
		$tpl = new templateEngine($style, "cp-newprofile-foot");
		$tpl->parseTags(array(
		"LANG-SUBMIT" => "$lang[createprofile]"));
		echo $tpl->outputHtml();
	}

    /**
	*newGroupProfile
	*
	*The form that allows administrator to modify a current group profile.
	*
	*@modified 7/13/10
	*
	*@param int $gpid - group profile ID.
	*@param str $gProfileName - Name of the Group Profile.
	*@param int $type - 1=admin;2-moderator;3-user.
	*
	*@access public
	*/
	public function groupProfileEdit($gpid, $gProfileName, $type){

		global $style, $title, $lang, $db;

		#see if profile type was used.
		if(($type == "") or ($gProfileName == "") or ($gpid == "")){
			$error = new notifySys($lang['noprofiletype'], true, true, __FILE__, __LINE__);
			$error->displayError();
		}

		#new profile form header.
		$tpl = new templateEngine($style, "cp-modifyprofile");
		$tpl->parseTags(array(
		"TYPE" => "$type",
		"ERROR-PROFILE" => "$lang[profileerr]",
		"ERROR-LONGPROFILENAME" => "$lang[longprofilenameerr]",
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[admincp]",
		"LANG-MODIFYPROFILE" => "$lang[modifyprofile]",
		"LANG-TEXT" => "$lang[profilemanagetxt]",
		"LANG-PROFILENAME" => "$lang[profilename]",
		"PROFILENAME" => "$gProfileName",
		"LANG-ACTIONLIST" => "$lang[availableactions]"));
		echo $tpl->outputHtml();

		#new profile form data.
	    $db->SQL = "SELECT id, permission FROM ebb_permission_actions WHERE type='$type'";
		$permissionQ = $db->query();

		while ($gPActions = mysql_fetch_assoc ($permissionQ)) {
			#sql query.
		    $db->SQL = "SELECT set_value FROM ebb_permission_data WHERE profile='$gpid' AND permission='$gPActions[id]'";
			$pdataR = $db->fetchResults();

	 		#get language text from db.
	 		$lowercaseAction = strtolower($gPActions['permission']);

			#grouplist manager header.
			$tpl = new templateEngine($style, "cp-profileactions-edit");
			$tpl->parseTags(array(
			"LANG-ACTION" => "$lang[$lowercaseAction]",
			"LANG-ACTIONCODE" => "$lowercaseAction",
			"LANG-YES" => "$lang[yes]",
			"LANG-NO" => "$lang[no]"));
			
			#see if a value equal 0 or 1.
			if($pdataR['set_value'] == 1){
				$tpl->removeBlock("value0");
			}else{
				$tpl->removeBlock("value1");
			}
			
			echo $tpl->outputHtml();
		}

		#new profile form footer.
		$tpl = new templateEngine($style, "cp-modifyprofile-foot");
		$tpl->parseTags(array(
		"LANG-SUBMIT" => "$lang[modifyprofile]",
		"GID" => "$gpid"));
		echo $tpl->outputHtml();
	}

    /**
	*groupPendingList
	*
	*management of pending group users.
	*
	*@modified 7/13/10
	*
	*@access public
	*/
	public function groupPendingList(){

		global $title, $lang, $members, $id, $db, $gmt, $timeFormat, $style;

		$db->SQL = "SELECT username, gid FROM ebb_group_member_request WHERE gid='$id'";
		$gPendingQ = $db->query();
		$gnum = $db->affectedRows();

		#pendinglist header.
        $tpl = new templateEngine($style, "cp-pendinglist_head");
		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[admincp]",
		"LANG-GROUPPENDINGLIST" => "$lang[pendinglist]",
		"LANG-TEXT" => "$lang[pendinglisttxt]",
		"LANG-PENDINGLIST" => "$lang[pendinglist]",
		"LANG-USERNAME" => "$lang[username]",
		"LANG-POSTCOUNT" => "$lang[posts]",
		"LANG-REGISTRATIONDATE" => "$lang[joindate]",
		"LANG-ADDUSERTOGROUP" => "$lang[addtogroup]",
		"LANG-USERNAME" => "$lang[username]",
		"LANG-NOPENDING" => "$lang[nopending]",
		"LANG-SUBMIT" => "$lang[addusergroup]",
		"ID" => "$id"));

		#see if any pending users exists.
		if($gnum > 0){
			$tpl->removeBlock("noresults");
		}
			
		echo $tpl->outputHtml();

		#pendlinglist data.
		while ($pending = mysql_fetch_assoc ($gPendingQ)){
			#get member's profile data.
			$db->SQL = "SELECT Post_Count, Date_Joined FROM ebb_users WHERE Username='$pending[username]'";
			$pendingUserData = $db->fetchResults();

			#date formatting.
            $joinDate = formatTime($timeFormat, $pendingUserData['Date_Joined'], $gmt);

			#output data.
            $tpl = new templateEngine($style, "cp-pendinglist");
            $tpl->parseTags(array(
			"GROUPID" => "$pending[gid]",
			"USERNAME" => "$pending[username]",
			"LANG-ACCEPTUSER" => "$lang[pendingaccept]",
			"LANG-DENYUSER" => "$lang[pendingdeny]",
			"LANG-PMALT" => "$lang[postpmalt]",
			"LANG-POSTCOUNT" => "$lang[posts]",
			"POSTCOUNT" => "$pendingUserData[Post_Count]",
			"JOINDATE" => "$joinDate"));

			echo $tpl->outputHtml();
		}

		#pendinglist footer.
        $tpl = new templateEngine($style, "cp-pendinglist_foot");
        $tpl->parseTags(array(
		"LANG-ADDUSERTOGROUP" => "$lang[addtogroup]",
		"LANG-USERNAME" => "$lang[username]",
		"LANG-SUBMIT" => "$lang[addusergroup]",
		"ID" => "$id"));
		
		echo $tpl->outputHtml();
	}

    /**
	*groupListSelector
	*
	*lists groups; used at user settings form.
	*
	*@modified 7/13/10
	*
	*@access public
	*/
	public function groupListSelector(){

		global $db, $boardPref;

		$ustat = '<select name="ustat" class="text">';

	    $db->SQL = "SELECT id, Name FROM ebb_groups";
		$groupQ = $db->query();

		while ($groupChk = mysql_fetch_assoc ($groupQ)){
			if(($boardPref->getPreferenceValue("userstat") == $groupChk['id'])){
				$ustat .= '<option value="'.$groupChk['id'].'" selected=selected>'.$groupChk['Name'].'</option>';
			}else{
				$ustat .= '<option value="'.$groupChk['id'].'">'.$groupChk['Name'].'</option>';
			}
		}

		$ustat .= '</select>';
		return ($ustat);
	}

}//END CLASS
?>
