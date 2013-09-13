<?php
if (!defined('IN_EBB') ) {
die("<b>!!ACCESS DENIED HACKER!!</b>");
}
//email message for PM Notify.
function pm_notify(){

	global $pm_data, $board_address;
	
	$pm_message = "Hello $pm_data[Reciever],

	$pm_data[Sender] has written you an PM titled, $pm_data[Subject].

	$board_address/PM.php?action=read&id=$pm_data[id]

	If you wish to stop receiving these notices, just edit your profile.
	======
	This is an automated message, please do not reply to this email.";
	
	return($pm_message);
}
//email message for digest subscription.
function digest(){

	global $digest, $topic, $board_address, $title;
	
	$digest_message = "hi $digest[username],

	You have received this because $topic[author] has replied to $topic[Topic].

	$board_address/viewtopic.php?bid=$topic[bid]&tid=$topic[tid]&pid=$topic[pid]#$topic[pid]

	There may be other replies to this topic, but you will not receive any new emails until you view this topic.

	If you wish to stop receiving notices about this topic, go to your control panel and click on Subscription Settings.

	regards,

	$title Staff
	======
	This is an automated message, please do not reply to this email.";
	return ($digest_message);
}
//email message for registering account.
function none_confirm(){

	global $title, $board_address;
	
	$register_message = "Welcome new user,

	You just joined $title.

	Please remember your login details as your password is encrypted and will require a reset if you forget.

	to login go here:
	$board_address/login.php

	If you wish to modify your profile, you will have to login first, then click on Profile.

	if you have any questions please email the admin of the board.

	$title Staff
	======
	This is an automated message, please do not reply to this email.";

	return($register_message);
}
//email verify for user.
function user_confirm(){

	global $title, $username, $act_key, $board_address;

	$user_verify_msg = "Hello,

	You are receiving this because you have registered an account on $title.

	Please remember your login details as your password is encrypted and will require a reset if you forget.

	But before you can login you have to activate your account by clicking on the link below.

	$board_address/login.php?mode=verify_acct&u=$username&key=$act_key

	If you don't want to be a member of this community, please contact the administrator of the board so they can delete the account.

	Regards,

	$title staff
	======
	This is an automated message, please do not reply to this email.";
	
	return($user_verify_msg);
}
//email verify for admin.
function admin_confirm(){

	global $title;

	$admin_verify_msg = "Hello,

	You are receiving this because you have registered an account on $title.
	
	Please remember your login details as your password is encrypted and will require a reset if you forget.

	Before you can do anything on this board, the administrator has requested that they review your account first. When
	they make their decision, you will get an email whether they accept you or not.

	Regards,

	$title staff
	======
	This is an automated message, please do not reply to this email.";

	return($admin_verify_msg);
}
//email to inform of approved review.
function accept_user(){

	global $title, $board_address;

	$acct_pass_msg = "Hello,

	You have been approved to become a member of $title. You may login at:

	$board_address/login.php

	Please remember your login details as your password is encrypted and will require a reset if you forget.

	Regards,

	$title staff
	======
	This is an automated message, please do not reply to this email.";
	
	return($acct_pass_msg);
}
//email to inform of denied review.
function deny_user(){

	global $title;

	$acct_fail_msg = "Hello,

	The administrator at $title has rejected your request to join the board.

	To find out why, you may contact them and ask them why they made that decision.

	Regards,

	$title staff
	======
	This is an automated message, please do not reply to this email.";

	return($acct_fail_msg);
}
//lost password email.
function pwd_reset(){

	global $title, $new_pwd, $board_address, $user_r, $newActKey;
    
    $IP = $_SERVER['REMOTE_ADDR'];

	$lost_message = "hello,

	you are receiving this email because you requested a password reset from $title.
	
	If you did not request this, report this to the board administrator.
	
	For security Reasons, your account is disabled until you reverify yourself.
	
	$board_address/login.php?mode=verify_acct&u=$user_r[Username]&key=$newActKey

	a new password was made for you, below is the new password.

	$new_pwd

	to change it, Click on Profile.
	
	IP Address that sent out the request: $IP
	
	If this is NOT your IP, report this to your board administrator IMMEDIATELY!

	$title staff
	======
	This is an automated message, please do not reply to this email.";
	
	return($lost_message);
}
//report post email.
function report_topic(){

	global $title, $reason, $msg, $t, $board_address, $tid;

	$report_topic_msg = "Hello,

	It has come to our attention that a user is abusing the board. Below is what the reported user has written:

	Reason for report: $reason
	Message: $msg

	the topic can be found at:
	
	$board_address/viewtopic.php?bid=$t[bid]&tid=$tid
	======
	This is an automated message, please do not reply to this email.";
	
	return ($report_topic_msg);
}
function report_post(){	

	global $title, $reason, $msg, $t, $tid, $pid, $board_address;

	$report_post_msg = "Hello,

	It has come to our attention that a user is abusing the board. Below is what the reported user has written:

	Reason for report: $reason
	Message: $msg

	the topic can be found at:

	$board_address/viewtopic.php?bid=$t[bid]&tid=$tid#$pid
	======
	This is an automated message, please do not reply to this email.";

	return($report_post_msg);
}
?>
