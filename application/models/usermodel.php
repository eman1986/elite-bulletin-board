<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * usermodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 07/10/2013
*/

/**
 * User Entity
 * @abstract CI_Model
*/
class Usermodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/

	private $id;
	private $userName;
	private $password;
	private $gid;
	private $email;
	private $customTitle;
	private $lastVisit;
	private $pmNotify;
	private $hideEmail;
	private $mSn;
	private $aol;
	private $yahoo;
	private $icq;
	private $www;
	private $location;
	private $avatar;
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

	public function __construct() {
        parent::__construct();
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * set value for MSN
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @param mixed $mSn
	 * @return Usermodel
	 */
	public function &setMSn($mSn) {
		$this->mSn=$mSn;
		return $this;
	}

	/**
	 * get value for MSN
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @return mixed
	 */
	public function getMSn() {
		return $this->mSn;
	}

	/**
	 * set value for AOL
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @param mixed $aol
	 * @return Usermodel
	 */
	public function &setAol($aol) {
		$this->aol=$aol;
		return $this;
	}

	/**
	 * get value for AOL
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @return mixed
	 */
	public function getAol() {
		return $this->aol;
	}

	/**
	 * set value for Yahoo
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @param mixed $yahoo
	 * @return Usermodel
	 */
	public function &setYahoo($yahoo) {
		$this->yahoo=$yahoo;
		return $this;
	}

	/**
	 * get value for Yahoo
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @return mixed
	 */
	public function getYahoo() {
		return $this->yahoo;
	}

	/**
	 * set value for ICQ
	 *
	 * type:VARCHAR,size:15,default:
	 *
	 * @param mixed $icq
	 * @return Usermodel
	 */
	public function &setIcq($icq) {
		$this->icq=$icq;
		return $this;
	}

	/**
	 * get value for ICQ
	 *
	 * type:VARCHAR,size:15,default:
	 *
	 * @return mixed
	 */
	public function getIcq() {
		return $this->icq;
	}

	/**
	 * set value for WWW
	 *
	 * type:VARCHAR,size:200,default:
	 *
	 * @param mixed $www
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * set value for Avatar
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @param mixed $avatar
	 * @return Usermodel
	 */
	public function &setAvatar($avatar) {
		$this->avatar=$avatar;
		return $this;
	}

	/**
	 * get value for Avatar
	 *
	 * type:VARCHAR,size:255,default:
	 *
	 * @return mixed
	 */
	public function getAvatar() {
		return $this->avatar;
	}

	/**
	 * set value for Sig
	 *
	 * type:TINYTEXT,size:255,default:null
	 *
	 * @param mixed $sig
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return mixed
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @return Usermodel
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
	 * @version 10/09/12
	 * @param integer $user user we want to get info on from DB.
	 */
	public function getUser($userID) {

		//SQL grabbing count of all topics for this board.
		$this->db->select('id, Username, Password, Email, gid, Custom_Title, last_visit, PM_Notify, Hide_Email,MSN, AOL, Yahoo, ICQ, WWW, Location, Avatar, Sig, Time_format, date_format, Time_Zone, Date_Joined, IP, Style, Language, Post_Count, last_post, last_search, failed_attempts, active, act_key, password_recovery_date, warning_level, suspend_length, suspend_time')
		  ->from('ebb_users')
		  ->where('id', $userID);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
			$userData = $query->row();
			
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
			$this->setMSn($userData->MSN);
			$this->setAol($userData->AOL);
			$this->setYahoo($userData->Yahoo);
			$this->setIcq($userData->ICQ);
			$this->setWww($userData->WWW);
			$this->setLocation($userData->Location);
			$this->setAvatar($userData->Avatar);
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
			log_message('error', 'invalid userID was provided.'.$userID); //log error in error log.
			return FALSE;
		}
	}
	
	/**
	 * Search the username list.
	 * @param string $user Search by username.
	 * @param boolean $showBannedUsers do we want to show or hide banned users.
	 * @return array an array of results.
	*/
	public function searchUser($user, $showBannedUsers) {
		$usrRes = array();
		
		if ($showBannedUsers) {
			$this->db->select('u.id, u.Username')
			  ->from('ebb_users u')
			  ->like('u.Username', $user);
		} else {
			$this->db->select('u.id, u.Username')
			  ->from('ebb_users u')
			  ->join('ebb_groups g', 'g.id=u.gid', 'LEFT')
			  ->where('g.permission_type!=', 6)
			  ->like('u.Username', $user);
		}
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$usrRes[] = $row;
			}
		}
		return $usrRes;
	}
	
	/**
	 * Get User ID from a define dusername.
	 * @param integer $user The username we wish to grab their id.
	 * @return integer|boolean User ID if username exists, FALSE if no username is found.
	*/
	public function getUIDFromUsername($user) {
		$this->db->select('id')
		  ->from('ebb_users')
		  ->where('Username', $user);
		$query = $this->db->get();
		
		//see if we have any records to show.
		if($query->num_rows() > 0) {
			$userData = $query->row();
			return $userData->id;
		} else {
			return FALSE;
		}
	}

	/**
	 * Get Username from a define user id.
	 * @param integer $uid The uid we wish to grab their username.
	 * @return integer|boolean Username if username exists, FALSE if no user id is found.
	*/
	public function getUsernameFromUID($uid) {
		$this->db->select('Username')
		  ->from('ebb_users')
		  ->where('id', $uid);
		$query = $this->db->get();
		
		//see if we have any records to show.
		if($query->num_rows() > 0) {
			$userData = $query->row();
			return $userData->Username;
		} else {
			return FALSE;
		}
	}
	
	public function getEmailList() {
		$this->db->select('Username, Email')
		  ->from('ebb_users')
		  ->where('active', 1);
		$query = $this->db->get();
		
		//see if we have any records to show.
		if($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$users[] = $row;
			}

			return $users;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get some settings to check for PM Notification.
	 * @param type $uid User we want to get PM Data from.
	 * @return array|boolean Array of values for user if valid, FALSE if invalid uid entered.
	 */
	public function getPMNotifyData($uid) {
		$uData = array();
		//SQL grabbing count of all topics for this board.
		$this->db->select('id, Username, Email, PM_Notify, Language')
		  ->from('ebb_users')
		  ->where('id', $uid);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
			$userData = $query->row();
			
			$uData = array(
			  'uid' => $userData->id,
			  'username' => $userData->Username,
			  'email' => $userData->Email,
			  'notify' => $userData->PM_Notify,
			  'lang' => $userData->Language
			);
			return $uData;
		} else {
			log_message('error', 'invalid userID was provided.'.$uid); //log error in error log.
			return FALSE;
		}
	}
	
	/**
	 * List all records from ebb_users table.
	 * @param string $order The column and direction to sort data by.
	 * @param integer $page how many records to show per page.
	 * @param integer $indx where to begin the data range.
	 * @return array the data from ebb_users.
	 * @version 09/22/12
	 */
	public function ListAll($order, $page, $indx) {
		$users = array();
		
		//fetch user data.
		$this->db->select('id, Username, Post_Count, Custom_Title')
		  ->from('ebb_users');
		
		if (!is_null($order)) {
			$this->db->order_by($order);
		}
		
		if (!is_null($page) && !is_null($indx)) {
			$this->db->limit($page, $indx);
		}

		$query = $this->db->get();

		//loop through data and bind to an array.
		foreach ($query->result() as $row) {
			$users[] = $row;
		}
		
		return $users;
	}
	
	/**
	 * Get a total count of records for the ebb_users table.
	 * @return integer
	 * @version 09/03/12
	 */
	public function countAll() {
		$this->db->select('id')->from('ebb_users');
		$query = $this->db->get();
		
		return $query->num_rows();
	}

	/**
	 * Creates a new user.
	 * @access Public
	 * @version 06/16/12
	 * @return integer New User ID
	*/
	public function CreateUser() {
		#setup values.
		$data = array(
		  'Username' => $this->getUserName(),
		  'Password' => $this->getPassword(),
		  'gid' => $this->getGid(),
		  'Email' => $this->getEmail(),
		  'Custom_Title' => $this->getCustomTitle(),
		  'PM_Notify' => $this->getPmNotify(),
		  'Hide_Email' => $this->getHideEmail(),
		  'MSN' => $this->getMSn(),
		  'AOL' => $this->getAol(),
		  'Yahoo' => $this->getYahoo(),
		  'ICQ' => $this->getIcq(),
		  'WWW' => $this->getWww(),
		  'Location' => $this->getLocation(),
		  'Avatar' => $this->getAvatar(),
		  'Sig' => $this->getSig(),
		  'Time_format' => $this->getTimeFormat(),
		  'Time_Zone' => $this->getTimeZone(),
		  'Date_Joined' => $this->getDateJoined(),
		  'IP' => $this->getIp(),
		  'Style' => $this->getStyle(),
		  'Language' => $this->getLanguage(),
		  'Post_Count' => $this->getPostCount(),
		  'last_post' => $this->getLastPost(),
		  'last_search' => $this->getLastSearch(),
		  'failed_attempts' => $this->getFailedAttempts(),
		  'active' => $this->getActive(),
		  'act_key' => $this->getActKey(),
		  'password_recovery_date' => $this->getPasswordRecoveryDate(),
		  'warning_level' => $this->getWarningLevel(),
		  'suspend_length' => $this->getSuspendLength(),
		  'suspend_time' => $this->getSuspendTime()
        );

		#add new user.
		$this->db->insert('ebb_users', $data);
		
		//get user id
		return $this->db->insert_id();
	}

	/**
	 * Update a current user.
	 * @param array $data The field(s) we want to modify. (NULL will update everything).
	 * @access Public
	*/
	public function UpdateUser($data = NULL) {
		
		#see if any specific field was defined.
		if (is_null($data)) {
			$data = array(
			  'Username' => $this->getUserName(),
			  'Password' => $this->getPassword(),
			  'gid' => $this->getGid(),
			  'Email' => $this->getEmail(),
			  'Custom_Title' => $this->getCustomTitle(),
			  'PM_Notify' => $this->getPmNotify(),
			  'Hide_Email' => $this->getHideEmail(),
			  'MSN' => $this->getMSn(),
			  'AOL' => $this->getAol(),
			  'Yahoo' => $this->getYahoo(),
			  'ICQ' => $this->getIcq(),
			  'WWW' => $this->getWww(),
			  'Location' => $this->getLocation(),
			  'Avatar' => $this->getAvatar(),
			  'Sig' => $this->getSig(),
			  'Time_format' => $this->getTimeFormat(),
			  'Time_Zone' => $this->getTimeZone(),
			  'Style' => $this->getStyle(),
			  'Language' => $this->getLanguage(),
			  'Post_Count' => $this->getPostCount(),
			  'last_post' => $this->getLastPost(),
			  'last_search' => $this->getLastSearch(),
			  'failed_attempts' => $this->getFailedAttempts(),
			  'active' => $this->getActive(),
			  'act_key' => $this->getActKey(),
			  'password_recovery_date' => $this->getPasswordRecoveryDate(),
			  'warning_level' => $this->getWarningLevel(),
			  'suspend_length' => $this->getSuspendLength(),
			  'suspend_time' => $this->getSuspendTime()
			  );
		}

		#update user.
		$this->db->where('id', $this->getId());
		$this->db->update('ebb_users', $data);
	}
	
	/**
	 * Removes a user and all associated data.
	 * @access Public
	 * @return boolean TRUE the user and all assoc. data is gone; FALSE, an error occurred.
	*/
	public function DeleteUser() {
		// -------------------------------------------- /
		// Delete Topic & Replies by user.
		// -------------------------------------------- /
		$this->db->select('tid')
		  ->from('ebb_topics')
		  ->where('author', $this->getId());
		$topicData = $this->db->get();
		
		//see if we have any records to delete.
		if($topicData->num_rows() > 0) {
			foreach ($topicData->result() as $topicRow) {
				
				// -------------------------------------------- /
				// Delete post data
				// -------------------------------------------- /
				$this->db->select('pid')
				  ->from('ebb_posts')
				  ->where('tid', $topicRow->tid);
				$rQuery = $this->db->get();

				//see if we have any replies to delete.
				if($rQuery->num_rows() > 0) {
					//loop through data and clear attachments.
					foreach ($rQuery->result() as $replyRow) {
						$this->db->select('Filename')
						  ->from('ebb_attachments')
						  ->where('pid', $replyRow->pid);
						$aQuery = $this->db->get();

						//see if we have any records to delete.
						if($aQuery->num_rows() > 0) {

							foreach ($aQuery->result() as $rAttachRow) {
								$success = unlink($this->config->item('upload_path').$rAttachRow->Filename);
								//see if the file successfully deleted.
								if ($success) {
									//remove entry from db.
									$this->db->where('pid', $replyRow->pid)
									  ->where('Filename', $rAttachRow->Filename)
									  ->delete('ebb_attachments');
								}
							}
						}
					}
					//delete all replies tied to defined topic id.
					$this->db->where('tid', $topicRow->tid)
					  ->delete('ebb_posts');
				}
				
				// -------------------------------------------- /
				// Delete topic subscriptions
				// -------------------------------------------- /
				$this->db->where('tid', $topicRow->tid)
					  ->delete('ebb_topic_watch');
				
				// -------------------------------------------- /
				// Delete topic read data
				// -------------------------------------------- /
				$this->db->where('Topic', $topicRow->tid)
					  ->delete('ebb_read_topic');

				// -------------------------------------------- /
				// Delete polls & votes
				// -------------------------------------------- /
				$this->db->where('tid', $topicRow->tid)
				  ->delete('ebb_poll');

				$this->db->where('tid', $topicRow->tid)
				  ->delete('ebb_votes');
				
				// -------------------------------------------- /
				// Delete topic data
				// -------------------------------------------- /
				$this->db->select('Filename')
				  ->from('ebb_attachments')
				  ->where('tid', $topicRow->tid);
				$topicAttachs = $this->db->get();
				
				//see if we have any records to delete.
				if($topicAttachs->num_rows() > 0) {
					foreach ($topicAttachs->result() as $tAttachRow) {
						$tSuccess = unlink($this->config->item('upload_path').$tAttachRow->Filename);

						//see if the file successfully deleted.
						if ($tSuccess) {
							//remove entry from db.
							$this->db->where('tid', $topicRow->tid)
							  ->where('Filename', $tAttachRow->Filename)
							  ->delete('ebb_attachments');
						} else {
							//log this error.
							log_message('error', 'Was unable to delete the file '.$this->config->item('upload_path').$tAttachRow->Filename); //log error in error log.
						}
					}

					$this->db->where('tid', $topicRow->tid)
					  ->delete('ebb_topics');
				} else {
					//no attachments, so just delete the topic.
					$this->db->where('tid', $topicRow->tid)
					  ->delete('ebb_topics');
				}

			}
		}
		
		// -------------------------------------------- /
		// Delete post data
		// -------------------------------------------- /
		$this->db->select('pid')
		  ->from('ebb_posts')
		  ->where('author', $this->getId());
		$rQuery2 = $this->db->get();

		//see if we have any replies to delete.
		if($rQuery2->num_rows() > 0) {
			//loop through data and clear attachments.
			foreach ($rQuery2->result() as $replyRow) {
				$this->db->select('Filename')
				  ->from('ebb_attachments')
				  ->where('pid', $replyRow->pid);
				$aQuery = $this->db->get();

				//see if we have any records to delete.
				if($aQuery->num_rows() > 0) {

					foreach ($aQuery->result() as $rAttachRow) {
						$success = unlink($this->config->item('upload_path').$rAttachRow->Filename);
						//see if the file successfully deleted.
						if ($success) {
							//remove entry from db.
							$this->db->where('pid', $replyRow->pid)
							  ->where('Filename', $rAttachRow->Filename)
							  ->delete('ebb_attachments');
						}
					}
				}
			}
			//delete all replies tied to defined user id.
			$this->db->where('author', $this->getId())
			  ->delete('ebb_posts');
		}
		
		// -------------------------------------------- /
		// Delete any admin logged actions
		// -------------------------------------------- /
		$this->db->where('User', $this->getId())
		  ->delete('ebb_cplog');
		
		// -------------------------------------------- /
		// Delete any membership requests
		// -------------------------------------------- /
		$this->db->where('username', $this->getId())
		  ->delete('ebb_group_member_request');
		
		// -------------------------------------------- /
		// Delete any active login sessions
		// -------------------------------------------- /
		$this->db->where('username', $this->getId())
		  ->delete('ebb_login_session');
		
		// -------------------------------------------- /
		// Delete online status
		// -------------------------------------------- /
		$this->db->where('Username', $this->getId())
		  ->delete('ebb_online');
		
		// -------------------------------------------- /
		// Delete any private messages
		// -------------------------------------------- /
		$this->db->where('Sender', $this->getId())->or_where('Receiver', $this->getId())
		  ->delete('ebb_pm');
		
		// -------------------------------------------- /
		// Delete read topic status
		// -------------------------------------------- /
		$this->db->where('User', $this->getId())
		  ->delete('ebb_read_topic');
		
		// -------------------------------------------- /
		// Delete any relationships
		// -------------------------------------------- /
		$this->db->where('username', $this->getId())->or_where('uid', $this->getId())
		  ->delete('ebb_relationship');
		
		// -------------------------------------------- /
		// Delete any subscriptions
		// -------------------------------------------- /
		$this->db->where('username', $this->getId())
		  ->delete('ebb_topic_watch');
		
		// -------------------------------------------- /
		// Delete any votes casted
		// -------------------------------------------- /
		$this->db->where('Username', $this->getId())
		  ->delete('ebb_votes');
		
		// -------------------------------------------- /
		// Delete any warn logs
		// -------------------------------------------- /
		$this->db->where('Username', $this->getId())->or_where('Authorized', $this->getId())
		  ->delete('ebb_warnlog');
		
		// -------------------------------------------- /
		// Delete user now that all associated data is gone
		// -------------------------------------------- /
		$this->db->where('id', $this->getId())
		  ->delete('ebb_users');
		return TRUE;
	}
	
}