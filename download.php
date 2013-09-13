<?php
define('IN_EBB', true);
/*
Filename: download.php
Last Modified: 06/30/2012

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
include "header.php";

$id = var_cleanup($_GET['id']);
#see if any important ids are left blank.
if(empty($id)){
	#send that user to the index page.
	header("Location: index.php"); 
}
// Check to see if the download script was called
if (basename($_SERVER['PHP_SELF']) == 'download.php'){
	$colume = 'download_attachments';
	$settings = board_settings($colume);
	$permission_chk_dwnld = access_vaildator($permission_type, 29);
	#see if guests or authorized users can download content.
	if(($permission_chk_dwnld == 0) and ($stat == "groupmember")){
		$error = $attach['accessdenied'];
		echo error($error, "error");
	}elseif(($settings['download_attachments'] == 0) and ($stat == "guest")){
		$error = $attach['accessdenied'];
		echo error($error, "error");	 
	}
	#see if attachment is listed, if so proceed.
	$db->run = "select Filename, encryptedFileName, Download_Count from ebb_attachments where id='$id'";
    $attach_ct = $db->num_results();
    $attach_r = $db->result();
    $db->close();
	if ($attach_ct == 1){
		#replace space code with an actual space.
		$file = str_replace('%20', ' ', $attach_r['Filename']);
		$dl_path = "uploads/".$attach_r['encryptedFileName'];
		#see if the user is trying to access another directory.
		if (substr($file, 0, 1) == '.' || strpos($file, '..') > 0 || substr($file, 0, 1) == '/' || strpos($file, '/') > 0){
			#Display hack attempt error
			die($txt['invalidaction']);
		}
		#check if the file exists, if it doesn't, fire an error message and kill the script
		if(!file_exists($dl_path)){
			$error = $attach['nofile'];
			echo error($error, "error");
		}
		$ext = strtolower(substr(strrchr($file, "."), 1));
		#Determine correct MIME type
		switch($ext){
			case "asf":     $type = "video/x-ms-asf";                break;
			case "avi":     $type = "video/x-msvideo";               break;
			case "exe":     $type = "application/octet-stream";      break;
			case "mov":     $type = "video/quicktime";               break;
			case "mp3":     $type = "audio/mpeg";                    break;
			case "mpg":     $type = "video/mpeg";                    break;
			case "mpeg":    $type = "video/mpeg";                    break;
			case "rar":     $type = "encoding/x-compress";           break;
			case "txt":     $type = "text/plain";                    break;
			case "wav":     $type = "audio/wav";                     break;
			case "wma":     $type = "audio/x-ms-wma";                break;
			case "wmv":     $type = "video/x-ms-wmv";                break;
			case "zip":     $type = "application/x-zip-compressed";  break;
			default:        $type = "application/force-download";    break;
        }
		#Fix IE bug.
		$header_file = (strstr($_SERVER['HTTP_USER_AGENT'], 'MSIE')) ? preg_replace('/\./', '%2e', $file, substr_count($file, '.') - 1) : $file;
		header("Pragma: public"); #required.

		header("Expires: 0");

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

		header("Cache-Control: private",false); #some browsers require this

		header("Content-Type: $type");
		#declares file as an attachment.
		header("Content-Disposition: attachment; filename=\"" . $header_file . "\";");

		header("Content-Transfer-Encoding: binary");
		
		header("Content-Length: ".filesize($dl_path));
	
        #Send file for download
        $stream = fopen($dl_path, 'rb');
		if ($stream){
			if (!feof($stream) && connection_status() == 0) {
				#reset time limit for big files
				set_time_limit(0);
				print(fread($stream, filesize($dl_path)));
				flush();
			}
			fclose($stream);
			
			#increase the download counter by 1.
			$newct = $attach_r['Download_Count'] + 1;
			$db->run = "update ebb_attachments SET Download_Count='$newct' where id='$id'";
			$db->query();
			$db->close();
		} else {
	        #display an error here.
			$error = $attach['dlerr'];
			echo error($error, "error");
		}
	}else{
		#display an error here.
		$error = $attach['notfound'];
		echo error($error, "error");
	}
}else{
	#Display hack attempt error
	die($txt['invalidaction']);
}
ob_end_flush();
?>
