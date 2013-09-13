<?php
define('IN_EBB', true);
/*
Filename: boardcp.php
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
if(isset($_GET['action'])){
	$action = var_cleanup($_GET['action']);
}else{
	$action = ''; 
}
#get title.
switch($action){
case 'board_order':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 1);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$boardcp = $cp['boardmenu'].' - '.$cp['boardsetup'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'board_add':
case 'board_add_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 1);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$boardcp = $cp['boardmenu'].' - '.$cp['addnew'];
	$helpTitle = $help['addboardtitle'];
	$helpBody = $help['addboardbody'];
break;
case 'board_modify':
case 'board_modify_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 1);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$boardcp = $cp['boardmenu'].' - '.$cp['modifyboard'];
	$helpTitle = $help['addboardtitle'];
	$helpBody = $help['addboardbody'];
break;
case 'board_delete':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 1);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$boardcp = $cp['boardmenu'].' - '.$cp['delboard'];
	$helpTitle = $help['nohelptitle'];
	$helpBody = $help['nohelpbody'];
break;
case 'prune':
case 'prune_process':
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 2);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$boardcp = $cp['boardmenu'].' - '.$cp['prune'];
	$helpTitle = $help['pruneboardtitle'];
	$helpBody = $help['pruneboardbody'];
break;
default:
	#see if user has access to this portion of the script.
	$permission_chk = access_vaildator($permission_type, 1);
	if($permission_chk == 0){
		die($cp['noaccess']);
	}
	$boardcp = $cp['boardmenu'].' - '.$cp['boardsetup'];
	$helpTitle = $help['boardmanagetitle'];
	$helpBody = $help['boardmanagebody'];
}
$page = new template("../". $template_path ."/acp_header.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "PAGETITLE" => "$boardcp",
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
switch( $action ){
	case 'board_order':
		#see if a board id was defined
		if(isset($_GET['id'])){
			$id = var_cleanup($_GET['id']);		 
		}else{
			$error = $txt['nobid'];
			echo acp_error($error, "error");		
		}
		#see if board type was defined.
		if(isset($_GET['type'])){
			$type = var_cleanup($_GET['type']);
		}else{
			$error = $cp['noboardtype'];
			echo acp_error($error, "error");		
		}
		$o = var_cleanup($_GET['o']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		if ($type == 1){
			$cat = 0; 
		}else{
			$cat = var_cleanup($_GET['cat']);
		}
		$db->run = "SELECT B_Order, Board FROM ebb_boards where id='$id' and type='$type'";
		$order_r = $db->result();
		$db->close();
		if ($o == "up"){
			#error check.
			if ($order_r['B_Order'] == 1){
				$errormsg = $cp['ontop']."\n\n";
				$error = 1;
			}
			#see if any errors occured and if so report it.
			if($error == 1){
				$error = nl2br($errormsg);
				echo acp_error($error, "validate"); 
			}else{
				$neworder = $order_r['B_Order'] - 1;
				//move the old order number.
				$move_up = $neworder + 1;
				//perform query.
				$db->run = "UPDATE ebb_boards SET B_Order='$move_up' WHERE B_Order='$neworder' and Category='$cat' and type='$type' and id!='$id'";
				$db->query();
				$db->close();
				//process the query.
				$db->run = "UPDATE ebb_boards SET B_Order='$neworder' WHERE id='$id'";
				$db->query();
				$db->close();
				#log action in database.
				$acp_date = time();
				$ip = $_SERVER['REMOTE_ADDR'];
				echo acp_log_add("Repositioned $order_r[Board]", "$logged_user", "$acp_date", "$ip");
				//bring user back to board section
				header("Location: boardcp.php"); 
			}
		}
		#set board down a number.
		if ($o == "down"){
			$db->run = "SELECT id FROM ebb_boards where Category='$cat'";
			$ct = $db->num_results();
			$db->close();
			//see if user is trying to go lower than they can.
			if($order_r['B_Order'] == $ct){
				$errormsg = $cp['onbottom']."\n\n";
				$error = 1;
			}
			#see if any errors occured and if so report it.
			if($error == 1){
				$error = nl2br($errormsg);
				echo acp_error($error, "validate"); 
			}else{
				$neworder = $order_r['B_Order'] + 1;
				//move the old order number.
				$move_dwn = $neworder - 1;
				//perform query.
				$db->run = "UPDATE ebb_boards SET B_Order='$move_dwn' WHERE B_Order='$neworder' and Category='$cat' and type='$type' and id!='$id'";
				$db->query();
				$db->close();
				//process the query.
				$db->run = "UPDATE ebb_boards SET B_Order='$neworder' WHERE id='$id'";
				$db->query();
				$db->close();
				#log action in database.
				$acp_date = time();
				$ip = $_SERVER['REMOTE_ADDR'];
				echo acp_log_add("Repositioned $order_r[Board]", "$logged_user", "$acp_date", "$ip");
				//bring user back to board section
				header("Location: boardcp.php"); 
			}
		}
	break;
	case 'board_add':
		if(isset($_GET['type'])){
			$type = var_cleanup($_GET['type']);
		}else{
			$error = $cp['noboardtype']; 
			echo acp_error($error, "error");
		}
		if ($type == 1){
			#output html.
			$page = new template("../". $template_path ."/cp-newcategory.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$cp[title]",
			"LANG-ADDCATEGORY" => "$cp[addnewcategory]",
			"LANG-NOCAT" => "$cp[addcaterror]",
			"LANG-LONGCAT" => "$cp[longcatname]",
			"LANG-TEXT" => "$cp[addcattxt]",
			"LANG-CATEGORYNAME" => "$cp[catname]",
			"LANG-BOARDPERMISSION" => "$cp[boardpermissions]",
			"LANG-READACCESS" => "$cp[boardread]",
			"LANG-ACCESS-PRIVATE" => "$cp[access_private]",
			"LANG-ACCESS-ADMINS" => "$cp[access_admin]",
			"LANG-ACCESS-ADMINSMODS" => "$cp[access_admin_mod]",
			"LANG-ACCESS-ALL" => "$cp[access_all]",
			"LANG-ACCESS-REG" => "$cp[access_users]",
			"LANG-SUBMIT" => "$cp[addcat]"));
			$page->output(); 
		}elseif($type == 2){
			#output html.
			$cat_select = acpcategory_select();
			$page = new template("../". $template_path ."/cp-newboard.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$cp[title]",
			"LANG-ADDBOARD" => "$cp[addnewboard]",
			"LANG-NOBOARD" => "$cp[boardnameerror]",
			"LANG-LONGBOARD" => "$cp[longboardname]",
			"LANG-NODESCRIPTION" => "$cp[descriptionerror]",
			"LANG-NOCATSEL" => "$cp[categoryerror]",
			"LANG-NOPOSTINCRED" => "$cp[incrementerror]",
			"LANG-NOBBCODE" => "$cp[bbcodeerror]",
			"LANG-NOSMILE" => "$cp[smileserror]",
			"LANG-NOIMG" => "$cp[imgerror]",
			"LANG-TEXT" => "$cp[addboardtxt]",
			"BOARDTYPE" => "$type",
			"LANG-BOARDNAME" => "$cp[boardname]",
			"LANG-DESCRIPTION" => "$cp[description]",
			"LANG-BOARDPERMISSION" => "$cp[boardpermissions]",
			"LANG-BOARDSETTINGS" => "$cp[boardsettings]",
			"LANG-ON" => "$cp[on]",
			"LANG-OFF" => "$cp[off]",
			"LANG-READACCESS" => "$cp[boardread]",
			"LANG-WRITEACCESS" => "$cp[boardwrite]",
			"LANG-REPLYACCESS" => "$cp[boardreply]",
			"LANG-VOTEACCESS" => "$cp[boardvote]",
			"LANG-POLLACCESS" => "$cp[boardpoll]",
			"LANG-EDITACCESS" => "$cp[boardedit]",
			"LANG-DELETEACCESS" => "$cp[boarddelete]",
			"LANG-ATACHMENTACCESS" => "$cp[boardattachments]",
			"LANG-IMPORTANTACCESS" => "$cp[boardimportant]",
			"LANG-ACCESS-PRIVATE" => "$cp[access_private]",
			"LANG-ACCESS-ADMINS" => "$cp[access_admin]",
			"LANG-ACCESS-ADMINSMODS" => "$cp[access_admin_mod]",
			"LANG-ACCESS-ALL" => "$cp[access_all]",
			"LANG-ACCESS-NONE" => "$cp[access_none]",
			"LANG-ACCESS-REG" => "$cp[access_users]",
			"LANG-CATEGORY" => "$cp[newcategory]",
			"CATEGORY" => "$cat_select",
			"LANG-POSTINCREMENT" => "$cp[postincrement]",
			"LANG-YES" => "$txt[yes]",
			"LANG-NO" => "$txt[no]",
			"LANG-BBCODE" => "$cp[bbcode]",
			"LANG-SMILE" => "$post[smiles]",
			"LANG-IMG" => "$cp[img]",
			"LANG-SUBMIT" => "$cp[addboard]"));
			$page->output(); 
		}else{
			#output html.
			$cat_select = acpboard_select();
			$page = new template("../". $template_path ."/cp-newboard.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$cp[title]",
			"LANG-ADDBOARD" => "$cp[addnewsubboard]",
			"LANG-NOBOARD" => "$cp[boardnameerror]",
			"LANG-LONGBOARD" => "$cp[longboardname]",
			"LANG-NODESCRIPTION" => "$cp[descriptionerror]",
			"LANG-NOCATSEL" => "$cp[categoryerror]",
			"LANG-NOPOSTINCRED" => "$cp[incrementerror]",
			"LANG-NOBBCODE" => "$cp[bbcodeerror]",
			"LANG-NOSMILE" => "$cp[smileserror]",
			"LANG-NOIMG" => "$cp[imgerror]",
			"LANG-TEXT" => "$cp[addboardtxt]",
			"BOARDTYPE" => "$type",
			"LANG-BOARDNAME" => "$cp[boardname]",
			"LANG-DESCRIPTION" => "$cp[description]",
			"LANG-BOARDPERMISSION" => "$cp[boardpermissions]",
			"LANG-BOARDSETTINGS" => "$cp[boardsettings]",
			"LANG-ON" => "$cp[on]",
			"LANG-OFF" => "$cp[off]",
			"LANG-READACCESS" => "$cp[boardread]",
			"LANG-WRITEACCESS" => "$cp[boardwrite]",
			"LANG-REPLYACCESS" => "$cp[boardreply]",
			"LANG-VOTEACCESS" => "$cp[boardvote]",
			"LANG-POLLACCESS" => "$cp[boardpoll]",
			"LANG-EDITACCESS" => "$cp[boardedit]",
			"LANG-DELETEACCESS" => "$cp[boarddelete]",
			"LANG-ATACHMENTACCESS" => "$cp[boardattachments]",
			"LANG-IMPORTANTACCESS" => "$cp[boardimportant]",
			"LANG-ACCESS-PRIVATE" => "$cp[access_private]",
			"LANG-ACCESS-ADMINS" => "$cp[access_admin]",
			"LANG-ACCESS-ADMINSMODS" => "$cp[access_admin_mod]",
			"LANG-ACCESS-ALL" => "$cp[access_all]",
			"LANG-ACCESS-NONE" => "$cp[access_none]",
			"LANG-ACCESS-REG" => "$cp[access_users]",
			"LANG-CATEGORY" => "$cp[newcategory]",
			"CATEGORY" => "$cat_select",
			"LANG-POSTINCREMENT" => "$cp[postincrement]",
			"LANG-YES" => "$txt[yes]",
			"LANG-NO" => "$txt[no]",
			"LANG-BBCODE" => "$cp[bbcode]",
			"LANG-SMILE" => "$post[smiles]",
			"LANG-IMG" => "$cp[img]",
			"LANG-SUBMIT" => "$cp[addboard]"));
			$page->output(); 
		}
	break;
	case 'board_add_process':
		if(isset($_GET['type'])){
			$type = var_cleanup($_GET['type']);
		}else{
			$error = $cp['noboardtype']; 
			echo acp_error($error, "error");
		}
		#set error values to default.
		$error = 0;
		$errormsg = '';
		#process based on board type.
		if ($type == 1){
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
			#error check.
			if(empty($board_name)){
				$errormsg = $cp['addcaterror']."\n\n";
				$error = 1;
			}
			if(strlen($board_name) > 50){
				$errormsg .= $cp['longcatname']."\n\n";
				$error = 1;
			}
			if ($readaccess == ""){
				$errormsg .= $cp['noreadsetting']."\n\n";
				$error = 1;
			}
			//get board order
			$db->run = "SELECT id FROM ebb_boards where type='1'";
			$ct = $db->num_results();
			$db->close();
			$board_order = $ct + 1; 
		}elseif($type == 2){
			$board_name = var_cleanup($_POST['board_name']);
			$description = var_cleanup($_POST['description']);
			$readaccess = var_cleanup($_POST['readaccess']);
			$writeaccess = var_cleanup($_POST['writeaccess']);
			$replyaccess = var_cleanup($_POST['replyaccess']);
			$voteaccess = var_cleanup($_POST['voteaccess']);
			$pollaccess = var_cleanup($_POST['pollaccess']);
			$editaccess = var_cleanup($_POST['editaccess']);
			$deleteaccess = var_cleanup($_POST['deleteaccess']);
			$attachmentaccess = var_cleanup($_POST['attachmentaccess']);
			$importantaccess = var_cleanup($_POST['importantaccess']);
			$catsel = var_cleanup($_POST['catsel']);
			$increment = var_cleanup($_POST['increment']);
			$bbcode = var_cleanup($_POST['bbcode']);
			$smiles = var_cleanup($_POST['smiles']);
			$img = var_cleanup($_POST['img']);
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
				$errormsg .= $cp['noattachmentsetting']."\n\n";
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
			//get board order
			$db->run = "SELECT id FROM ebb_boards where type='2' and Category='$catsel'";
			$ct = $db->num_results();
			$db->close();
			$board_order = $ct + 1;
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
			$attachmentaccess = var_cleanup($_POST['attachmentaccess']);
			$importantaccess = var_cleanup($_POST['importantaccess']);
			$catsel = var_cleanup($_POST['catsel']);
			$increment = var_cleanup($_POST['increment']);
			$bbcode = var_cleanup($_POST['bbcode']);
			$smiles = var_cleanup($_POST['smiles']);
			$img = var_cleanup($_POST['img']); 
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
				$errormsg .= $cp['noattachmentsetting']."\n\n";
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
			//get board order
			$db->run = "SELECT id FROM ebb_boards where type='3' and Category='$catsel'";
			$ct = $db->num_results();
			$db->close();
			$board_order = $ct + 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
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
			$db->run = "insert into ebb_board_access (B_Read, B_Post, B_Reply, B_Vote, B_Poll, B_Delete, B_Edit, B_Important, B_Attachment, B_id) values ('$readaccess', '$writeaccess', '$replyaccess', '$voteaccess', '$pollaccess', '$deleteaccess', '$editaccess', '$importantaccess', '$attachmentaccess', '$r_id[id]')";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Created New Board: $board_name", "$logged_user", "$acp_date", "$ip");
			//bring the user to the board section
			header("Location: boardcp.php");
		}
	break;
	case 'board_modify':
		if(isset($_GET['type'])){
			$type = var_cleanup($_GET['type']);
		}else{
			$error = $cp['noboardtype']; 
			echo acp_error($error, "error");
		}
		if($type == 1){
			#see if a board id was defined
			if(isset($_GET['id'])){
				$id = var_cleanup($_GET['id']);		 
			}else{
				$error = $txt['nobid'];
				echo acp_error($error, "error");		
			}
			$db->run = "SELECT Board FROM ebb_boards WHERE id='$id' and type='$type'";
			$modify_cat_r = $db->result();
			$chk_cat = $db->num_results();
			$db->close();
			#see if item is a category.
			if($chk_cat == 0){
				$error = $cp['notfound'];
				echo acp_error($error, "error");
			}
			//get permission values.
			$db->run = "SELECT B_Read FROM ebb_board_access where B_id='$id'";
			$permission = $db->result();
			$db->close();
			//read detect.
			$read_status = board_readaccess();
			#output html.
			$page = new template("../". $template_path ."/cp-modifycategory.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$cp[title]",
			"LANG-MODIFYCATEGORY" => "$cp[modcat]",
			"LANG-NOCAT" => "$cp[addcaterror]",
			"LANG-LONGCAT" => "$cp[longcatname]",
			"ID" => "$id",
			"LANG-CATEGORYNAME" => "$cp[catname]",
			"CATEGORYNAME" => "$modify_cat_r[Board]",
			"LANG-BOARDPERMISSION" => "$cp[boardpermissions]",
			"LANG-READACCESS" => "$cp[boardread]",
			"READACCESS" => "$read_status"));
			$page->output();
		}elseif($type == 2){
			#see if a board id was defined
			if(isset($_GET['id'])){
				$id = var_cleanup($_GET['id']);		 
			}else{
				$error = $txt['nobid'];
				echo acp_error($error, "error");		
			}
			$db->run = "SELECT Board, Description, Post_Increment, BBcode, Smiles, Image, Category FROM ebb_boards where id='$id' and type='$type'";
			$modify = $db->result();
			$board_chk = $db->num_results();
			$db->close();
			#see if item is a board.
			if($board_chk == 0){
				$error = $cp['notfound'];
				echo acp_error($error, "error");
			}
			//get permission values.
			$db->run = "SELECT B_Read, B_Post, B_Reply, B_Vote, B_Poll, B_Delete, B_Edit, B_Important, B_Attachment FROM ebb_board_access where B_id='$id'";
			$permission = $db->result();
			$db->close();
			//read detect.
			$read_status = board_readaccess();
			//write detect.
			$write_status = board_writeaccess();
			//reply detect.
			$reply_status = board_replyaccess();
			//vote detect
			$vote_status = board_voteaccess();
			//poll detect
			$poll_status = board_pollaccess();
			//delete detect
			$delete_status = board_deleteaccess();
			//edit detect
			$edit_status = board_editaccess();
			#attachment detect.
			$attachment_status = board_attachmentaccess();
			//important detect
			$important_status = board_importantaccess();
			//post increment detect.
			if($modify['Post_Increment'] == 1){
				$postincred = "<input type=\"radio\" name=\"increment\" value=\"1\" checked=checked />$txt[yes] <input type=\"radio\" name=\"increment\" value=\"0\" />$txt[no]";
			}else{
				$postincred = "<input type=\"radio\" name=\"increment\" value=\"1\" />$txt[yes] <input type=\"radio\" name=\"increment\" value=\"0\" checked=checked />$txt[no]"; 
			}
			//bbcode detect.
			if ($modify['BBcode'] == 1){
				$bbcode = "<input type=\"radio\" name=\"bbcode\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"bbcode\" value=\"0\" />$cp[off]";
			}else{
				$bbcode = "<input type=\"radio\" name=\"bbcode\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"bbcode\" value=\"0\" checked=checked />$cp[off]";
			}
			//smiles detect.
			if ($modify['Smiles'] == 1){
				$smile = "<input type=\"radio\" name=\"smiles\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"smiles\" value=\"0\" />$cp[off]";
			}else{
				$smile = "<input type=\"radio\" name=\"smiles\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"smiles\" value=\"0\" checked=checked />$cp[off]";
			}
			//image detect.
			if ($modify['Image'] == 1){
				$img = "<input type=\"radio\" name=\"img\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"img\" value=\"0\" />$cp[off]";
			}else{
				$img = "<input type=\"radio\" name=\"img\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"img\" value=\"0\" checked=checked />$cp[off]";
			}
			$cat_select = acpcategory_select();
			$page = new template("../". $template_path ."/cp-modifyboard.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$cp[title]",
			"LANG-MODIFYBOARD" => "$cp[modifyboard]",
			"LANG-NOBOARD" => "$cp[boardnameerror]",
			"LANG-LONGBOARD" => "$cp[longboardname]",
			"LANG-NODESCRIPTION" => "$cp[descriptionerror]",
			"LANG-NOCATSEL" => "$cp[categoryerror]",
			"ID" => "$id",
			"TYPE" => "$type",
			"LANG-BOARDNAME" => "$cp[boardname]",
			"BOARDNAME" => "$modify[Board]",
			"LANG-DESCRIPTION" => "$cp[description]",
			"DESCRIPTION" => "$modify[Description]",
			"LANG-BOARDPERMISSION" => "$cp[boardpermissions]",
			"LANG-BOARDSETTINGS" => "$cp[boardsettings]",
			"LANG-ON" => "$cp[on]",
			"LANG-OFF" => "$cp[off]",
			"LANG-READACCESS" => "$cp[boardread]",
			"READACCESS" => "$read_status",
			"LANG-WRITEACCESS" => "$cp[boardwrite]",
			"WRITEACCESS" => "$write_status",
			"LANG-REPLYACCESS" => "$cp[boardreply]",
			"REPLYACCESS" => "$reply_status",
			"LANG-VOTEACCESS" => "$cp[boardvote]",
			"VOTEACCESS" => "$vote_status",
			"LANG-POLLACCESS" => "$cp[boardpoll]",
			"POLLACCESS" => "$poll_status",
			"LANG-EDITACCESS" => "$cp[boardedit]",
			"EDITACCESS" => "$edit_status",
			"LANG-DELETEACCESS" => "$cp[boarddelete]",
			"DELETEACCESS" => "$delete_status",
			"LANG-ATTACHMENTACCESS" => "$cp[boardattachments]",
			"ATTACHMENTACCESS" => "$attachment_status",
			"LANG-IMPORTANTACCESS" => "$cp[boardimportant]",
			"IMPORTANTACCESS" => "$important_status",
			"LANG-CATEGORY" => "$cp[newcategory]",
			"CATEGORY" => "$cat_select",
			"LANG-POSTINCREMENT" => "$cp[postincrement]",
			"POSTINCREMENT" => "$postincred",
			"LANG-BBCODE" => "$cp[bbcode]",
			"BBCODE" => "$bbcode",
			"LANG-SMILE" => "$post[smiles]",
			"SMILE" => "$smile",
			"LANG-IMG" => "$cp[img]",
			"IMG" => "$img"));
			$page->output(); 
		}else{
			#see if a board id was defined
			if(isset($_GET['id'])){
				$id = var_cleanup($_GET['id']);		 
			}else{
				$error = $txt['nobid'];
				echo acp_error($error, "error");		
			}
			$db->run = "SELECT Board, Description, Post_Increment, BBcode, Smiles, Image, Category FROM ebb_boards where id='$id' and type='$type'";
			$modify = $db->result();
			$subboard_chk = $db->num_results();
			$db->close();
			#see if item is a board.
			if($subboard_chk == 0){
				$error = $cp['notfound'];
				echo acp_error($error, "error");
			}
			//get permission values.
			$db->run = "SELECT B_Read, B_Post, B_Reply, B_Vote, B_Poll, B_Delete, B_Edit, B_Important, B_Attachment FROM ebb_board_access where B_id='$id'";
			$permission = $db->result();
			$db->close();
			//read detect.
			$read_status = board_readaccess();
			//write detect.
			$write_status = board_writeaccess();
			//reply detect.
			$reply_status = board_replyaccess();
			//vote detect
			$vote_status = board_voteaccess();
			//poll detect
			$poll_status = board_pollaccess();
			//delete detect
			$delete_status = board_deleteaccess();
			//edit detect
			$edit_status = board_editaccess();
			#attachment detect.
			$attachment_status = board_attachmentaccess();
			//important detect
			$important_status = board_importantaccess();
			//post increment detect.
			if($modify['Post_Increment'] == 1){
				$postincred = "<input type=\"radio\" name=\"increment\" value=\"1\" checked=checked />$txt[yes] <input type=\"radio\" name=\"increment\" value=\"0\" />$txt[no]";
			}else{
				$postincred = "<input type=\"radio\" name=\"increment\" value=\"1\" />$txt[yes] <input type=\"radio\" name=\"increment\" value=\"0\" checked=checked />$txt[no]"; 
			}
			//bbcode detect.
			if ($modify['BBcode'] == 1){
				$bbcode = "<input type=\"radio\" name=\"bbcode\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"bbcode\" value=\"0\" />$cp[off]";
			}else{
				$bbcode = "<input type=\"radio\" name=\"bbcode\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"bbcode\" value=\"0\" checked=checked />$cp[off]";
			}
			//smiles detect.
			if ($modify['Smiles'] == 1){
				$smile = "<input type=\"radio\" name=\"smiles\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"smiles\" value=\"0\" />$cp[off]";
			}else{
				$smile = "<input type=\"radio\" name=\"smiles\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"smiles\" value=\"0\" checked=checked />$cp[off]";
			}
			//image detect.
			if ($modify['Image'] == 1){
				$img = "<input type=\"radio\" name=\"img\" value=\"1\" checked=checked />$cp[on] <input type=\"radio\" name=\"img\" value=\"0\" />$cp[off]";
			}else{
				$img = "<input type=\"radio\" name=\"img\" value=\"1\" />$cp[on] <input type=\"radio\" name=\"img\" value=\"0\" checked=checked />$cp[off]";
			}
			$cat_select = acpboard_select();
			$page = new template("../". $template_path ."/cp-modifyboard.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$cp[title]",
			"LANG-MODIFYBOARD" => "$cp[modifyboard]",
			"LANG-NOBOARD" => "$cp[boardnameerror]",
			"LANG-LONGBOARD" => "$cp[longboardname]",
			"LANG-NODESCRIPTION" => "$cp[descriptionerror]",
			"LANG-NOCATSEL" => "$cp[categoryerror]",
			"ID" => "$id",
			"TYPE" => "$type",
			"LANG-BOARDNAME" => "$cp[boardname]",
			"BOARDNAME" => "$modify[Board]",
			"LANG-DESCRIPTION" => "$cp[description]",
			"DESCRIPTION" => "$modify[Description]",
			"LANG-BOARDPERMISSION" => "$cp[boardpermissions]",
			"LANG-BOARDSETTINGS" => "$cp[boardsettings]",
			"LANG-ON" => "$cp[on]",
			"LANG-OFF" => "$cp[off]",
			"LANG-READACCESS" => "$cp[boardread]",
			"READACCESS" => "$read_status",
			"LANG-WRITEACCESS" => "$cp[boardwrite]",
			"WRITEACCESS" => "$write_status",
			"LANG-REPLYACCESS" => "$cp[boardreply]",
			"REPLYACCESS" => "$reply_status",
			"LANG-VOTEACCESS" => "$cp[boardvote]",
			"VOTEACCESS" => "$vote_status",
			"LANG-POLLACCESS" => "$cp[boardpoll]",
			"POLLACCESS" => "$poll_status",
			"LANG-EDITACCESS" => "$cp[boardedit]",
			"EDITACCESS" => "$edit_status",
			"LANG-DELETEACCESS" => "$cp[boarddelete]",
			"DELETEACCESS" => "$delete_status",
			"LANG-ATTACHMENTACCESS" => "$cp[boardattachments]",
			"ATTACHMENTACCESS" => "$attachment_status", 
			"LANG-IMPORTANTACCESS" => "$cp[boardimportant]",
			"IMPORTANTACCESS" => "$important_status",
			"LANG-CATEGORY" => "$cp[newcategory]",
			"CATEGORY" => "$cat_select",
			"LANG-BBCODE" => "$cp[bbcode]",
			"LANG-POSTINCREMENT" => "$cp[postincrement]",
			"POSTINCREMENT" => "$postincred",  
			"BBCODE" => "$bbcode",
			"LANG-SMILE" => "$post[smiles]",
			"SMILE" => "$smile",
			"LANG-IMG" => "$cp[img]",
			"IMG" => "$img"));
			$page->output(); 
		}
	break;
	case 'board_modify_process':
		if(isset($_GET['type'])){
			$type = var_cleanup($_GET['type']);
		}else{
			$error = $cp['noboardtype']; 
			echo acp_error($error, "error");
		}
		#set error values to default.
		$error = 0;
		$errormsg = '';
		if($type == 1){
			#see if a board id was defined
			if(isset($_GET['id'])){
				$id = var_cleanup($_GET['id']);		 
			}else{
				$error = $txt['nobid'];
				echo acp_error($error, "error");		
			}
			$modify_board_name = var_cleanup($_POST['board_name']);
			$modify_description = 'null';
			$modify_readaccess = var_cleanup($_POST['readaccess']);
			$modify_writeaccess = 4;
			$modify_replyaccess = 4;
			$modify_voteaccess = 4;
			$modify_pollaccess = 4;
			$modify_editaccess = 4;
			$modify_deleteaccess = 4;
			$modify_attachmentaccess = 4;
			$modify_importantaccess = 4;
			$modify_catsel = 0;
			$increment = 0;
			$modify_bbcode = 0;
			$modify_smiles = 0;
			$modify_img = 0;
			#do some error checking.
			if(empty($modify_board_name)){
				$errormsg = $cp['addcaterror']."\n\n";
				$error = 1;
			}
			if(strlen($modify_board_name) > 50){
				$errormsg .= $cp['longcatname']."\n\n";
				$error = 1;
			}
			if ($modify_readaccess == ""){
				$errormsg .= $cp['noreadsetting']."\n\n";
				$error = 1;
			}			
		}elseif($type == 2){
			#see if a board id was defined
			if(isset($_GET['id'])){
				$id = var_cleanup($_GET['id']);		 
			}else{
				$error = $txt['nobid'];
				echo acp_error($error, "error");		
			}
			$modify_board_name = var_cleanup($_POST['board_name']);
			$modify_description = var_cleanup($_POST['description']);
			$modify_readaccess = var_cleanup($_POST['readaccess']);
			$modify_writeaccess = var_cleanup($_POST['writeaccess']);
			$modify_replyaccess = var_cleanup($_POST['replyaccess']);
			$modify_voteaccess = var_cleanup($_POST['voteaccess']);
			$modify_pollaccess = var_cleanup($_POST['pollaccess']);
			$modify_editaccess = var_cleanup($_POST['editaccess']);
			$modify_deleteaccess = var_cleanup($_POST['deleteaccess']);
			$modify_attachmentaccess = var_cleanup($_POST['attachmentaccess']);
			$modify_importantaccess = var_cleanup($_POST['importantaccess']);
			$modify_catsel = var_cleanup($_POST['catsel']);
			$increment = var_cleanup($_POST['increment']);
			$modify_bbcode = var_cleanup($_POST['bbcode']);
			$modify_smiles = var_cleanup($_POST['smiles']);
			$modify_img = var_cleanup($_POST['img']); 
			//do some error checking.
			if (empty($modify_board_name)){
				$errormsg .= $cp['boardnameerror']."\n\n";
				$error = 1;
			}
			if (empty($modify_description)){
				$errormsg .= $cp['descriptionerror']."\n\n";
				$error = 1;
			}
			if ($modify_readaccess == ""){
				$errormsg .= $cp['noreadsetting']."\n\n";
				$error = 1;
			}
			if ($modify_writeaccess == ""){
				$errormsg .= $cp['nowritesetting']."\n\n";
				$error = 1;
			}
			if ($modify_replyaccess == ""){
				$errormsg .= $cp['noreplysetting']."\n\n";
				$error = 1;
			}
			if ($modify_voteaccess == ""){
				$errormsg .= $cp['novotesetting']."\n\n";
				$error = 1;
			}
			if ($modify_pollaccess == ""){
				$errormsg .= $cp['nopollsetting']."\n\n";
				$error = 1;
			}
			if ($modify_editaccess == ""){
				$errormsg .= $cp['noeditsetting']."\n\n";
				$error = 1;
			
			}
			if ($modify_deleteaccess == ""){
				$errormsg .= $cp['nodeletesetting']."\n\n";
				$error = 1;
			}
			if($modify_attachmentaccess == ""){
				$errormsg .= $cp['noattachmentsetting']."\n\n";
				$error = 1; 
			}
			if ($modify_importantaccess == ""){
				$errormsg .= $cp['noimportantsetting']."\n\n";
				$error = 1;
			}
			if (empty($modify_catsel)){
				$errormsg .= $cp['categoryerror']."\n\n";
				$error = 1;
			}
			if($increment == ""){
				$errormsg .= $cp['incrementerror']."\n\n";
				$error = 1; 
			}
			if ($modify_bbcode == ""){
				$errormsg .= $cp['bbcodeerror']."\n\n";
				$error = 1;
			}
			if ($modify_smiles == ""){
				$errormsg .= $cp['smileserror']."\n\n";
				$error = 1;
			}
			if ($modify_img == ""){
				$errormsg .= $cp['imgerror']."\n\n";
				$error = 1;
			}
			if(strlen($modify_board_name) > 50){
				$errormsg .= $cp['longboardname']."\n\n";
				$error = 1;
			} 
		}else{
			#see if a board id was defined
			if(isset($_GET['id'])){
				$id = var_cleanup($_GET['id']);		 
			}else{
				$error = $txt['nobid'];
				echo acp_error($error, "error");		
			}
			$modify_board_name = var_cleanup($_POST['board_name']);
			$modify_description = var_cleanup($_POST['description']);
			$modify_readaccess = var_cleanup($_POST['readaccess']);
			$modify_writeaccess = var_cleanup($_POST['writeaccess']);
			$modify_replyaccess = var_cleanup($_POST['replyaccess']);
			$modify_voteaccess = var_cleanup($_POST['voteaccess']);
			$modify_pollaccess = var_cleanup($_POST['pollaccess']);
			$modify_editaccess = var_cleanup($_POST['editaccess']);
			$modify_deleteaccess = var_cleanup($_POST['deleteaccess']);
			$modify_attachmentaccess = var_cleanup($_POST['attachmentaccess']);
			$modify_importantaccess = var_cleanup($_POST['importantaccess']);
			$modify_catsel = var_cleanup($_POST['catsel']);
			$increment = var_cleanup($_POST['increment']);
			$modify_bbcode = var_cleanup($_POST['bbcode']);
			$modify_smiles = var_cleanup($_POST['smiles']);
			$modify_img = var_cleanup($_POST['img']);
			//do some error checking.
			if (empty($modify_board_name)){
				$errormsg .= $cp['boardnameerror']."\n\n";
				$error = 1;
			}
			if (empty($modify_description)){
				$errormsg .= $cp['descriptionerror']."\n\n";
				$error = 1;
			}
			if ($modify_readaccess == ""){
				$errormsg .= $cp['noreadsetting']."\n\n";
				$error = 1;
			}
			if ($modify_writeaccess == ""){
				$errormsg .= $cp['nowritesetting']."\n\n";
				$error = 1;
			}
			if ($modify_replyaccess == ""){
				$errormsg .= $cp['noreplysetting']."\n\n";
				$error = 1;
			}
			if ($modify_voteaccess == ""){
				$errormsg .= $cp['novotesetting']."\n\n";
				$error = 1;
			}
			if ($modify_pollaccess == ""){
				$errormsg .= $cp['nopollsetting']."\n\n";
				$error = 1;
			}
			if ($modify_editaccess == ""){
				$errormsg .= $cp['noeditsetting']."\n\n";
				$error = 1;
			}
			if ($modify_deleteaccess == ""){
				$errormsg .= $cp['nodeletesetting']."\n\n";
				$error = 1;
			}
			if($modify_attachmentaccess == ""){
				$errormsg .= $cp['noattachmentsetting']."\n\n";
				$error = 1; 
			}
			if ($modify_importantaccess == ""){
				$errormsg .= $cp['noimportantsetting']."\n\n";
				$error = 1;
			}
			if (empty($modify_catsel)){
				$errormsg .= $cp['categoryerror']."\n\n";
				$error = 1;
			}
			if($increment == ""){
				$errormsg .= $cp['incrementerror']."\n\n";
				$error = 1;
			}
			if ($modify_bbcode == ""){
				$errormsg .= $cp['bbcodeerror']."\n\n";
				$error = 1;
			}
			if ($modify_smiles == ""){
				$errormsg .= $cp['smileserror']."\n\n";
				$error = 1;
			}
			if ($modify_img == ""){
				$errormsg .= $cp['imgerror']."\n\n";
				$error = 1;
			}
			if(strlen($modify_board_name) > 50){
				$errormsg .= $cp['longboardname']."\n\n";
				$error = 1;
			} 
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//process the query.
			$db->run = "UPDATE ebb_boards SET Board='$modify_board_name', Description='$modify_description', Category='$modify_catsel', Smiles='$modify_smiles', Post_Increment='$increment', BBcode='$modify_bbcode', Image='$modify_img' WHERE type='$type' and id='$id'";
			$db->query();
			$db->close();
			//modify the permission table.
			$db->run = "UPDATE ebb_board_access SET B_Read='$modify_readaccess', B_Post='$modify_writeaccess', B_Reply='$modify_replyaccess', B_Vote='$modify_voteaccess', B_Poll='$modify_pollaccess', B_Delete='$modify_deleteaccess', B_Edit='$modify_editaccess', B_Important='$modify_importantaccess', B_Attachment='$modify_attachmentaccess' WHERE B_id='$id'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Modified Board: $modify_board_name", "$logged_user", "$acp_date", "$ip");
			//bring user back to board section
			header("Location: boardcp.php"); 
		}
	break;
	case 'board_delete':
		#see if board type is defined.
		if(isset($_GET['type'])){
			$type = var_cleanup($_GET['type']);
		}else{
			$error = $cp['noboardtype']; 
			echo acp_error($error, "error");
		}
		#see if a board id was defined
		if(isset($_GET['id'])){
			$id = var_cleanup($_GET['id']);		 
		}else{
			$error = $txt['nobid'];
			echo acp_error($error, "error");		
		}
		if($type == 1){
			//get needed details for deleting other items.
			$db->run = "Select id FROM ebb_boards WHERE Category='$id'";
			$catdel_query = $db->query();
			$db->close();
			while($r = mysql_fetch_assoc($catdel_query)){
				#get topic details.
				$db->run = "Select tid FROM ebb_topics WHERE bid='$r[id]'";
				$topicdel_query = $db->query();
				$db->close();
				while($r2 = mysql_fetch_assoc($topicdel_query)){
					//delete polls made by topics in this board.
					$db->run = "DELETE FROM ebb_poll WHERE tid='$r2[tid]'";
					$db->query();
					$db->close();
					#delete any votes.
					$db->run = "DELETE FROM ebb_votes WHERE tid='$r2[tid]'";
					$db->query();
					$db->close();
					//delete read status from topics made in this board.
					$db->run = "DELETE FROM ebb_read WHERE Topic='$r2[tid]'";
					$db->query();
					$db->close();
					//delete any user subscriptions for topics made in this board.
					$db->run = "DELETE FROM ebb_topic_watch WHERE tid='$r2[tid]'";
					$db->query();
					$db->close();
					#delete any attachments thats tied to a topic under this board.
					$db->run = "select Filename from ebb_attachments where tid='$r2[tid]'";
					$attach_r = $db->result();
					$attach_chk = $db->num_results();
					$db->close();
					if($attach_chk == 1){
						#delete file from web space.
						$delattach = unlink ('../uploads/'. $attach_r['Filename']);
						#delete entry from db.
						$db->run = "DELETE FROM ebb_attachments WHERE tid='$r2[tid]'";
						$db->query();
						$db->close();
					}
				}
				//delete topics made in that board.
				$db->run = "DELETE FROM ebb_topics WHERE bid='$r[id]'";
				$db->query();
				$db->close();
				//delete read status from the db.
				$db->run = "DELETE FROM ebb_read WHERE Board='$r[id]'";
				$db->query();
				$db->close();
				#get post details.
				$db->run = "Select pid FROM ebb_posts WHERE bid='$r[id]'";
				$post_r = $db->query();
				$db->close();
				#delete any thing tied to posts.
				while($r3 = mysql_fetch_assoc($post_r)){
					#delete any attachments thats tied to a post under this board.
					$db->run = "select Filename from ebb_attachments where pid='$r3[pid]'";
					$attach_r2 = $db->result();
					$attach_chk2 = $db->num_results();
					$db->close();
					if($attach_chk2 == 1){
						#delete file from web space.
						$delattach = unlink ('../uploads/'. $attach_r2['Filename']);
						#delete entry from db.
						$db->run = "DELETE FROM ebb_attachments WHERE pid='$r3[pid]'";
						$db->query();
						$db->close(); 
					}
				}
				//delete posts made in that board.
				$db->run = "DELETE FROM ebb_posts WHERE bid='$r[id]'";
				$db->query();
				$db->close();
				//delete the permission rules set for this board.
				$db->run = "DELETE FROM ebb_board_access WHERE B_id='$r[id]'";
				$db->query();
				$db->close();
				//delete the moderator list for this board.
				$db->run = "DELETE FROM ebb_grouplist WHERE board_id='$r[id]'";
				$db->query();
				$db->close();
			}
			//delete board.
			$db->run = "DELETE FROM ebb_boards WHERE id='$id'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Deleted Board", "$logged_user", "$acp_date", "$ip");
			//bring user back
			header("Location: boardcp.php"); 
		}elseif($type == 2){
			//get topic details for deleting other items.
			$db->run = "Select tid FROM ebb_topics WHERE bid='$id'";
			$boarddel_query = $db->query();
			$db->close();
			while($r = mysql_fetch_assoc($boarddel_query)){
				//delete polls made by topics in this board.
				$db->run = "DELETE FROM ebb_poll WHERE tid='$r[tid]'";
				$db->query();
				$db->close();
				#delete any votes.
				$db->run = "DELETE FROM ebb_votes WHERE tid='$r[tid]'";
				$db->query();
				$db->close();
				//delete read status from topics made in this board.
				$db->run = "DELETE FROM ebb_read WHERE Topic='$r[tid]'";
				$db->query();
				$db->close();
				//delete any user subscriptions for topics made in this board.
				$db->run = "DELETE FROM ebb_topic_watch WHERE tid='$r[tid]'";
				$db->query();
				$db->close();
				#delete any attachments thats tied to a topic under this board.
				$db->run = "select Filename from ebb_attachments where tid='$r[tid]'";
				$attach_r = $db->result();
				$attach_chk = $db->num_results();
				$db->close();
				if($attach_chk == 1){
					#delete file from web space.
					$delattach = unlink ('../uploads/'. $attach_r['Filename']);
					#delete entry from db.
					$db->run = "DELETE FROM ebb_attachments WHERE tid='$r[tid]'";
					$db->query();
					$db->close();
				}
			}
			//delete board.
			$db->run = "DELETE FROM ebb_boards WHERE id='$id'";
			$db->query();
			$db->close();
			//delete topics made in that board.
			$db->run = "DELETE FROM ebb_topics WHERE bid='$id'";
			$db->query();
			$db->close();
			//delete read status from the db.
			$db->run = "DELETE FROM ebb_read WHERE Board='$id'";
			$db->query();
			$db->close();
			#get post details.
			$db->run = "Select pid FROM ebb_posts WHERE bid='$id'";
			$post_r = $db->query();
			$db->close();
			#delete any thing tied to posts.
			while($r2 = mysql_fetch_assoc($post_r)){
				#delete any attachments thats tied to a post under this board.
				$db->run = "select Filename from ebb_attachments where pid='$r2[pid]'";
				$attach_r2 = $db->result();
				$attach_chk2 = $db->num_results();
				$db->close();
				if($attach_chk2 == 1){
					#delete file from web space.
					$delattach = unlink ('../uploads/'. $attach_r2['Filename']);
					#delete entry from db.
					$db->run = "DELETE FROM ebb_attachments WHERE pid='$r2[pid]'";
					$db->query();
					$db->close(); 
				}
			}
			//delete posts made in that board.
			$db->run = "DELETE FROM ebb_posts WHERE bid='$id'";
			$db->query();
			$db->close();
			//delete the permission rules set for this board.
			$db->run = "DELETE FROM ebb_board_access WHERE B_id='$id'";
			$db->query();
			$db->close();
			//delete the moderator list for this board.
			$db->run = "DELETE FROM ebb_grouplist WHERE board_id='$id'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Deleted Board", "$logged_user", "$acp_date", "$ip");
			//bring user back to board section
			header("Location: boardcp.php"); 
		}else{
			//get topic details for deleting other items.
			$db->run = "Select tid FROM ebb_topics WHERE bid='$id'";
			$boarddel_query = $db->query();
			$db->close();
			while($r = mysql_fetch_assoc($boarddel_query)){
				//delete polls made by topics in this board.
				$db->run = "DELETE FROM ebb_poll WHERE tid='$r[tid]'";
				$db->query();
				$db->close();
				#delete any votes.
				$db->run = "DELETE FROM ebb_votes WHERE tid='$r[tid]'";
				$db->query();
				$db->close();
				//delete read status from topics made in this board.
				$db->run = "DELETE FROM ebb_read WHERE Topic='$r[tid]'";
				$db->query();
				$db->close();
				//delete any user subscriptions for topics made in this board.
				$db->run = "DELETE FROM ebb_topic_watch WHERE tid='$r[tid]'";
				$db->query();
				$db->close();
				#delete any attachments thats tied to a topic under this board.
				$db->run = "select Filename from ebb_attachments where tid='$r[tid]'";
				$attach_r = $db->result();
				$attach_chk = $db->num_results();
				$db->close();
				if($attach_chk == 1){
					#delete file from web space.
					$delattach = unlink ('../uploads/'. $attach_r['Filename']);
					#delete entry from db.
					$db->run = "DELETE FROM ebb_attachments WHERE tid='$r[tid]'";
					$db->query();
					$db->close();
				}
			}
			//delete board.
			$db->run = "DELETE FROM ebb_boards WHERE id='$id'";
			$db->query();
			$db->close();
			//delete topics made in that board.
			$db->run = "DELETE FROM ebb_topics WHERE bid='$id'";
			$db->query();
			$db->close();
			//delete read status from the db.
			$db->run = "DELETE FROM ebb_read WHERE Board='$id'";
			$db->query();
			$db->close();
			#get post details.
			$db->run = "Select pid FROM ebb_posts WHERE bid='$id'";
			$post_r = $db->query();
			$db->close();
			#delete any thing tied to posts.
			while($r2 = mysql_fetch_assoc($post_r)){
				#delete any attachments thats tied to a post under this board.
				$db->run = "select Filename from ebb_attachments where pid='$r2[pid]'";
				$attach_r2 = $db->result();
				$attach_chk2 = $db->num_results();
				$db->close();
				if($attach_chk2 == 1){
					#delete file from web space.
					$delattach = unlink ('../uploads/'. $attach_r2['Filename']);
					#delete entry from db.
					$db->run = "DELETE FROM ebb_attachments WHERE pid='$r2[pid]'";
					$db->query();
					$db->close(); 
				}
			}
			//delete posts made in that board.
			$db->run = "DELETE FROM ebb_posts WHERE bid='$id'";
			$db->query();
			$db->close();
			//delete the permission rules set for this board.
			$db->run = "DELETE FROM ebb_board_access WHERE B_id='$id'";
			$db->query();
			$db->close();
			//delete the moderator list for this board.
			$db->run = "DELETE FROM ebb_grouplist WHERE board_id='$id'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Deleted Board", "$logged_user", "$acp_date", "$ip");
			//bring user back to board section
			header("Location: boardcp.php");  
		}
	break;
	case 'prune':
		$board_select = prune_boardlist();
		$page = new template("../". $template_path ."/cp-prune.htm");
		$page->replace_tags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$cp[title]",
		"LANG-PRUNE" => "$cp[prune]",
		"LANG-NOAGE" => "$cp[noprunedate]",
		"LANG-TEXT" => "$cp[prunetxt]",
		"LANG-PRUNERULE" => "$cp[prunerule]",
		"LANG-BOARDLIST" => "$cp[pruneboard]",
		"BOARDLIST" => "$board_select",
		"LANG-SUBMIT" => "$cp[pruneboards]"));
		$page->output();
	break;
	case 'prune_process':
		$prune_age = var_cleanup($_POST['prune_age']);
		$boardsel = var_cleanup($_POST['boardsel']);
		#set error values to default.
		$error = 0;
		$errormsg = '';
		//error check
		if(empty($prune_age)){
			$errormsg = $cp['noprunedate']."\n\n";
			$error = 1;
		}
		if(strlen($prune_age) > 3){
			$errormsg .= $cp['longprunedate']."\n\n";
			$error = 1; 
		}
		if(empty($boardsel)){
			$errormsg .= $cp['noboardselect']."\n\n";
			$error = 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo acp_error($error, "validate");
		}else{
			//perform prune.
			$time_math = 3600*24*$prune_age;
			$remove_eq = time() - $time_math;
			#get post details.
			$db->run = "Select pid FROM ebb_posts WHERE Original_Date>='$remove_eq' and bid='$boardsel'";
			$post_r = $db->query();
			$db->close();
			#delete any thing tied to posts.
			while($r = mysql_fetch_assoc($post_r)){
				#delete any attachments thats tied to a post under this board.
				$db->run = "select Filename from ebb_attachments where pid='$r[pid]'";
				$attach_r = $db->result();
				$db->close();
				#delete file from web space.
				$delattach = unlink ('../uploads/'. $attach_r['Filename']);
				#delete entry from db.
				$db->run = "DELETE FROM ebb_attachments WHERE pid='$r[pid]'";
				$db->query();
				$db->close(); 
			}
			//process query
			$db->run = "DELETE FROM ebb_posts WHERE Original_Date>='$remove_eq' and bid='$boardsel'";
			$db->query();
			$db->close();
			//get topic details for deleting other items.
			$db->run = "Select tid FROM ebb_topics WHERE Original_Date>='$remove_eq' and bid='$boardsel'";
			$boarddel_query = $db->query();
			$db->close();
			while($r2 = mysql_fetch_assoc($boarddel_query)){
				//delete polls made by topics in this board.
				$db->run = "DELETE FROM ebb_poll WHERE tid='$r2[tid]'";
				$db->query();
				$db->close();
				#delete any votes.
				$db->run = "DELETE FROM ebb_votes WHERE tid='$r2[tid]'";
				$db->query();
				$db->close();
				//delete read status from topics made in this board.
				$db->run = "DELETE FROM ebb_read WHERE Topic='$r2[tid]'";
				$db->query();
				$db->close();
				//delete any user subscriptions for topics made in this board.
				$db->run = "DELETE FROM ebb_topic_watch WHERE tid='$r2[tid]'";
				$db->query();
				$db->close();
				#delete any attachments thats tied to a topic under this board.
				$db->run = "select Filename from ebb_attachments where tid='$r2[tid]'";
				$attach_r2 = $db->result();
				$db->close();
				#delete file from web space.
				$delattach = unlink ('../uploads/'. $attach_r2['Filename']);
				#delete entry from db.
				$db->run = "DELETE FROM ebb_attachments WHERE tid='$r2[tid]'";
				$db->query();
				$db->close();
			}
			//process query
			$db->run = "DELETE FROM ebb_topics WHERE Original_Date>='$remove_eq' and bid='$boardsel'";
			$db->query();
			$db->close();
			#log action in database.
			$acp_date = time();
			$ip = $_SERVER['REMOTE_ADDR'];
			echo acp_log_add("Pruned Board", "$logged_user", "$acp_date", "$ip");
			//bring user back.
			header("Location: boardcp.php");
		}
	break;
	default:
		if(isset($_GET['type'])){
			$type = var_cleanup($_GET['type']);
		}else{
			$type = 1; 
		}
		if(isset($_GET['bid'])){
			$bid = var_cleanup($_GET['bid']);
		}else{
			$bid = ''; 
		}
		#display board manager.
		$boardmanager = admin_board($type, $bid);
}
//display footer
$page = new template("../". $template_path ."/footer.htm");
$page->replace_tags(array(
  "LANG-POWERED" => "$index[poweredby]"));
$page->output();
ob_end_flush();
?>
