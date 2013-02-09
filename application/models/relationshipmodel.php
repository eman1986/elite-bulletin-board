<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * topicmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 02/08/2013
*/
class Relationshipmodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/
	
	private $rid;
	private $userName;
	private $uId;
	private $status;
	
    public function __construct() {
        parent::__construct();
    }

	/**
	 * PROPERTIES
	*/

	/**
	 * set value for rid 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @param mixed $rid
	 */
	public function setRid($rid) {
		$this->rid=$rid;
	}

	/**
	 * get value for rid 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @return mixed
	 */
	public function getRid() {
		return $this->rid;
	}

	/**
	 * set value for username 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null
	 *
	 * @param mixed $userName
	 */
	public function setUserName($userName) {
		$this->userName=$userName;
	}

	/**
	 * get value for username 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null
	 *
	 * @return mixed
	 */
	public function getUserName() {
		return $this->userName;
	}

	/**
	 * set value for uid 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null
	 *
	 * @param mixed $uId
	 */
	public function setUId($uId) {
		$this->uId=$uId;
	}

	/**
	 * get value for uid 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null
	 *
	 * @return mixed
	 */
	public function getUId() {
		return $this->uId;
	}

	/**
	 * set value for status 
	 *
	 * type:BIT,size:0,default:null
	 *
	 * @param mixed $status
	 */
	public function setStatus($status) {
		$this->status=$status;
	}

	/**
	 * get value for status 
	 *
	 * type:BIT,size:0,default:null
	 *
	 * @return mixed
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * METHODS
	*/

	/**
	 * Get a list of users marked as foe.
	 * @param integer $uid The user we wish to see a foe list for.
	 * @return array an array of foes.
	*/
	public function getFoeListByUser($uid) {
		$foeList = array();
		
		$this->db->select('username')
		  ->from('ebb_relationship')
		  ->where('status', 2)
		  ->where('uid', $uid)
		  ->limit(1);
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$foeList[] = $row;
			}
		}
		return $foeList;
	}
	
	/**
	 * Get a list of users marked as friend.
	 * @param integer $uid The user we wish to see a friend list for.
	 * @return array an array of friends.
	*/
	public function getFriendListByUser($uid) {
		$friendList = array();
		
		$this->db->select('username')
		  ->from('ebb_relationship')
		  ->where('status', 1)
		  ->where('uid', $uid)
		  ->limit(1);
		$query = $this->get();		
		
		if ($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$friendList[] = $row;
			}
		}
		return $friendList;
	}
	
	/**
	 * See if a user is banned by the defined user.
	 * @param integer $uid User that has the request about friendship
	 * @param integer $bUser User we want to see if their banned.
	 * @return boolean TRUE, user is blocked; FALSE, user is not blocked.
	*/
	public function IsBannedByUser($uid, $bUser) {
		$this->db->select('rid')
		  ->from('ebb_relationship')
		  ->where('status', 2)
		  ->where('username', $bUser)
		  ->where('uid', $uid)
		  ->limit(1);
		$query = $this->db->get();
		
		if ($query->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Create a new relationship between two users.
	 * @return integer the relationship ID of the new record.
	*/
	public function CreateRelationship() {
		#setup values.
		$data = array(
		  'username' => $this->getUserName(),
		  'uid' => $this->getUId(),
		  'status' => $this->getStatus()
        );

		#add new topic.
		$this->db->insert('ebb_relationship', $data);
		
		//get rid
		return $this->db->insert_id();
	}
	
	/**
	 * Remove a relationship between another user.
	 * @param integer $id The User ID to dissolve as a friend/foe.
	*/
	public function RemoveRelationship($id) {
		$this->db->delete('ebb_relationship', array('username' => $id));
	}
	
	/**
	 * Update the status of your friendship with another user.
	 * @param integer $id The User ID to update as a friend/foe.
	*/
	public function updateRelationship($id, $status) {
		$this->db->where('id', $id);
		$this->db->update('ebb_relationship', array('status' => $status));
	}
}