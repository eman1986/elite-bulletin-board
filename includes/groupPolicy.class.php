<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: groupPolicy.class.php
Last Modified: 10/04/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class groupPolicy {

    protected $db; // our PDO instance.

    /**
     * @var string
    */
    private $user;

    /**
     * @var string
    */
    private $gid;

    public function __construct(PDO $db, $username){

        global $lang;

        $this->db = $db;

        #see if user = guest.
        if ($username == "guest") {
            $this->gid = 0;
            $this->user = "guest";
        } else {
            $this->user = $username;

            #run a check on system
            if($this->validateGroupStatus()){
                #get group ID.
                $this->gid = $this->getGroupID();

                #validate the group exists.
                if($this->validateGroup() == false){
                    $error = new notifySys($lang['nogid'], true, true, __FILE__, __LINE__);
                    $error->genericError();
                }
            }else{
                    $error = new notifySys($lang['groupstatus'], true, true, __FILE__, __LINE__);
                    $error->genericError();
            }
        }
    }

    public function __destruct(){
        unset($this->user);
        unset($this->gid);
    }

    /**
    *validateGroup
    *
    *Validates gid value used within class to ensure user is correctly authenticated.
    *
    *@modified 8/27/09
    *
    *@return boolean (true|false)
    *
    *@access private
    */
    private function validateGroup(){

        global $db;

        #see if this is a guest, if so, do some hard-coded checks.
        if($this->user == "guest"){
            if(($this->user == "guest") and ($this->gid == 0)){
                return(true);
            }else{
                return(false);
            }
        }else{
            $db->SQL = "SELECT id FROM ebb_groups WHERE id='".$this->gid."' LIMIT 1";
            $validateGroup = $db->affectedRows();

            if($validateGroup == 1){
                return (true);
            }else{
                return(false);
            }
        }
    }

    /**
    *validateGroupStatus
    *
    *Validates that the group user has an active membership in defined gid.
    *
    *@modified 8/27/09
    *
    *@return boolean (true|false)
    *
    *@access private
    */
    private function validateGroupStatus(){

        global $db;

        #see if this is guest, if so, do some hard-coded checks.
        if($this->user == "guest"){
            if(($this->user == "guest") and ($this->gid == 0)){
                return(true);
            }else{
                return(false);
            }
        }else{
            $db->SQL = "SELECT gid FROM ebb_group_users WHERE Username='".$this->user."' AND Status='Active' LIMIT 1";
            $validateGroupStatus = $db->affectedRows();

            if($validateGroupStatus == 1){
                return (true);
            }else{
                return(false);
            }
        }
    }

    /**
    *getGroupID
    *
    *Obtains the GroupID to the group the defined user belongs to.
    *
    *@modified 8/27/09
    *
    *@return string $getGID['gid'] - SQL result of the requested groupID.
    *
    *@access private
    */
    private function getGroupID(){

        global $db;

        #set to 0, guest has no group setup.
        if($this->user == "guest"){
            $getGID = 0;
            return($getGID);
        }else{
            $db->SQL = "SELECT gid FROM ebb_group_users WHERE Username='".$this->user."' AND Status='Active' LIMIT 1";
            $getGID = $db->fetchResults();

            return($getGID['gid']);
        }
    }

    /**
    *groupAccessLevel
    *
    *Obtains the access level of the defined user.
    *
    *@modified 8/27/09
    *
    *@return string $getAccessLevel['Level'] - SQL result of requested access level.
    *
    *@access public
    */
    public function groupAccessLevel(){

        global $db;

        #see if user is guest, if so, they have zero-level access.
        if($this->user == "guest"){
            $getAccessLevel = 0;
            return($getAccessLevel);
        }else{
            $db->SQL = "SELECT Level FROM ebb_groups where id='".$this->gid."' LIMIT 1";
            $getAccessLevel = $db->fetchResults();

            return($getAccessLevel['Level']);
        }
    }

    /**
    *getGroupProfile
    *
    *Obtain the profile in use for defined group.
    *
    *@modified 8/27/09
    *
    *@return string $getAccessLevel['permission_type'] - SQL result of requested group profile.
    *
    *@access public
    */
    public function getGroupProfile(){

        global $db;

        #see if user is guest, if so, set profile to zero-level access.
        if($this->user == "guest"){
            $getAccessLevel = 0;

            return($getAccessLevel);
        }else{
            $db->SQL = "SELECT permission_type FROM ebb_groups where id='".$this->gid."' LIMIT 1";
            $getAccessLevel = $db->fetchResults();

            return($getAccessLevel['permission_type']);
        }
    }

    /**
    *getGroupName
    *
    *Obtain the group name for defined group.
    *
    *@modified 9/28/09
    *
    *@return string $getGroupName['Name'] - SQL result of requested group name.
    *
    *@access public
    */
    public function getGroupName(){

        global $db;

        #see if user is guest, if so, set gorpu name as simply guest.
        if($this->user == "guest"){
            $getGroupName = 'guest';

            return($getGroupName);
        }else{
            $db->SQL = "SELECT Name FROM ebb_groups where id='".$this->gid."' LIMIT 1";
            $getGroupName = $db->fetchResults();

            return($getGroupName['Name']);
        }
    }

    /**
    *changeGroupID
    *
    *use to either promote or demote a user.
    *
    *@modified 3/9/10
    *
    *@param integer $newGID - new GID user is part of.
    *
    *
    *@access public
    */
    public function changeGroupID($newGID){

        global $db;

        #see if user is guest, if so, exit without result.
        if($this->user == "guest"){
            $error = new notifySys($err['groupstatus'], true);
            $error->genericError();
        }else{
            $db->SQL = "UPDATE ebb_group_users SET gid='$newGID' WHERE Username='".$this->user."'";
            $db->query();
        }
    }

    /**
    *accessValidator
    *
    *validate user's privileges.
    *
    *@modified 3/9/10
    *
    *@param string $permissionAction - action code being validated.
    *
    *@return integer $permissionValue - automatic deny return for guest account.
    *@return string $validatePermission['set_value'] - filtered string to use in SQL query.
    *
    *@access private
    */
    private function accessVaildator($permissionAction){

        global $db, $lang;

        #see if user is guest, if so, deny any requests.
        if($this->user == "guest"){
            return (false);
        }else{
            #first lets make sure the permission profile used is valid.
            $db->SQL= "SELECT id FROM ebb_permission_profile WHERE id='".$this->getGroupProfile()."'";
            $permissionProfileChk = $db->affectedRows();

            #see if user ID is incorrect or Null.
            if(($permissionProfileChk == 0) and ($this->user !== "guest")){
                $error = new notifySys($lang['invalidprofile'], false, true, __FILE__, __LINE__);
                $error->genericError();
            }

            #lets also check to make sure the action requested is valid.
            $db->SQL = "SELECT id FROM ebb_permission_actions WHERE id='$permissionAction'";
            $permissionActionChk = $db->affectedRows();

            if($permissionActionChk == 0){
                $error = new notifySys($lang['invalidaction'], false, true, __FILE__, __LINE__);
                $error->genericError();
            }

            #see if user has correct permission to access requested permission.
            $db->SQL = "SELECT set_value FROM ebb_permission_data WHERE profile='".$this->getGroupProfile()."' and permission='$permissionAction'";
            $validatePermission = $db->fetchResults();

            #output value in script.
            if($validatePermission['set_value'] == 1){
                return(true);
            }else{
                return(false);
            }
        }
    }

    /**
    *permissionCheck
    *
    *Validate to see if user can access the requested area.
    *
    *@modified 8/27/09
    *
    *@param string $action - action in check.
    *
    *@return boolean $permissionChk - (true|false).
    *
    *@access private
    */
    private function permissionCheck($action){

        global $checkgroup, $checkmod;

        #autmatically fail check if user is a guest, and its not set to public.
        if(($this->user == "guest") AND ($action != 0)){
            $permissionChk = false;
        }else{
            if($checkmod == 1){
                $permissionChk = true;
            }else{
                if(($action == 1) AND ($this->groupAccessLevel() == 1)){
                    $permissionChk = true;
                }elseif(($action == 2) AND ($this->groupAccessLevel() == 1) or ($this->groupAccessLevel() == 2)){
                    $permissionChk = true;
                }elseif(($action == 3) AND ($this->groupAccessLevel() == 3) or ($this->groupAccessLevel() == 2) or ($this->groupAccessLevel() == 1)){
                    $permissionChk = true;
                }elseif($action == 4){
                    $permissionChk = false;
                }elseif(($action == 5) and ($checkgroup == 1) or ($this->groupAccessLevel() == 1) or ($checkmod == 1)){
                    $permissionChk = true;
                }elseif($action == 0){
                    $permissionChk = true;
                }else{
                    $permissionChk = false;
                }
            }
        }
        return($permissionChk);
    }

    /**
    *validateAccess
    *
    *Validate to see if user can access the requested area.
    *
    *@modified 3/22/10
    *
    *@param int $type - type of permission being checked (0=board, 1=group).
    *@param string $action - The action being validated.
    *
    *@return boolean $permissionChk - (true|false).
    *
    *@access public
    */
    public function validateAccess($type, $action){

        #see what type of permission to validate.
        if($type == 0){
            #board-based permssion validation.
            if($this->permissionCheck($action) == true){
                return(true);
            }else{
                return(false);
            }
        }elseif($type == 1){
            #group-based permission validation
            if($this->accessVaildator($action) == true){
                return(true);
            }else{
                return(false);
            }
        }else{
            #invalid operation, return an automatic false.
            return(false);
        }
    }
}
