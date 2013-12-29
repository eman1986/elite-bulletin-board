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
Last Modified: 12/29/2013

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
            $query = $this->db->prepare('SELECT COUNT(id) FROM ebb_users WHERE Username=:username LIMIT 1');
            $query->execute(array(":username" => $this->usr));
            $validateUser = $query->fetchColumn();

            //see if username is value.
            return $validateUser == 0 ? FALSE : TRUE;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return NULL;
        }
    }

    /**
     * Performs a check through the database to ensure the requested password is valid.
     * @return bool
    */
    private function validatePwd() {
        try {
            //check against the database to see if the password match.
            $query = $this->db->prepare('SELECT Password FROM ebb_users WHERE Username=:username LIMIT 1');
            $query->execute(array(":username" => $this->usr));
            $hashedPwd = $query->fetchColumn();

            //see if password is value.
            return password_verify($this->pwd, $hashedPwd) ? FALSE : TRUE;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }

    /**
     * Validate Login Key is valid.
     * @param string $key encrypted key hash being validated.
     * @return boolean
    */
    private function ValidateLoginKey($key) {
        try {
            $query = $this->db->prepare('SELECT COUNT(login_key) FROM ebb_login_session WHERE username=:username');
            $query->execute(array(":username" => $key));
            $validateKey = $query->fetchColumn();

            //determine if user is active or not.
            return $validateKey == 0 ? FALSE : TRUE;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }

    /**
     * Validates current login session.
     * @param $lastActive
     * @param $loginKey
     * @return bool
    */
    public function validateLoginSession($lastActive, $loginKey) {
        //Level 1 - Validate session.
        if ($this->validateSession()) {
            //Level 2, validate username.
            if ($this->validateUser()) {
                //Level 3, validate activity & key.
                if ((time() - $lastActive < 300 && $this->ValidateLoginKey($loginKey))) { //5 minutes
                    //Level 4, update activity value and regenerate key.
                    $new_loginKey = sha1(makeRandomPassword());
                    $new_lastActive = time() + 300;

                    //set new values in session.
                    $_SESSION['ebbLastActive'] = $new_lastActive;
                    $_SESSION['ebbLoginKey'] = $new_loginKey;

                    //add new values to database.
                    $data = array(
                        'last_active' => $new_lastActive,
                        'login_key' => $new_loginKey,
                        'username' => $this->usr
                    );

                    $query = $this->db->prepare('UPDATE ebb_login_session SET last_active=:last_active, login_key=:login_key  WHERE username=:username');
                    $query->execute($data);

                    return TRUE; //session is valid
                } else {
                    return FALSE; //session is invalid
                }
            } else {
                return FALSE; //session is invalid
            }
        } else {
            return FALSE; //session is invalid
        }
    }

    /**
     * Performs a check through the database to ensure the requested information is valid.
     * @return bool
    */
    public function validateLogin() {
        //See if this is a guest account.
        if ($this->usr == "guest" || $this->pwd == "guest") {
            return FALSE;
        } else {
            //see if user entered the correct information.
            if ($this->validateUser() && $this->validatePwd()) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Performs login process, creating any sessions or cookies needed for the system.
     * @param boolean $remember keep user login info in tact?
    */
    public function logOn($remember=FALSE){
        //see if user wants to remain logged on.
        if($remember) {
            //setup session length.
            $expireTime = time() + (2592000);

            //create cookie.
            setcookie("ebbuser", $this->user, $expireTime, '/', $_SERVER['SERVER_NAME'], isSecure() ? 1 : 0, true);
        } else {
            //create a session.
            $_SESSION['ebb_user'] = $this->user;
        }

        //remove user's IP from who's online list.
        $query = $this->db->prepare('DELETE FROM ebb_online WHERE ip=:ip');
        $query->execute(array(
            ":ip" => detectProxy()
        ));

        $_SESSION['ebb_last_active'] = time() + 300;
        $_SESSION['ebb_login_key'] = sha1(makeRandomPassword());

        //generate session-based validation.
        $this->regenerateSession(true);
    }

    /**
     * Performs logout process, removing any sessions or cookies created from the system.
    */
    public function logOut() {

        #setup session length.
        $expireTime = time() - (2592000);

        //see if user was being remembered.
        if (isset($_COOKIE['ebbuser'])) {
            //destroy cookies.
            setcookie("ebbuser", $this->user, $expireTime, '/', $_SERVER['SERVER_NAME'], isSecure() ? 1 : 0, true);
        }

        //clear session data.
        session_destroy();

        $query = $this->db->prepare('DELETE FROM ebb_online WHERE Username=:username');
        $query->execute(array(
            ":username" => $this->user
        ));
    }

    /**
     * Performs a check to ensure the session value is valid and not hijacked.
     * @param bool $destroy true will destroy old session data; false will not.
     * @return bool TRUE, session is valid; FALSE, its not.
    */
    public function validateSession($destroy = FALSE) {
        //validate User Agent and make sure it didn't just 'magically' change.
        if ($_SESSION['userAgent'] != $_SERVER['HTTP_USER_AGENT']) {
            //session mismatch, lets silently log out user.
            $this->logOut();
            return FALSE;
        } else {
            //regenerate Session ID.
            $this->regenerateSession($destroy);
            return TRUE;
        }
    }

    /**
     * creates a new session id and destroys the old session id(if any exists).
     * @param bool $destroy true will destroy old session data; false will not.
    */
    public function regenerateSession($destroy = FALSE){
        if (!isset($_SESSION['userAgent'])) {
            $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
        }

        //Create new session & destroy the old one.
        session_regenerate_id($destroy);
    }
}
