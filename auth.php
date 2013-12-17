<?php
define('IN_EBB', true);
/**
 * auth.php
 * @package ebbv22
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 12/10/13
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/
require "header.php";

$do = isset($_GET['do']) ? var_cleanup($_GET['do']) : "";

switch ($do) {
    case 'login':
        //get form value
        $username = var_cleanup($_POST['username']);
        $password = var_cleanup($_POST['password']);

        //validate data.
        if (empty($username)) {
            set_flashdata('NotifyType', 'error');
            set_flashdata('NotifyMsg', outputLanguageTag('login:nouser'));
            redirect("index.php");
        }
        if (empty($password)) {
            set_flashdata('NotifyType', 'error');
            set_flashdata('NotifyMsg', outputLanguageTag('login:nopass'));
            redirect("index.php");
        }

        //see if user is already logged in.
        if ($access_level != 0) {
            set_flashdata('NotifyType', 'error');
            set_flashdata('NotifyMsg', outputLanguageTag('login:alreadylogged'));
            redirect("index.php");
        }

        //try to log in.
        $authenticate = new ebb\login($db);
        $authenticate->usr = $username;
        $authenticate->pwd = $password;

        if ($authenticate->validateLogin()) {
            //see if account is activate.
            if (!$authenticate->isActive()) {
                set_flashdata('NotifyType', 'error');
                set_flashdata('NotifyMsg', outputLanguageTag('auth:inactiveuser'));
                redirect("index.php");
            }

        } else {
            set_flashdata('NotifyType', 'error');
            set_flashdata('NotifyMsg', outputLanguageTag('auth:nomatch'));
            redirect("index.php");
        }
    break;
    case 'logout':

    break;
    case 'forgot':

    break;
    case 'activate':

    break;
}