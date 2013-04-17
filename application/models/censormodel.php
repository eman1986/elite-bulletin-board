<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * censormodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/16/2013
*/

class Censormodel extends CI_Model {
	/**
	 * DATA MEMBERS
	*/

	private $id;
	private $originalWord;
	
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
		$this->id=$id;
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
	 * set value for Original_Word 
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @param mixed $originalWord
	 */
	public function setOriginalWord($originalWord) {
		$this->originalWord=$originalWord;
	}

	/**
	 * get value for Original_Word 
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @return mixed
	 */
	public function getOriginalWord() {
		return $this->originalWord;
	}

	/**
	 * METHODS
	*/
	
	/**
	 * List all records from ebb_censor table.
	 * @param string $order The column and direction to sort data by.
	 * @param integer $page how many records to show per page.
	 * @param integer $indx where to begin the data range.
	 * @return array the data from ebb_censor.
	*/
	public function ListAll($order, $page, $indx) {
		$spamList = array();
		
		//fetch user data.
		$this->db->select('id, Original_Word')
		  ->from('ebb_censor');
		
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
	 * Get a total count of records for the ebb_censor table.
	 * @return integer
	*/
	public function countAll() {
		$this->db->select('id')->from('ebb_censor');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	/**
	 * Creates a new censor word.
	 * @return integer New ID
	*/
	public function createCensorEntry() {
		#setup values.
		$data = array(
		  'Original_Word' => $this->getOriginalWord()
        );

		#add new user.
		$this->db->insert('ebb_censor', $data);
		
		//get user id
		return $this->db->insert_id();
	}
	
	/**
	 * Update censor word.
	*/
	public function updateCensorEntry() {
		#setup values.
		$data = array(
		  'Original_Word' => $this->getOriginalWord()
        );

		#update user.
		$this->db->where('id', $this->getId());
		$this->db->update('ebb_censor', $data);
	}
	
	/**
	 * Deletes spam word from database.
	 * @return boolean TRUE the record deleted successfully; FALSE, the record failed to delete.
	*/
	public function deleteCensorEntry() {
		try {
			$this->db->where('id', $this->getId())->delete('ebb_censor');
			return TRUE;
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
			return FALSE;
		}
	}

	/**
	 * Get a censored word entry from database.
	 * @param integer $id The ID of the censored word entry.
	 * @return boolean TRUE, the censored word was found and loaded; FALSE, No such records exists.
	*/
	public function getCensorData($id) {
		$this->db->select('id, Original_Word')->from('ebb_censor')->where('id', $id);
		$censorQ = $this->db->get();
		
		if ($censorQ->num_rows() > 0) {
			$censoredWordtData = $censorQ->row();

			//populate properties with values.
			$this->setId($censoredWordtData->id);
			$this->setOriginalWord($censoredWordtData->Original_Word);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * See if a censored word was used in a string.
	 * @param string $string The string we wish to validate.
	 * @return string The string with the censored word(s) starred out.
	*/
	public function searchCensorList($string) {
		//@TODO consider using the word_censor() function from the text helper to prevent reinventing the wheel.
		$stars = '';
		
		$this->db->select('Original_Word')->from('ebb_censor');
		$censorQ = $this->db->get();
		
		foreach ($censorQ->result() as $row) {
			if (stristr(trim($string), $row->Original_Word)) {
				$length = strlen($row->Original_Word);
				for ($i = 1; $i <= $length; $i++) {
					$stars .= "*";
				}
				$string = str_ireplace($row->Original_Word, $stars, trim($string));
				$stars = "";
			}
		}

	   return $string;
	}
}