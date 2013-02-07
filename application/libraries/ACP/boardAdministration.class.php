<?php
if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: boardAdministration.class.php
Last Modified: 11/10/2010

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class boardAdministration{

    /**
	*boardManager
	*
	*controls the layout of the board, all boards are managed through this.
	*
	*@modified 5/26/10
	*
	*@param string $type - category, board, or sub-board.
	*@param string $bid - parent Board ID.
	*
	*@access public
	*/
	public function boardManager($type, $bid){

		global $style, $title, $lang, $db, $boardAddr;

		#check to see if type equals nothing.
		if(($type == "") and ($bid == "")){
			$bType = 1;
			$parentID = '';
		}else{
			$bType = $type;
			$parentID = $bid;
		}

		#perform loop.
		$db->SQL = "SELECT id, Board FROM ebb_boards WHERE type='$bType' AND Category='$parentID' ORDER BY B_Order";
		$parentQ = $db->query();

		#board manager header.
        $tpl = new templateEngine($style, "cp-boards_head");
		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[admincp]",
		"LANG-BOARDS" => "$lang[boardsetup]",
		"LANG-TEXT" => "$c$langp[boardtext]",
		"LANG-ADDBOARD" => "$lang[addnew]",
		"LANG-NEWCATEGORY" => "$lang[newparentboard]",
		"LANG-NEWBOARD" => "$lang[newboard]",
		"LANG-SUBBOARD" => "$lang[newsubboard]",
		"LANG-GOUP" => "$lang[goup1]"));
		echo $tpl->outputHtml();

		while ($child = mysql_fetch_assoc ($parentQ)) {
			#see if board has any sub-boards linking to them.
			$db->SQL = "SELECT id FROM ebb_boards where Category='$child[id]'";
			$parentCount = $db->affectedRows();

			if($parentCount > 0){
				if($bType == 1){
					$boardName = "<a href=\"boardcp.php?type=2&amp;bid=$child[id]\">$child[Board]</a>";
				}elseif ($bType == 2){
					$boardName = "<a href=\"boardcp.php?type=3&amp;bid=$child[id]\">$child[Board]</a>";
				}else{
					$boardName = "<a href=\"boardcp.php?type=3&amp;bid=$child[id]\">$child[Board]</a>";
				}
			}else{
				$boardName = $child['Board'];
			}

			#board manager data.
            $tpl = new templateEngine($style, "cp-boards");
			$tpl->parseTags(array(
			"LANG-DELPROMPT" => "$lang[condel]",
			"LANG-DELPROMPT2" => "$lang[catdelwarning]",
			"BOARDDIR" => "$boardAddr",
			"BOARDNAME" => "$boardName",
			"BOARDID" => "$child[id]",
			"BOARDTYPE" => "$bType",
			"LANG-MODIFY" => "$lang[modifyboard]",
			"LANG-DELETE" => "$lang[delboard]",
			"CATEGORYID" => "$parentID",
			"LANG-MOVEUP" => "$lang[moveup]",
			"LANG-MOVEDOWN" => "$lang[movedown]"));
			echo $tpl->outputHtml();
		}
		#board manager header.
        $tpl = new templateEngine($style, "cp-boards_foot");
		echo $tpl->outputHtml();
	}
	
    /**
	*parentBoardSelection
	*
	*a list of parent boards.
	*
	*@param string $type - is this board a main parent or child parent.
	*@param int $boardID - board ID, if not parent.
	*
	*@modified 6/1/10
	*
	*@access public
	*/
	public function parentBoardSelection($type){

		global $lang, $boardData, $db, $id;

		#see what type of board to look for.
		if($type == "parent"){
			$db->SQL = "SELECT id, Board FROM ebb_boards WHERE type='1'";
			$parentSelection = $db->query();
		}else{
            $db->SQL = "SELECT id, Board FROM ebb_boards WHERE type='2' OR type='3' AND id!='$id'";
			$parentSelection = $db->query();
		}

		#start data collecting.
		$boardlist = '<select name="catsel" class="text">
		<option value="">'.$lang['selparent'].'</option>';
		while ($parent = mysql_fetch_assoc ($parentSelection)){
			#see if anything needs to be selected.
			if ($parent['id'] == $boardData['Category']){
				$selected = "selected=selected";
			}else{
				$selected = '';
			}
			$boardlist .= '<option value="'.$parent['id'].'" '.$selected.'>'.$parent['Board'].'</option>';
		}
		$boardlist .= '</select>';
		return ($boardlist);
	}
	
    /**
	*readAccessSelection
	*
	*board-based read permission list.
	*
	*@modified 5/8/10
	*
	*@access public
	*/
	public function readAccessSelection(){

		global $permission, $lang;

		if ($permission['B_Read'] == 5){
			$readStatus = '<select name="readaccess" class="text">
			<option value="5" selected=selected>'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="0">'.$lang['access_all'].'</option>
			</select>';
		}
		if ($permission['B_Read'] == 1){
			$readStatus = '<select name="readaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1" selected=selected>'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="0">'.$lang['access_all'].'</option>
			</select>';
		}
		if ($permission['B_Read'] == 2){
			$readStatus = '<select name="readaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2" selected=selected>'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="0">'.$lang['access_all'].'</option>
			</select>';
		}
		if ($permission['B_Read'] == 3){
			$readStatus = '<select name="readaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3" selected=selected>'.$lang['access_users'].'</option>
			<option value="0">'.$lang['access_all'].'</option>
			</select>';
		}
		if ($permission['B_Read'] == 0){
			$readStatus = '<select name="readaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="0" selected=selected>'.$lang['access_all'].'</option>
			</select>';
		}
		return ($readStatus);
	}
	
    /**
	*writeAccessSelection
	*
	*board-based write permission list.
	*
	*@modified 5/8/10
	*
	*@access public
	*/
	public function writeAccessSelection(){

		global $permission, $lang;

	    if ($permission['B_Post'] == 5){
			$writeStatus = '<select name="writeaccess" class="text">
			<option value="5" selected=selected>'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Post'] == 1){
			$writeStatus = '<select name="writeaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1" selected=selected>'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Post'] == 2){
			$writeStatus = '<select name="writeaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2" selected=selected>'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Post'] == 3){
			$writeStatus = '<select name="writeaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3" selected=selected>'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Post'] == 4){
			$writeStatus = '<select name="writeaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4" selected=selected>'.$lang['access_none'].'</option>
			</select>';
		}
		return ($writeStatus);
	}
	
    /**
	*replyAccessSelection
	*
	*board-based reply permission list.
	*
	*@modified 5/8/10
	*
	*@access public
	*/
	public function replyAccessSelection(){

		global $permission, $lang;

		if ($permission['B_Reply'] == 5){
			$replyStatus = '<select name="replyaccess" class="text">
			<option value="5" selected=selected>'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Reply'] == 1){
			$replyStatus = '<select name="replyaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1" selected=selected>'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Reply'] == 2){
			$replyStatus = '<select name="replyaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2" selected=selected>'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Reply'] == 3){
			$replyStatus = '<select name="replyaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3" selected=selected>'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Reply'] == 4){
			$replyStatus = '<select name="replyaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4" selected=selected>'.$lang['access_none'].'</option>
			</select>';
		}
		return ($replyStatus);
	}

    /**
	*voteAccessSelection
	*
	*board-based vote permission list.
	*
	*@modified 5/8/10
	*
	*@access public
	*/
	public function voteAccessSelection(){

		global $permission, $lang;

		if ($permission['B_Vote'] == 5){
			$voteStatus = '<select name="voteaccess" class="text">
			<option value="5" selected=selected>'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Vote'] == 1){
			$voteStatus = '<select name="voteaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1" selected=selected>'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Vote'] == 2){
			$voteStatus = '<select name="voteaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2" selected=selected>'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Vote'] == 3){
			$voteStatus = '<select name="voteaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3" selected=selected>'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Vote'] == 4){
			$voteStatus = '<select name="voteaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4" selected=selected>'.$lang['access_none'].'</option>
			</select>';
		}
		return ($voteStatus);
	}

    /**
	*pollAccessSelection
	*
	*board-based poll permission list.
	*
	*@modified 5/8/10
	*
	*@access public
	*/
	public function pollAccessSelection(){

		global $permission, $lang;

		if ($permission['B_Poll'] == 5){
			$pollStatus = '<select name="pollaccess" class="text">
			<option value="5" selected=selected>'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Poll'] == 1){
			$pollStatus = '<select name="pollaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1" selected=selected>'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Poll'] == 2){
			$pollStatus = '<select name="pollaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2" selected=selected>'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
			if ($permission['B_Poll'] == 3){
			$pollStatus = '<select name="pollaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3" selected=selected>'.$lang['access_users'].'</option>
			<option value="4">'.$lang['access_none'].'</option>
			</select>';
		}
		if ($permission['B_Poll'] == 4){
			$pollStatus = '<select name="pollaccess" class="text">
			<option value="5">'.$lang['access_private'].'</option>
			<option value="1">'.$lang['access_admin'].'</option>
			<option value="2">'.$lang['access_admin_mod'].'</option>
			<option value="3">'.$lang['access_users'].'</option>
			<option value="4" selected=selected>'.$lang['access_none'].'</option>
			</select>';
		}
		return ($pollStatus);
	}
}//END class
?>
