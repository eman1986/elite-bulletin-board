<?php
namespace ebb;
if (!defined('IN_EBB')) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: PM.php
Last Modified: 10/20/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
 */

class PM {

    //
    // Data Members
    //

    /**
             * Defines a user.
             * @var string
         */
    public $user;

    /**
             * Defines a PM ID.
             * @var int
         */
    public $pmID;

    //
    // Constructors & Destructors
    //

    /**
             * Creates our PM Object.
             * @param int $pmID PM ID
             * @param string $pmUsr PM User.
             * @access Public
             * @version 6/11/2011
            */
    public function __construct($pmID, $pmUsr){
        $this->pmID = $pmID;
        $this->user = $pmUsr;
    }

    /**
             * Resets our PM Object.
             * @access Public
             * @version 6/8/2011
            */
    public function __destruct(){
        unset($this->pmID);
        unset($this->user);
    }

    //
    // Methods
    //

    /**
             * Obtains a list of Private Mages.
             * @param string $folder - Folder to view.
             * @access Public
             * @version 4/13/2011
             * @return  SQL query
         */
    public function ListMessages($folder="Inbox") {

        global $db;

        if($folder == "Outbox"){
            $db->SQL = "SELECT id, Subject, Sender, Date, Read_Status FROM ebb_pm WHERE Sender='".$this->user."' AND Read_Status='' ORDER BY Date DESC";
            $pmQry = $db->query();
        }else{
            $db->SQL = "SELECT id, Subject, Sender, Date, Read_Status FROM ebb_pm WHERE Reciever='".$this->user."' AND Folder='$folder' ORDER BY Date DESC";
            $pmQry = $db->query();
        }

        return $pmQry;
    }

    /**
             * Gets the amount of messages inside of a PM Folder.
             * @param string $folder
             * @return int
             * @access Public
             * @version 4/13/2011
        */
    public function GetUsageAmount($folder="Inbox") {

        global $db;

        if($folder == "Outbox"){
            $db->SQL = "SELECT id FROM ebb_pm WHERE Sender='".$this->user."' AND Read_Status=''";
            $usageAmt = $db->affectedRows();
        }else{
            $db->SQL = "SELECT id FROM ebb_pm WHERE Reciever='".$this->user."' AND Folder='$folder'";
            $usageAmt = $db->affectedRows();
        }

        return $usageAmt;
    }

    /**
             * Composes a message to a defined user.
             * @param string $sendTo - Person getting the message.
             * @param string $sentFrom - Person sending the message.
             * @param string $subject - Subject of PM Message.
             * @param string $body - Message body.
             * @param string $date - Date message was sent.
             * @access Public
             * @version 4/9/2011
         */
    public function ComposeMessage($sendTo, $sentFrom, $subject, $body, $date) {

        global $db;

        $db->SQL = "INSERT INTO ebb_pm (Sender, Reciever, Subject, Folder, Message, Date) VALUES('".$sentFrom."', '".$sendTo."', '".$subject."', 'Inbox', '".$body."', '".$date."')";
        $db->query();
    }

    /**
             * Gets a selected PM Message.
             * @access Public
             * @return Data Row
             * @version 4/9/2011
         */
    public function ReadMessage() {

        global $db;

        #get PM data.
        $db->SQL = "SELECT id, Read_Status, Sender, Reciever, Folder, Subject, Message, Date FROM ebb_pm WHERE id='".$this->pmID."'";
        $pmMessage = $db->fetchResults();

        return $pmMessage;
    }

    /**
             * See if a PM Exists.
             * @access Public
             * @version 4/13/2011
             * @return boolean
        */
    public function PMExists() {
        global $db;

        $db->SQL = "SELECT id  FROM ebb_pm WHERE id='".$this->pmID."'";
        $pmCheck = $db->affectedRows();

        #see if PM exists.
        if ($pmCheck == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
             * Deletes a selected PM Message.
             * @access Public
             * @version 4/9/2011
         */
    public function DeleteMessage() {

        global $db, $lang;

        #only delete message if user is owner.
        if ($this->IsPMOwner()) {
            //process query
            $db->SQL = "DELETE FROM ebb_pm WHERE id='".$this->pmID."'";
            $db->query();
        } else {
            $displayMsg = new notifySys($lang['accessdenied'], true);
            $displayMsg->displayError();
        }
    }

    /**
             * Archives a selected PM Message.
             * @access Public
             * @version 6/22/2011
         */
    public function ArchiveMessage() {

        global $db, $boardPref, $lang;

        #see if user has enough space to save message.
        $db->SQL = "SELECT id FROM ebb_pm WHERE Reciever='".$this->user."' AND Folder='Archive'";
        $check_archive = $db->affectedRows();

        if ($check_archive == $boardPref->getPreferenceValue("archive_quota")){
            $displayMsg = new notifySys($lang['overquota'], true);
            $displayMsg->displayError();
        }else{
            //process query
            $db->SQL = "UPDATE ebb_pm SET Folder='Archive' WHERE id='".$this->pmID."'";
            $db->query();
        }
    }

    /**
             * Performs a check to see if the user viewing the message has access to this message.
             * @access Public
             * @version 6/8/2011
             * @return boolean
         */
    public function IsPMOwner() {

        global $db;

            #get PM data.
        $db->SQL = "SELECT id FROM ebb_pm WHERE id='".$this->pmID."' AND Reciever='".$this->user."'";
        $validateOwner = $db->affectedRows();

        //see if pm message belong to the right user.
        if ($validateOwner == 1){
            return true;
        } else {
            return false;
        }
    }

    /**
             * Performs a check to see if the user posting a message isn't blocked by the desired sender.
             * @param string $bUser User to check against.
             * @access Public
             * @version 6/22/2011
             * @return boolean
         */
    public function IsBannedByUser($bUser) {

        global $db;

        //check to see if this user is on the ban list.
        $db->SQL = "SELECT rid FROM ebb_relationship WHERE status='2' AND friend='$bUser' AND uid='".$this->user."'";
        $validateBan = $db->affectedRows();

        if ($validateBan == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
             * Checks to see user has space for message.
             * @access Public
             * @version 6/22/2011
             * @param $folder the folder we're checking for quota.
             * @return boolean
         */
    public function QuotaCheck($folder, $usr="") {

        global $db, $boardPref;

        #see how we're checking this.
        if ($usr == "") {
            $sUsr = $this->user;
        } else {
            $sUsr = $usr;
        }

        if ($folder == "Inbox") {

            $db->SQL = "SELECT id FROM ebb_pm WHERE Reciever='".$sUsr."' AND Folder='Inbox'";
            $check_inbox = $db->affectedRows();

            //check to see if the from user's inbox is full.
            if ($check_inbox == $boardPref->getPreferenceValue("pm_quota")){
                return false;
            } else {
                return true;
            }
        } else if ($folder == "Archive") {

            $db->SQL = "SELECT id FROM ebb_pm WHERE Reciever='".$sUsr." AND Folder='Archive'";
            $check_archive = $db->affectedRows();

            #see if user has enough space to save message.
            if ($check_archive == $boardPref->getPreferenceValue("archive_quota")){
                return false;
            } else {
                return true;
            }
        }
    }
}