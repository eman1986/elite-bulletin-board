<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * spamlistmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/16/2013
*/

class Spamlistmodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/

	private $id;
	private $spamWord;
	
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
	 */
	public function setId($id) {
		$this->id = $id;
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
	 * set value for spam_word 
	 *
	 * type:VARCHAR,size:255,default:null
	 *
	 * @param mixed $spamWord
	 */
	public function setSpamWord($spamWord) {
		$this->spamWord = $spamWord;
	}

	/**
	 * get value for spam_word 
	 *
	 * type:VARCHAR,size:255,default:null
	 *
	 * @return mixed
	 */
	public function getSpamWord() {
		return $this->spamWord;
	}

	/**
	 * METHODS
	*/
	
	/**
	 * List all records from ebb_spam_list table.
	 * @param string $order The column and direction to sort data by.
	 * @param integer $page how many records to show per page.
	 * @param integer $indx where to begin the data range.
	 * @return array the data from ebb_spam_list.
	*/
	public function ListAll($order, $page, $indx) {
		$spamList = array();
		
		//fetch user data.
		$this->db->select('id, spam_word')
		  ->from('ebb_spam_list');
		
		if (!is_null($order)) {
			$this->db->order_by($order);
		}
		
		if (!is_null($page) && !is_null($indx)) {
			$this->db->limit($page, $indx);
		}

		$query = $this->db->get();

		//loop through data and bind to an array.
		foreach ($query->result() as $row) {
			$spamList[] = $row;
		}
		
		return $spamList;
	}
	
	/**
	 * Get a total count of records for the ebb_spam_list table.
	 * @return integer
	*/
	public function countAll() {
		$this->db->select('id')->from('ebb_spam_list');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	/**
	 * Creates a new spam word.
	 * @return integer New ID
	*/
	public function createSpamEntry() {
		#setup values.
		$data = array(
		  'spam_word' => $this->getSpamWord()
        );

		#add new user.
		$this->db->insert('ebb_spam_list', $data);
		
		//get user id
		return $this->db->insert_id();
	}
	
	/**
	 * Update Spam word.
	*/
	public function updateSpamEntry() {
		#setup values.
		$data = array(
		  'spam_word' => $this->getSpamWord()
        );

		#update user.
		$this->db->where('id', $this->getId());
		$this->db->update('ebb_spam_list', $data);
	}
	
	/**
	 * Deletes spam word from database.
	 * @return boolean TRUE the record deleted successfully; FALSE, the record failed to delete.
	*/
	public function deleteSpamEntry() {
		try {
			$this->db->where('id', $this->getId())->delete('ebb_spam_list');
			return TRUE;
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
			return FALSE;
		}
	}
	
	/**
	 * Get a spam word entry from database.
	 * @param integer $id The ID of the spam word entry.
	 * @return boolean TRUE, the spam word was found and loaded; FALSE, No such records exists.
	*/
	public function getSpamData($id) {
		$this->db->select('id, spam_word')->from('ebb_spam_list')->where('id', $id);
		$spamQ = $this->db->get();
		
		if ($spamQ->num_rows() > 0) {
			$spamListData = $spamQ->row();

			//populate properties with values.
			$this->setId($spamListData->id);
			$this->setSpamWord($spamListData->spam_word);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Validate that a string doesn't contain any spamish words.
	 * @param string $str the string to validate
	 * @return boolean TRUE, string contains a banned word; FALSE, no banned words were detected.
	*/
	public function searchSpamList($str) {
		#grab our banned words..
		$this->db->select('id')->from('ebb_spam_list')->like('spam_word', $str);
		$spamQ = $this->db->get();
		
		//see if anything is listed as spam.
		if ($spamQ->num_rows() > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}