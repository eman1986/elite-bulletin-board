<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| EMAIL
| -------------------------------------------------------------------
| This file contains configuration options for the Email library.
|
| Please see user guide for more info:
| http://ellislab.com/codeigniter/user-guide/libraries/email.html
|
*/

$config['protocol'] = 'sendmail'; // The mail sending protocol.
$config['mailpath'] = '/usr/sbin/sendmail -t -i'; // The server path to Sendmail.
$config['smtp_host'] = ''; // SMTP Server Address.
$config['smtp_user'] = ''; //SMTP Username.
$config['smtp_pass'] = ''; // SMTP Password.
$config['smtp_port'] = ''; // SMTP Port.
$config['smtp_timeout'] = ''; //SMTP Timeout (in seconds).
$config['validate'] = TRUE; // Whether to validate the email address.

/* End of file email.php */
/* Location: ./application/config/email.php */
