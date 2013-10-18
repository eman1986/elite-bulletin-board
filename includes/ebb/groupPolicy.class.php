<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: groupPolicy.class.php
Last Modified: 10/18/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

namespace ebb;
class groupPolicy {

    /**
     * Our PDO instance.
     * @var PDO
    */
    protected $db;

    /**
     * flag to see if user is a guest or not.
     * @var boolean
    */
    public $isGuest = FALSE;

    private $id;
    private $name;
    private $description;
    private $enrollment;
    private $level;
    private $permissionType;

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
     * @return groupPolicy
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
     * set value for Name
     *
     * type:VARCHAR,size:30,default:
     *
     * @param mixed $name
     * @return groupPolicy
     */
    public function &setName($name) {
        $this->name=$name;
        return $this;
    }

    /**
     * get value for Name
     *
     * type:VARCHAR,size:30,default:
     *
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * set value for Description
     *
     * type:TINYTEXT,size:255,default:null
     *
     * @param mixed $description
     * @return groupPolicy
     */
    public function &setDescription($description) {
        $this->description=$description;
        return $this;
    }

    /**
     * get value for Description
     *
     * type:TINYTEXT,size:255,default:null
     *
     * @return mixed
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * set value for Enrollment
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $enrollment
     * @return groupPolicy
     */
    public function &setEnrollment($enrollment) {
        $this->enrollment=$enrollment;
        return $this;
    }

    /**
     * get value for Enrollment
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getEnrollment() {
        return $this->enrollment;
    }

    /**
     * set value for Level
     *
     * type:BIT,size:0,default:0
     *
     * @param mixed $level
     * @return groupPolicy
     */
    public function &setLevel($level) {
        $this->level=$level;
        return $this;
    }

    /**
     * get value for Level
     *
     * type:BIT,size:0,default:0
     *
     * @return mixed
     */
    public function getLevel() {
        return $this->level;
    }

    /**
     * set value for permission_type
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:0
     *
     * @param mixed $permissionType
     * @return groupPolicy
     */
    public function &setPermissionType($permissionType) {
        $this->permissionType=$permissionType;
        return $this;
    }

    /**
     * get value for permission_type
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:0
     *
     * @return mixed
     */
    public function getPermissionType() {
        return $this->permissionType;
    }

    /**
     * METHODS
    */

    /**
     * Populate properties with data.
     * @param integer $gid defined GroupID assigned to logged in user.
     * @return bool
    */
    public function getGroupData($gid) {
        try {
            //Get data
            $query = $this->db->prepare('SELECT id, Name, Description, Enrollment, Level, permission_type FROM ebb_groups WHERE id=:id LIMIT 1');
            $query->execute(array(":id" => $gid));

            //see if we have any records to show.
            if($query->rowCount() > 0) {
                $GroupData = $query->fetch(PDO::FETCH_OBJ);

                //populate properties with values.
                $this->setId($GroupData->id);
                $this->setName($GroupData->Name);
                $this->setDescription($GroupData->Description);
                $this->setEnrollment($GroupData->Enrollment);
                $this->setLevel($GroupData->Level);
                $this->setPermissionType($GroupData->permission_type);
                return TRUE;
            } else {
                return FALSE;
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }

    /**
     * use to either promote or demote a user.
     * @param string $user the username to change.
     * @param integer $newGID new GID user is part of.
     * @return bool
    */
    public function changeGroupID($user, $newGID){

        //see if user is guest, if so, exit without result.
        if ($this->IsGuest) {
            return FALSE;
        } else {
            try {
                $updateQ = $this->db->prepare('UPDATE  ebb_online SET gid=:gid WHERE Username=:username');
                $updateQ->execute(array(":gid" => $newGID, ":username" => $user));
                return TRUE;
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                return FALSE;
            }
        }
    }

    /**
     * validate user's privileges.
     * @param string $permissionAction action code being validated.
     * @return boolean
     * @access private
    */
    private function accessVaildator($permissionAction) {

        //see if user is guest, if so, deny any requests.
        if ($this->IsGuest) {
            return FALSE;
        } else {
            //see if user ID is incorrect or Null.
            if ($this->validateGroup()) {

                try {
                    $groupActionQ = $this->db->prepare('SELECT id FROM  ebb_permission_actions WHERE id=:id');
                    $groupActionQ->execute(array(":id" => $permissionAction));

                    if ($groupActionQ->rowCount() == 0) {
                        return FALSE;
                    } else {
                        //see if user has correct permission to access requested permission.
                        $groupPermissionQ = $this->db->prepare('SELECT set_value FROM  ebb_permission_data WHERE profile=:profile AND permission=:permission');
                        $groupPermissionQ->execute(array(":id" => $this->getPermissionType(), ":permission" => $permissionAction));

                        if ($groupPermissionQ->rowCount() > 0) {
                            $permissionData = $groupPermissionQ->fetch(PDO::FETCH_OBJ);

                            if ($permissionData->set_value == 1) {
                                return TRUE;
                            } else {
                                return FALSE;
                            }
                        } else {
                            return FALSE;
                        }
                    }
                }
                catch (PDOException $e) {
                    echo $e->getMessage();
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Validate to see if user can access the requested area.
     * @param string $action action in check.
     * @return boolean
     * @access private
    */
    private function permissionCheck($action){

        //automatically fail check if user is a guest, and its not set to public.
        if(($this->IsGuest) AND ($action != 0)) {
            return FALSE;
        } else {
            if(($action == 1) AND ($this->getLevel() == 1)) { //ADMIN ONLY
                return TRUE;
            } elseif(($action == 2) AND ($this->getLevel() == 1) or ($this->getLevel() == 2)) { //MODERATOR OR ADMIN
                return TRUE;
            } elseif(($action == 3) AND ($this->getLevel() == 3) or ($this->getLevel() == 2) or ($this->getLevel() == 1)) { //REG. USERS
                return TRUE;
            } elseif($action == 4){ //NO ONE
                return FALSE;
            } elseif($action == 0) { //EVERYONE
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Validate to see if user can access the requested area.
     * @param int $type type of permission being checked (0=board, 1=group).
     * @param string $action The action being validated.
     * @return boolean
     * @access public
    */
    public function validateAccess($type, $action){

        //see what type of permission to validate.
        if ($type == 0) {
            //board-based permission validation.
            if ($this->permissionCheck($action)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } elseif ($type == 1) {
            //group-based permission validation
            if ($this->accessVaildator($action)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            //invalid operation, return an automatic false.
            return FALSE;
        }
    }

    /**
     * Validates the entered group is valid.
     * @return boolean
    */
    private function validateGroup() {
        try {
            //Get data
            $query = $this->db->prepare('SELECT id FROM ebb_groups WHERE id=:id LIMIT 1');
            $query->execute(array(":id" => $this->getId()));

            //see if we have any records to show.
            if($query->rowCount() > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }
}
