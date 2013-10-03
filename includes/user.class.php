<?php
if (!defined('IN_EBB')) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: user.class.php
Last Modified: 10/02/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
 */

class user {

    /**
     * DATA MEMBERS
    */

    protected $db; // our PDO instance.

    private $id;
    private $userName;
    private $password;
    private $gid;
    private $email;
    private $customTitle;
    private $lastVisit;
    private $pmNotify;
    private $hideEmail;
    private $www;
    private $location;
    private $sig;
    private $timeFormat;
    private $dateFormat;
    private $timeZone;
    private $dateJoined;
    private $ip;
    private $style;
    private $language;
    private $postCount;
    private $lastPost;
    private $lastSearch;
    private $failedAttempts;
    private $active;
    private $actKey;
    private $passwordRecoveryDate;
    private $warningLevel;
    private $suspendLength;
    private $suspendTime;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * PROPERTIES
     */

    /**
     * set value for id
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
     *
     * @param mixed $id
     * @return user
     */
    public function &setId($id) {
        $this->id=$id;
        return $this;
    }

    /**
     * get value for id
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
     *
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * set value for Username
     *
     * type:VARCHAR,size:25,default:
     *
     * @param mixed $userName
     * @return user
     */
    public function &setUserName($userName) {
        $this->userName=$userName;
        return $this;
    }

    /**
     * get value for Username
     *
     * type:VARCHAR,size:25,default:
     *
     * @return mixed
     */
    public function getUserName() {
        return $this->userName;
    }

    /**
     * set value for Password
     *
     * type:VARCHAR,size:40,default:null
     *
     * @param mixed $password
     * @return user
     */
    public function &setPassword($password) {
        $this->password=$password;
        return $this;
    }

    /**
     * get value for Password
     *
     * type:VARCHAR,size:40,default:null
     *
     * @return mixed
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * set value for gid
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:null
     *
     * @param mixed $gid
     * @return user
     */
    public function &setGid($gid) {
        $this->gid=$gid;
        return $this;
    }

    /**
     * get value for gid
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:null
     *
     * @return mixed
     */
    public function getGid() {
        return $this->gid;
    }

    /**
     * set value for Email
     *
     * type:VARCHAR,size:255,default:
     *
     * @param mixed $email
     * @return user
     */
    public function &setEmail($email) {
        $this->email=$email;
        return $this;
    }

    /**
     * get value for Email
     *
     * type:VARCHAR,size:255,default:
     *
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * set value for Custom_Title
     *
     * type:VARCHAR,size:20,default:null
     *
     * @param mixed $customTitle
     * @return user
     */
    public function &setCustomTitle($customTitle) {
        $this->customTitle=$customTitle;
        return $this;
    }

    /**
     * get value for Custom_Title
     *
     * type:VARCHAR,size:20,default:null
     *
     * @return mixed
     */
    public function getCustomTitle() {
        return $this->customTitle;
    }

    /**
     * set value for last_visit
     *
     * type:VARCHAR,size:14,default:
     *
     * @param mixed $lastVisit
     * @return user
     */
    public function &setLastVisit($lastVisit) {
        $this->lastVisit=$lastVisit;
        return $this;
    }

    /**
     * get value for last_visit
     *
     * type:VARCHAR,size:14,default:
     *
     * @return mixed
     */
    public function getLastVisit() {
        return $this->lastVisit;
    }

    /**
     * set value for PM_Notify
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $pmNotify
     * @return user
     */
    public function &setPmNotify($pmNotify) {
        $this->pmNotify=$pmNotify;
        return $this;
    }

    /**
     * get value for PM_Notify
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getPmNotify() {
        return $this->pmNotify;
    }

    /**
     * set value for Hide_Email
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $hideEmail
     * @return user
     */
    public function &setHideEmail($hideEmail) {
        $this->hideEmail=$hideEmail;
        return $this;
    }

    /**
     * get value for Hide_Email
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getHideEmail() {
        return $this->hideEmail;
    }

    /**
     * set value for WWW
     *
     * type:VARCHAR,size:200,default:
     *
     * @param mixed $www
     * @return user
     */
    public function &setWww($www) {
        $this->www=$www;
        return $this;
    }

    /**
     * get value for WWW
     *
     * type:VARCHAR,size:200,default:
     *
     * @return mixed
     */
    public function getWww() {
        return $this->www;
    }

    /**
     * set value for Location
     *
     * type:VARCHAR,size:70,default:
     *
     * @param mixed $location
     * @return user
     */
    public function &setLocation($location) {
        $this->location=$location;
        return $this;
    }

    /**
     * get value for Location
     *
     * type:VARCHAR,size:70,default:
     *
     * @return mixed
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * set value for Sig
     *
     * type:TINYTEXT,size:255,default:null
     *
     * @param mixed $sig
     * @return user
     */
    public function &setSig($sig) {
        $this->sig=$sig;
        return $this;
    }

    /**
     * get value for Sig
     *
     * type:TINYTEXT,size:255,default:null
     *
     * @return mixed
     */
    public function getSig() {
        return $this->sig;
    }

    /**
     * set value for Time_format
     *
     * type:VARCHAR,size:14,default:
     *
     * @param mixed $timeFormat
     * @return user
     */
    public function &setTimeFormat($timeFormat) {
        $this->timeFormat=$timeFormat;
        return $this;
    }

    /**
     * get value for Time_format
     *
     * type:VARCHAR,size:14,default:
     *
     * @return mixed
     */
    public function getTimeFormat() {
        return $this->timeFormat;
    }

    /**
     * set value for date_format
     *
     * type:VARCHAR,size:5,default:
     *
     * @param mixed $timeFormat
     * @return user
     */
    public function &setDateFormat($dateFormat) {
        $this->dateFormat=$dateFormat;
        return $this;
    }

    /**
     * get value for date_format
     *
     * type:VARCHAR,size:5,default:
     *
     * @return mixed
     */
    public function getDateFormat() {
        return $this->dateFormat;
    }

    /**
     * set value for Time_Zone
     *
     * type:VARCHAR,size:255,default:
     *
     * @param mixed $timeZone
     * @return user
     */
    public function &setTimeZone($timeZone) {
        $this->timeZone=$timeZone;
        return $this;
    }

    /**
     * get value for Time_Zone
     *
     * type:VARCHAR,size:255,default:
     *
     * @return mixed
     */
    public function getTimeZone() {
        return $this->timeZone;
    }

    /**
     * set value for Date_Joined
     *
     * type:VARCHAR,size:50,default:
     *
     * @param mixed $dateJoined
     * @return user
     */
    public function &setDateJoined($dateJoined) {
        $this->dateJoined=$dateJoined;
        return $this;
    }

    /**
     * get value for Date_Joined
     *
     * type:VARCHAR,size:50,default:
     *
     * @return mixed
     */
    public function getDateJoined() {
        return $this->dateJoined;
    }

    /**
     * set value for IP
     *
     * type:VARCHAR,size:40,default:
     *
     * @param mixed $ip
     * @return user
     */
    public function &setIp($ip) {
        $this->ip=$ip;
        return $this;
    }

    /**
     * get value for IP
     *
     * type:VARCHAR,size:40,default:
     *
     * @return mixed
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * set value for Style
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:0
     *
     * @param mixed $style
     * @return user
     */
    public function &setStyle($style) {
        $this->style=$style;
        return $this;
    }

    /**
     * get value for Style
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:0
     *
     * @return mixed
     */
    public function getStyle() {
        return $this->style;
    }

    /**
     * set value for Language
     *
     * type:VARCHAR,size:50,default:
     *
     * @param mixed $language
     * @return user
     */
    public function &setLanguage($language) {
        $this->language=$language;
        return $this;
    }

    /**
     * get value for Language
     *
     * type:VARCHAR,size:50,default:
     *
     * @return mixed
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * set value for Post_Count
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:0
     *
     * @param mixed $postCount
     * @return user
     */
    public function &setPostCount($postCount) {
        $this->postCount=$postCount;
        return $this;
    }

    /**
     * get value for Post_Count
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:0
     *
     * @return mixed
     */
    public function getPostCount() {
        return $this->postCount;
    }

    /**
     * set value for last_post
     *
     * type:VARCHAR,size:14,default:
     *
     * @param mixed $lastPost
     * @return user
     */
    public function &setLastPost($lastPost) {
        $this->lastPost=$lastPost;
        return $this;
    }

    /**
     * get value for last_post
     *
     * type:VARCHAR,size:14,default:
     *
     * @return mixed
     */
    public function getLastPost() {
        return $this->lastPost;
    }

    /**
     * set value for last_search
     *
     * type:VARCHAR,size:14,default:
     *
     * @param mixed $lastSearch
     * @return user
     */
    public function &setLastSearch($lastSearch) {
        $this->lastSearch=$lastSearch;
        return $this;
    }

    /**
     * get value for last_search
     *
     * type:VARCHAR,size:14,default:
     *
     * @return mixed
     */
    public function getLastSearch() {
        return $this->lastSearch;
    }

    /**
     * set value for failed_attempts
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $failedAttempts
     * @return user
     */
    public function &setFailedAttempts($failedAttempts) {
        $this->failedAttempts=$failedAttempts;
        return $this;
    }

    /**
     * get value for failed_attempts
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getFailedAttempts() {
        return $this->failedAttempts;
    }

    /**
     * set value for active
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $active
     * @return user
     */
    public function &setActive($active) {
        $this->active=$active;
        return $this;
    }

    /**
     * get value for active
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * set value for act_key
     *
     * type:VARCHAR,size:32,default:
     *
     * @param mixed $actKey
     * @return user
     */
    public function &setActKey($actKey) {
        $this->actKey=$actKey;
        return $this;
    }

    /**
     * get value for act_key
     *
     * type:VARCHAR,size:32,default:
     *
     * @return mixed
     */
    public function getActKey() {
        return $this->actKey;
    }

    /**
     * set value for password_recovery_date
     *
     * type:VARCHAR,size:14,default:
     *
     * @return user
     */
    public function &setPasswordRecoveryDate($passwordRecoveryDate) {
        $this->passwordRecoveryDate=$passwordRecoveryDate;
        return $this;
    }

    /**
     * get value for password_recovery_date
     *
     * type:VARCHAR,size:14,default:
     *
     * @return mixed
     */
    public function getPasswordRecoveryDate() {

    }

    /**
     * set value for warning_level
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $warningLevel
     * @return user
     */
    public function &setWarningLevel($warningLevel) {
        $this->warningLevel=$warningLevel;
        return $this;
    }

    /**
     * get value for warning_level
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getWarningLevel() {
        return $this->warningLevel;
    }

    /**
     * set value for suspend_length
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $suspendLength
     * @return user
     */
    public function &setSuspendLength($suspendLength) {
        $this->suspendLength=$suspendLength;
        return $this;
    }

    /**
     * get value for suspend_length
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getSuspendLength() {
        return $this->suspendLength;
    }

    /**
     * set value for suspend_time
     *
     * type:VARCHAR,size:14,default:
     *
     * @param mixed $suspendTime
     * @return user
     */
    public function &setSuspendTime($suspendTime) {
        $this->suspendTime=$suspendTime;
        return $this;
    }

    /**
     * get value for suspend_time
     *
     * type:VARCHAR,size:14,default:
     *
     * @return mixed
     */
    public function getSuspendTime() {
        return $this->suspendTime;
    }

    /**
     * METHODS
    */

    /**
     * Assign values from hash where the indexes match the tables field names
     * @param integer $userID user we want to get info on from DB.
     * @return bool
    */
    public function getUser($userID) {

        //Get data
        $query = $this->db->prepare('SELECT id, Username, Password, salt, Email, gid, Custom_Title, last_visit, PM_Notify, Hide_Email,WWW, Location, Sig, Time_format, date_format, Time_Zone, Date_Joined, IP, Style, Language, Post_Count, last_post, last_search, failed_attempts, active, act_key, password_recovery_date, warning_level, suspend_length, suspend_time from ebb_users WHERE Username=:username LIMIT 1');
        $query->execute(array(":username" => $userID));

        //see if we have any records to show.
        if($query->rowCount() > 0) {
            $userData = $query->fetch(PDO::FETCH_OBJ);

            //populate properties with values.
            $this->setId($userData->id);
            $this->setUserName($userData->Username);
            $this->setPassword($userData->Password);
            $this->setEmail($userData->Email);
            $this->setGid($userData->gid);
            $this->setCustomTitle($userData->Custom_Title);
            $this->setLastVisit($userData->last_visit);
            $this->setPmNotify($userData->PM_Notify);
            $this->setHideEmail($userData->Hide_Email);
            $this->setWww($userData->WWW);
            $this->setLocation($userData->Location);
            $this->setSig($userData->Sig);
            $this->setTimeFormat($userData->Time_format);
            $this->setDateFormat($userData->date_format);
            $this->setTimeZone($userData->Time_Zone);
            $this->setDateJoined($userData->Date_Joined);
            $this->setIp($userData->IP);
            $this->setStyle($userData->Style);
            $this->setLanguage($userData->Language);
            $this->setPostCount($userData->Post_Count);
            $this->setLastPost($userData->last_post);
            $this->setLastSearch($userData->last_search);
            $this->setFailedAttempts($userData->failed_attempts);
            $this->setActive($userData->active);
            $this->setActKey($userData->act_key);
            $this->setPasswordRecoveryDate($userData->password_recovery_date);
            $this->setWarningLevel($userData->warning_level);
            $this->setSuspendLength($userData->suspend_length);
            $this->setSuspendTime($userData->suspend_time);
            return TRUE;
        } else {
            return FALSE;
        }
    }


}