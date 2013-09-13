<?php
define('IN_EBB', true);
/*
Filename: upload.php
Last Modified: 10/17/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
	if(isset($_GET['mode'])){
		$mode = var_cleanup($_GET['mode']);
	}else{
		$mode = ''; 
	}
//see if this is a guest trying to post.
	if ($stat == "guest"){
		header ("Location: login.php");
	}
//check to see if the install file is stil on the user's server.
$setupexist = checkinstall();
if ($setupexist == 1){
    if ($access_level == 1){
        $error = $txt['installadmin'];
        echo error($error, "error");
    }else{
        $error = $txt['install'];
        echo error($error, "general");
    }
}
//check to see if this user is able to access this board.
echo check_ban();
//check to see if the board is on or off.
	if ($board_status == 0){
		$offline_msg = nl2br($off_msg);
		$error = $offline_msg;
		if ($access_level == 1){
			$error .= "<p class=\"td\">[<a href=\"acp/index.php\">$menu[cp]</a>]</p>";
		}else{
			$error .= "<p class=\"td\">[<a href=\"login.php\">$txt[login]</a>]</p>"; 
		}
		echo error($error, "general");
		#terminate program after message appears.
		exit();
	}
#see if Board ID was declared, if not terminate any further outputting.
if((!isset($_GET['bid'])) or (empty($_GET['bid']))){
	die($txt['nobid']);
}else{
	$bid = var_cleanup($_GET['bid']); 
}
#see if Topic ID was declared, if not assume we're looking for a post ID instead.
if(isset($_GET['tid'])){
	$tid = var_cleanup($_GET['tid']);
}else{
	$tid = '';
}
#see if Post ID was declared, if not leave it blank as we're assuming no replies are yet created.
if((!isset($_GET['pid'])) or (empty($_GET['pid']))){
	$pid = '';
}else{
	$pid = var_cleanup($_GET['pid']);
}
//get posting rules.
$db->run = "select B_Attachment from ebb_board_access WHERE B_id='$bid'";
$board_rule = $db->result();
$db->close();
#get group access information.
if($stat == "Member"){
	$checkgroup = 0;
	$checkmod = 0;
}else{
	$checkgroup = group_validate($bid, $level_result['id'], 2);
	$checkmod = group_validate($bid, $level_result['id'], 1);
}
#see if user can even upload anything.
$attach_chk = permission_check($board_rule['B_Attachment']);
$permission_chk_attach = access_vaildator($permission_type, 26);

if($attach_chk == 0){
	$error = $process['noattach'];
	echo error($error, "error");
}elseif(($permission_chk_attach == 1) and ($checkgroup == 1)){
	$error = $process['noattach'];
	echo error($error, "error");
}else{
	switch ( $mode ){
	case 'upload':
		$colume = 'attachment_quota';
		$settings = board_settings($colume);
    	#get file values.
    	$file_name = $_FILES['attachment']['name'];
        $encryptName = sha1($file_name);
    	$file_type = $_FILES['attachment']['type'];
    	$file_size = $_FILES['attachment']['size'];
    	$file_temp = $_FILES['attachment']['tmp_name'];
    	$file_ext = strtolower(substr(strrchr($file_name, "."), 1));
        $uploadpath = "uploads/".$encryptName;
    	$error = 0;
    	$errormsg = '';
        
        if (!is_file($file_temp)) {
            $errormsg = $process['nofileentry']."\n\n";
            $error = 1;
        }        
		if($file_size > $settings['attachment_quota']){
			$errormsg .= $process['sizelimit']."\n\n";				 
			$error = 1;
		}    			
		if(!filetype_lookup($file_ext)){
			$errormsg .= $process['noattach']."\n\n";
			$error = 1; 
		} 
		if(empty($file_name)){
			$errormsg .= $process['nofileentry']."\n\n";
			$error = 1; 
		}
		if(file_exists($uploadpath)){
			$errormsg .= $process['fileexist']."\n\n";
			$error = 1;
		}
		#see if any errors occured and if so report it.
		if ($error == 1){
			$error = nl2br($errormsg);
			echo error($error, "validate");
		}else{
		 	#see if user already attached a file(current limit is 1 per topic).
		 	$db->run = "select id from ebb_attachments where Username='$logged_user' and tid='0' and pid='0'";
			$attach_ct = $db->num_results();
			$db->close();
			if($attach_ct == 1){
				#attach limit met.
				$error = $process['attachlimit'];
				echo error($error, "error"); 
			}else{
				#add attachment to upload folder.
                if ((is_uploaded_file($file_temp)) AND (move_uploaded_file($file_temp, $uploadpath))) {
					#add attachment to db for listing purpose.
                    $db->run = "INSERT INTO ebb_attachments (Username, Filename, encryptedFileName, File_Type, File_Size) VALUES('$logged_user', '$file_name', '$encryptName', '$file_type', '$file_size')";
					$db->query();
					$db->close();
					#go back to original location.
	  	    		header ("Location: upload.php?bid=".$bid);
				}else{
                    //see why the upload failed.
                    switch($_FILES['attachment']['error']){
                        case UPLOAD_ERR_OK: //0 - no error; possible file attack!
                            $uploadError = $process['cantupload'];
                        break;
                        case UPLOAD_ERR_INI_SIZE: //1 - uploaded file exceeds the upload_max_filesize directive in php.ini.
                            $uploadError = $process['sizelimit'];
                        break;
                        case UPLOAD_ERR_FORM_SIZE: //2 - uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form.
                            $uploadError = $process['sizelimit'];
                        break;
                        case UPLOAD_ERR_PARTIAL: //3 - uploaded file was only partially uploaded.
                            $uploadError = $process['partialupload'];
                        break;
                        case UPLOAD_ERR_NO_FILE: //4 - no file was uploaded.
                            $uploadError = $process['nofileentry'];
                        break;
                        case UPLOAD_ERR_NO_TMP_DIR: //6 - Missing a temporary folder. (PHP 4.3.10+ Only)
                            $uploadError = $process['notmpfile'];
                        break;
                        case UPLOAD_ERR_CANT_WRITE: //7 - Failed to write file to disk. (PHP 5.1+ Only)
                            $uploadError = $process['diskfailure'];
                        break;
                        case UPLOAD_ERR_EXTENSION: //8 - A PHP extension stopped the file upload. (PHP 5.2+ Only)
                            $uploadError = $process['phpexterr'];
                        break;
                        default: //default error.
                            $uploadError = $process['cantupload'];
                        break;
                    }
					echo error($uploadError, "error");
				}
			}
       	}
    break;
    case 'manage':
    	if (!isset($_GET['type'])){
    		die($txt['invalidaction']);
		}else{
			$type = var_cleanup($_GET['type']); 
		}
    	#manage attachments.
    	if($type == "topic"){
    	 	#sql query.
    	 	$db->run = "select id, Filename, File_Size from ebb_attachments where Username='$logged_user' and tid='$tid' and pid='0'";
			$topic_q = $db->query();
			$attach_ct = $db->num_results();
			$db->close();
			#see if there is an attachment to the topic, if not go to the attachment form.
			if($attach_ct == 0){
				header("Location: upload.php?bid=$bid");
			}else{
			 	#display manager.
    			$attachments = attach_manager($type);
			}
    	}elseif ($type == "post"){
    	 	#sql query.
	 	 	$db->run = "select id, Filename, File_Size from ebb_attachments where Username='$logged_user' and tid='0' and pid='$pid'";
			$post_q = $db->query();
			$attach_ct = $db->num_results();
			$db->close();
			#see if there is an attachment to the topic, if not go to the attachment form.
			if($attach_ct == 0){
				header("Location: upload.php?bid=$bid");
			}else{
			 	#display manager.
    			$attachments = attach_manager($type); 
			}
    	}else{
    		die("INVALID SELECTION!"); 
    	}
    break;
    case 'delete':
    	#get form values.
    	if(isset($_GET['id'])){    			 
    		$id = var_cleanup($_GET['id']);
    	}else{
    		die($txt['noattachid']); 
    	}
    	#get filename from db.
    	$db->run = "select encryptedFileName from ebb_attachments where id='$id'";
		$attach_r = $db->result();
		$db->close();
    	#delete file from web space.
    	$delattach = @unlink ('uploads/'. $attach_r['encryptedFileName']);
    	if($delattach){
    		#remove entry from db.
    		$db->run = "delete from ebb_attachments where id='$id'";
			$db->query();
			$db->close();
    		#go back to attachment form.
    		header ("Location: upload.php?bid=$bid");
    	}else{
    		$error = $post['cantdelete']; 
			echo error($error, "error"); 
    	}
    break;
    default:
    	#see if user already attached a file(current limit is 1 per topic).
		$db->run = "select id, Filename, File_Size from ebb_attachments where Username='$logged_user' and tid='0' and pid='0'";
		$attach_ct = $db->num_results();
		$uploads = $db->query();
		$db->close();
		if($attach_ct == 1){
		 	#display manager.
		 	$attachments = attach_manager("newentry");
		}else{
			$page = new template($template_path ."/upload.htm");
			$page->replace_tags(array(
			"TITLE" => "$title",
			"LANG-TITLE" => "$post[uploadfile]",
			"BID" => "$bid",
			"LANG-FILETOUPLOAD" => "$post[attachfile]",
			"LANG-UPLOADTEXT" => "$post[uploadtxt]",
			"LANG-LOADING" => "$post[loading]",
			"LANG-NOTHINGSELECTED" => "$process[nofileentry]",
			"LANG-CLOSEWINDOW" => "$txt[closewindow]"));
			$page->output();
		}
	}//switch statment.
}//attachment rule.
?>
