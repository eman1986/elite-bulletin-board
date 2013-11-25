<?php
namespace ebb;
use PDO;
use PDOException;
use Exception;

if (!defined('IN_EBB')) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: login.php
Last Modified: 11/25/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class login {

    protected $db; // our PDO instance.

    public $usr;

    public $pwd;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function __destruct() {
        $this->usr = NULL;
        $this->pwd = NULL;
    }

    /**
     *Performs a check through the database to ensure the requested username is valid.
     *@return bool
   */
    private function validateUser() {

        try {
            //check against the database to see if the username  match.
            $query = $this->db->prepare('SELECT count(id) from ebb_users WHERE Username=:username LIMIT 1');
            $query->execute(array(":username" => $this->usr));
            $validateUser = $query->fetchColumn();

            //see if username is value.
            return $validateUser == 0 ? false : true;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Performs a check through the database to ensure the requested password is valid.
     * @return bool
    */
    private function validatePwd() {

        //check against the database to see if the password match.
        $query = $this->db->prepare('SELECT Password from ebb_users WHERE Username=:username LIMIT 1');
        $query->execute(array(":username" => $this->usr));
        $hashedPwd = $query->fetchColumn();

        //see if password is value.
        return password_verify($this->pwd, $hashedPwd) ? false : true;
    }

    /**
     * Validate Login Key is valid.
     * @param string $key encrypted key hash being validated.
     * @return boolean
    */
    private function ValidateLoginKey($key) {

        $query = $this->db->prepare('SELECT count(login_key) from ebb_login_session WHERE username=:username');
        $query->execute(array(":username" => $key));
        $validateKey = $query->fetchColumn();

        #setup bool. value to see if user is active or not.
        return $validateKey == 0 ? false : true;
    }

    /**
     * Validates current login session.
     * @param $lastActive
     * @param $loginKey
     * @return bool
    */
    public function validateLoginSession($lastActive, $loginKey) {
        #Level 1, validate username.
        if ($this->validateUser()) {
            #Level 2, validate activity & key.
            if ((time() - $lastActive < 300) AND ($this->ValidateLoginKey($loginKey))) { //5 minutes
                #Level 3, update activity value and regenerate key.
                $new_loginKey = sha1(makeRandomPassword());
                $new_lastActive = time() + 300;

                #set new values in session.
                $_SESSION['ebbLastActive'] = $new_lastActive;
                $_SESSION['ebbLoginKey'] = $new_loginKey;

                #add new values to database.
                $data = array(
                    'last_active' => $new_lastActive,
                    'login_key' => $new_loginKey,
                    'username' => $this->usr
                );

                $query = $this->db->prepare('UPDATE ebb_login_session SET last_active=:last_active, login_key=:login_key  WHERE username=:username');
                $query->execute($data);

                return (true); //session is valid
            } else {
                return (false); //session is invalid
            }
        } else {
            return (false); //session is invalid
        }
    }

    /**
     * Performs a check through the database to ensure the requested information is valid.
     * @return bool
    */
    public function validateLogin() {
        //See if this is a guest account.
        if ($this->usr == "guest" || $this->pwd == "guest") {
            return false;
        } else {
            //see if user entered the correct information.
            if ($this->validateUser() && $this->validatePwd()) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * validateAdministrator
     * @return bool
    */
    public function validateAdministrator() {

        #See if this is a guest account.
        if ($this->user == "guest" || $this->pass == "guest") {
            return FALSE;
        } else {
            //see if user entered the correct information.
            if ($this->validateUser() || $this->validatePwd()) {
                //see if user is an administrator.
                $validateGroupPolicy = new groupPolicy($this->user);
                if ($validateGroupPolicy->groupAccessLevel() == 1) {
                    return TRUE;
                } else {
                    return FALSE;
                }//END group validation.
            } else {
                return FALSE;
            }//END user validation.
        }//END guest filtering.
    }

    /**
     * Validates current adminCP session.
     * @return bool
    */
    public function validateAdministratorSession() {

        //See if this is a guest account.
        if ($this->user == "guest" || $this->pass == "guest") {
            return FALSE;
        } else {
            //see if user entered the correct information.
            if ($this->validateUser() || $this->validatePwdEncrypted()) {
                //see if user is an administrator.
                $validateGroupPolicy = new groupPolicy($this->user);
                if ($validateGroupPolicy->groupAccessLevel() == 1) {
                    return TRUE;
                } else {
                    return FALSE;
                }//END group validation.
            }else{
                return FALSE;
            }//END user validation.
        }//END guest filtering.

    }

    /**
     * Performs login process, creating any sessions or cookies needed for the system.
     * @param boolean $remember keep user login info in tact?
     * @version 07/28/12
     * @access public
     */
//    public function logOn($remember){
//
//        global $db;
//
//        #setup variables.
//        $loginKey = sha1(makeRandomPassword());
//        $lastActive = time() + 300;
//
//        #see if user wants to remain logged on.
//        if($remember) {
//            //setup session length.
//            $expireTime = time() + (2592000);
//
//            //create cookie.
//            setcookie("ebbuser", $this->user, $expireTime, '/', $_SERVER['SERVER_NAME'], isSecure() ? 1 : 0, true);
//
//            #remove user's IP from who's online list.
//            $db->delete("ebb_online", "ip = :ip", array(":ip" => $ipAddr));
//
//            $_SESSION['ebb_login_key'] = $loginKey;
//
//            //generate session-based validation.
//            $this->regenerateSession(true);
//        } else {
//            //create a session.
//            $_SESSION['ebb_user'] = $this->user;
//            $_SESSION['ebb_login_key'] = $loginKey;
//
//            //generate session-based validation.
//            $this->regenerateSession(true);
//        }
//    }

    /**
     * Performs login process, creating any sessions or cookies needed for the system.
     * @param bool $remember
    */
    public function logOn($remember=false){

        global $ipAddr, $db;

        //see if user wants to remain logged on.
        if ($remember) {
            //create a session.
            $_SESSION['ebb_user'] = $this->user;

            //generate session-based validation.
            $this->regenerateSession(true);
        } else {
            //setup session length.
            $expireTime = time() + (2592000);

            //create cookie.
            setcookie("ebbuser", $this->user, $expireTime, '/', $_SERVER['SERVER_NAME'], isSecure() ? 1 : 0, true);

            #remove user's IP from who's online list.
            $db->SQL = "delete from ebb_online where ip='$ipAddr'";
            $db->query();

            //generate session-based validation.
            $this->regenerateSession(true);
        }
    }

    /**
     * Performs logout process, removing any sessions or cookies created from the system.
    */
    public function logOut() {

        global $db;

        #setup session length.
        $expireAcp = time()-3600;
        $expireTime = time() - (2592000);

        #see if user wants to remain logged on.
        if (isset($_COOKIE['ebbuser'])) {

            #destroy cookies.
            setcookie("ebbuser", $this->user, $expireTime, '/', $_SERVER['SERVER_NAME'], isSecure() ? 1 : 0, true);

            #remove user from who's online list.
            $db->SQL = "DELETE FROM ebb_online WHERE Username='".$this->user."'";
            $db->query();

            #close out ACP cookie if needed
            if (isset($_COOKIE['ebbacpu']) and (isset($_COOKIE['ebbacpp']))) {
                setcookie("ebbacpu", $this->user, $expireAcp, '/', $_SERVER['SERVER_NAME'], isSecure() ? 1 : 0, true);
            }

            #clear session data.
            session_destroy();
        } else {
            #remove user from who's online list.
            $db->SQL = "DELETE FROM ebb_online WHERE Username='".$this->user."'";
            $db->query();

            #close out ACP cookie if needed
            if (isset($_COOKIE['ebbacpu']) and (isset($_COOKIE['ebbacpp']))) {
                setcookie("ebbacpu", $this->user, $expireAcp, '/', $_SERVER['SERVER_NAME'], isSecure() ? 1 : 0, true);
            }

            #clear session data.
            session_destroy();
        }
    }

    /**
     * Performs a check to ensure the session value is valid and not hijacked.
     * @param bool $destroy true will destroy old session data; false will not.
    */
    public function validateSession($destroy = false){
        try {
            //validate User Agent and make sure it didn't just 'magically' change.
            if($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']){
                //session mismatch, lets silently log out user.
                $this->logOut();
            } else {
                /*
                regenerate Session ID.
                NOTE: We should only be clearing the old session IDs when performing important tasks
                such as logging in or anything within the ACP.
                */
                $this->regenerateSession($destroy);
            }
        } catch(Exception $e) {
            $error = new notifySys($e, true, true, __FILE__, __LINE__);
            $error->genericError();
        }
    }

    /**
     * creates a new session id and destroys the old session id(if any exists).
     * @param bool $destroy true will destroy old session data; false will not.
    */
    public function regenerateSession($destroy = false){
        if(!isset($_SESSION['userAgent'])){
            $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        //Create new session & destroy the old one.
        if ($destroy) {
            session_regenerate_id(true);
        } else {
            session_regenerate_id();
        }
    }

    /**
     * Checks to see if the user is verified as active or still waiting for activation.
     * @return bool
    */
    public function isActive(){
        global $db;

        //@TODO MAY NOT NEED THIS ANYMORE!
        //check against the database to see if the username and password match.
        $db->SQL = "SELECT active FROM ebb_users WHERE Username='".$this->user."' LIMIT 1";
        $validateStatus = $db->fetchResults();

        #setup bool. value to see if user is active or not.
        if ($validateStatus['active'] == 0) {
            return(false);
        } else {
            return(true);
        }
    }

    /**
	*deactivateUser
	*
	*disable user's active status.
	*
	*@modified 10/26/09
	*
	*@access public
	*/
	public function deactivateUser(){

        //@TODO MAY NOT NEED THIS ANYMORE!

		global $db;
		
		$db->SQL = "UPDATE ebb_users SET active='0' WHERE Username='".$this->user."' LIMIT 1";
		$db->query();
	}

	/**
	*activateUser
	*
	*activates the user to allow them access the system.
	*
	*@modified 10/26/09
	*
	*@access public
	*/
	public function activateUser(){

        //@TODO MAY NOT NEED THIS ANYMORE!

		global $db;

		$db->SQL = "UPDATE ebb_users SET active='1' WHERE Username='".$this->user."' LIMIT 1";
		$db->query();
	}

    /**
	*getFailedLoginCt
	*
	*See how many times the user failed to login correctly.
	*
	*@modified 8/10/09
	*
	*
	*@access public
	*/
	public function getFailedLoginCt(){

        //@TODO MAY NOT NEED THIS ANYMORE!

	    global $db;

	    #get the count from the user table..
        $db->SQL = "SELECT failed_attempts FROM ebb_users WHERE Username='".$this->user."' LIMIT 1";
		$getFailedLoginCt = $db->fetchResults();

		return($getFailedLoginCt);
	}

    /**
	*setFailedLogin
	*
	*increment fail count for defined user.
	*
	*@modified 7/24/11
	*
	*
	*@access public
	*/
	public function setFailedLogin(){

	    global $db;

        //@TODO MAY NOT NEED THIS ANYMORE!

	    #get new count.
		$newCount = $this->getFailedLoginCt();
		$incrementFailedCt = $newCount['failed_attempts'] + 1;

	    #get the count from the user table.
        $db->SQL = "UPDATE ebb_users SET failed_attempts='$incrementFailedCt' WHERE Username='".$this->user."' LIMIT 1";
		$db->query();
	}

    /**
	*clearFailedLogin
	*
	*Clears the failed count to 0.
	*
	*@modified 8/10/09
	*
	*
	*@access public
	*/
	public function clearFailedLogin(){

	    global $db;

        //@TODO MAY NOT NEED THIS ANYMORE!

	    #clear count.
        $db->SQL = "UPDATE ebb_users SET failed_attempts='0' WHERE Username='".$this->user."' LIMIT 1";
		$db->query();
	}

    /**
	*checkBan
	*
	*Checks to see if the user is banned or suspended.
	*/
	public function checkBan() {
        global $db, $lang, $suspend_length, $suspend_date, $groupProfile;

        //@TODO MAY NOT NEED THIS ANYMORE OR SHOULD BE REFACTORED!

        //see if user is marked as banned.
        if ($groupProfile == 6) {
            $error = new notifySys($lang['banned'], true);
        }

        //see if user is suspended.
        if ($suspend_length > 0) {
            #see if user is still suspended.
            $math = 3600 * $suspend_length;
            $suspend_time = $suspend_date + $math;
            $today = time() - $math;
            if($suspend_time > $today){
                $error = new notifySys($lang['suspended'], true);
                $error->displayError();
            }
        }

        #see if the IP of the user is banned.
        $uip = detectProxy();
        $db->SQL = "SELECT ban_item FROM ebb_banlist WHERE ban_type='IP' AND ban_item LIKE '%$uip%'";
        $banChk = $db->affectedRows();

        #output an error msg.
        if ($banChk == 1) {
            $error = new notifySys($lang['banned'], true);
            $error->displayError();
        }
    }
}
