<?php
/*
Filename: smtp.php
Last Modified: 10/17/2008

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

#mail class-entends the class created by phpmailer.
class ebbmail extends PHPMailer{
	var $priority = 3;
    var $to_name;
    var $to_email;
    var $From = null;
    var $FromName = null;
    var $Sender = null;

	function ebbmail(){

		global $db;
		//get setting values for smtp.
		$db->run = "select mail_type,smtp_server,smtp_port,smtp_user,smtp_pass,Board_Email,Site_Title from ebb_settings";
		$smtp = $db->result();
		$db->close();
		//setup smtp server connection.
		if($smtp['mail_type'] == 0){
			$this->Host = $smtp['smtp_server'];
			$this->Port = $smtp['smtp_port'];
			//setup the login details.
			if($smtp['smtp_user'] !== ""){
				$this->SMTPAuth  = true;
				$this->Username  = $smtp['smtp_user'];
				$this->Password  = $smtp['smtp_pass'];
			}
			$this->Mailer = "smtp";
		}
		//setup from address.
		if(!$this->From){
			$this->From = $smtp['Board_Email'];
		}
		//setup from name.
		if(!$this->FromName){
			$this-> FromName = $smtp['Site_Title'];
		}
		//setup sender address.
		if(!$this->Sender){
			$this->Sender = $smtp['Board_Email'];
		}
		//set priority of email.
		$this->Priority = $this->priority;
	}
}
?>
