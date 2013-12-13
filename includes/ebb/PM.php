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
    // DATA MEMBERS
    //

    /**
     * PDO instance.
     * @var PDO
     */
    protected $db;

    private $id;
    private $subject;
    private $sender;
    private $receiver;
    private $folder;
    private $message;
    private $date;
    private $readStatus;
    private static $sdb;
    public static $validFolders = array('Inbox', 'Archive');


    public function __construct(\PDO $db) {
        $this->db = $db;
        self::$sdb = $db; //get instance for our static methods
    }

    //
    // PROPERTIES
    //

    /**
     * set value for id
     *
     * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
     *
     * @param mixed $id
     * @return PM
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
     * set value for Subject
     *
     * type:VARCHAR,size:25,default:
     *
     * @param mixed $subject
     * @return PM
     */
    public function &setSubject($subject) {
        $this->subject=$subject;
        return $this;
    }

    /**
     * get value for Subject
     *
     * type:VARCHAR,size:25,default:
     *
     * @return mixed
     */
    public function getSubject() {
        return $this->subject;
    }

    /**
     * set value for Sender
     *
     * type:VARCHAR,size:25,default:
     *
     * @param mixed $sender
     * @return PM
     */
    public function &setSender($sender) {
        $this->sender=$sender;
        return $this;
    }

    /**
     * get value for Sender
     *
     * type:VARCHAR,size:25,default:
     *
     * @return mixed
     */
    public function getSender() {
        return $this->sender;
    }

    /**
     * set value for Receiver
     *
     * type:VARCHAR,size:25,default:
     *
     * @param mixed $receiver
     * @return PM
     */
    public function &setReceiver($receiver) {
        $this->receiver=$receiver;
        return $this;
    }

    /**
     * get value for Receiver
     *
     * type:VARCHAR,size:25,default:
     *
     * @return mixed
     */
    public function getReceiver() {
        return $this->receiver;
    }

    /**
     * set value for Folder
     *
     * type:VARCHAR,size:7,default:null
     *
     * @param mixed $folder
     * @return PM
     */
    public function &setFolder($folder) {
        $this->folder=$folder;
        return $this;
    }

    /**
     * get value for Folder
     *
     * type:VARCHAR,size:7,default:null
     *
     * @return mixed
     */
    public function getFolder() {
        return $this->folder;
    }

    /**
     * set value for Message
     *
     * type:TEXT,size:65535,default:null
     *
     * @param mixed $message
     * @return PM
     */
    public function &setMessage($message) {
        $this->message=$message;
        return $this;
    }

    /**
     * get value for Message
     *
     * type:TEXT,size:65535,default:null
     *
     * @return mixed
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * set value for Date
     *
     * type:VARCHAR,size:14,default:
     *
     * @param mixed $date
     * @return PM
     */
    public function &setDate($date) {
        $this->date=$date;
        return $this;
    }

    /**
     * get value for Date
     *
     * type:VARCHAR,size:14,default:
     *
     * @return mixed
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * set value for Read_Status
     *
     * type:CHAR,size:3,default:
     *
     * @param mixed $readStatus
     * @return PM
     */
    public function &setReadStatus($readStatus) {
        $this->readStatus=$readStatus;
        return $this;
    }

    /**
     * get value for Read_Status
     *
     * type:CHAR,size:3,default:
     *
     * @return mixed
     */
    public function getReadStatus() {
        return $this->readStatus;
    }

    //
    // METHODS
    //

    /**
     * Get a list of message assigned to a defined user.
     * @param integer $uid The UserID we want to filter by.
     * @param string $tZone The user's time zone.
     * @param string $dtFormat The user's date/time format.
     * @param string $folder The folder we wish to look in.
     * @param string $order The column and direction to sort data by.
     * @param integer $page how many records to show per page.
     * @param integer $indx where to begin the data range.
     * @return array an array of data to present to grid.
     */
    public function getPMMessagesByUserID($uid, $tZone,$dtFormat, $folder, $order, $page, $indx) {
        $messages = array();

        //fetch pm data.
        $this->db->select('p.id, p.Subject, p.Date, p.Read_Status, u.Username')
            ->from('ebb_pm p')
            ->join('ebb_users u', 'p.Sender=u.id', 'LEFT')
            ->where('p.Receiver', $uid)
            ->where('p.Folder', $folder);

        if (!is_null($order)) {
            if ($order == "formattedDateTime DESC") {
                $this->db->order_by('p.Date DESC');
            } elseif ($order == "formattedDateTime ASC") {
                $this->db->order_by('p.Date ASC');
            } else {
                $this->db->order_by($order);
            }
        }

        if (!is_null($page) && !is_null($indx)) {
            $this->db->limit($page, $indx);
        }

        $query = $this->db->get();

        //loop through data and bind to an array.
        foreach ($query->result() as $row) {
            $row->formattedDateTime = datetimeFormatter($row->Date,$dtFormat,$tZone);
            $messages[] = $row;
        }

        return $messages;
    }

    /**
     * Get a defined PM Message by PM ID.
     * @param integer $id The PM ID we want to filter by.
     * @return boolean TRUE, the record exists; FALSE, no such record exists.
     */
    public function getPMMessage($id) {
        try {
            $query = $this->db->prepare('SELECT id, Subject, Folder, Message, Date, Read_Status, Receiver, Sender FROM ebb_pm WHERE id=:id LIMIT 1');
            $query->execute(array(":id" => $id));
            $pmData = $query->fetchAll();

            //see if we have any records to show.
            if(count($pmData) > 0) {
                $this->setId($pmData->id);
                $this->setFolder($pmData->Folder);
                $this->setSubject($pmData->Subject);
                $this->setDate($pmData->Date);
                $this->setMessage($pmData->Message);
                $this->setReadStatus($pmData->Read_Status);
                $this->setReceiver($pmData->Receiver);
                $this->setSender($pmData->Sender);
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }

    /**
     * Get a total count of records for the ebb_pm table.
     * @param integer $uid User ID to filter by.
     * @param string $folder The folder we wish to look in.
     * @return integer
     */
    public function countAllMessagesByUser($uid, $folder) {
        $this->db->select('id')
            ->from('ebb_pm')
            ->where('Receiver', $uid)
            ->where('Folder', $folder);

        $query = $this->db->get();

        return $query->num_rows();
    }

    /**
     * Validate the user owns this message.
     * @param integer $uid
     * @return boolean TRUE means the message belongs to this user, FALSE, mean it does not.
     */
    public function IsPMOwner($uid) {
        $this->db->select('id')
            ->from('ebb_pm')
            ->where('id', $this->getId())
            ->where('Receiver', $uid)
            ->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Marks a message as read.
     */
    public function markAsRead() {
        $data = array('Read_Status' => 0);
        #update user.
        $this->db->where('id', $this->getId());
        $this->db->update('ebb_pm', $data);
    }

    /**
     * Creates a new PM Message.
     * @return integer New PM ID.
     */
    public function CreateMessage() {
        #setup values.
        $data = array(
            'Subject' => $this->getSubject(),
            'Sender' => $this->getSender(),
            'Receiver' => $this->getReceiver(),
            'Folder' => 'Inbox',
            'Message' => $this->getMessage(),
            'Date' => time(),
            'Read_Status' => 1
        );

        #add new message.
        $this->db->insert('ebb_pm', $data);

        //get pm id
        return $this->db->insert_id();
    }

    /**
     * Deletes the defined PM Message.
     */
    public function deleteMessage() {
        $this->db->where('id', $this->getId())
            ->delete('ebb_pm');
    }

    /**
     * Move a message to the Archive folder.
     */
    public function archiveMessage() {
        $data = array('Folder' => 'Archive');
        #update user.
        $this->db->where('id', $this->getId());
        $this->db->update('ebb_pm', $data);
    }

    /**
     * Get the amount of new messages by user.
     * @param integer $uid The user we want to check
     * @return integer Number of new messages.
     */
    public static function getNewMessageCount($uid) {
        self::$db->select('id')
            ->from('ebb_pm')
            ->where('Read_Status', 1)
            ->where('Receiver', $uid)
            ->limit(1);

        $query = self::$db->get();
        return $query->num_rows();
    }

    /**
     * See if we have hit the quota set by administrator.
     * @param string $folder The folder to check.
     * @param integer $uid User ID to check by.
     * @return boolean TRUE, quota not reached; FALSE, quota has been reached.
     */
    public static function QuotaCheck($folder, $uid) {
        //see if we entered a correct folder.
        if (!in_array($folder, self::$validFolders)) {
            log_message('error', 'invalid folder was provided.'.$folder); //log error in error log.
            return FALSE;
        } else {
            self::$db->select('id')
                ->from('ebb_pm')
                ->where('Folder', $folder)
                ->where('Receiver', $uid);
            $query = self::$db->get();
            $folderQuota = $query->num_rows();

            //see if we went over our quota.
            if ($folder == "Inbox" && self::$ci->preference->getPreferenceValue("pm_quota") == $folderQuota) {
                return FALSE;
            } elseif ($folder == "Archive" && self::$ci->preference->getPreferenceValue("archive_quota") == $folderQuota) {
                return FALSE;
            } else {
                return TRUE;
            }
        }

    }
}