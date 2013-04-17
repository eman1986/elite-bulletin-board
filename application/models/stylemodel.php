<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * stylemodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 10/01/2012
*/

class Stylemodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/
	
	private $id;
	private $name;
	private $tempPath;

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
	 * @return Stylemodel
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
	 * type:VARCHAR,size:50,default:
	 *
	 * @param mixed $name
	 * @return Stylemodel
	 */
	public function &setName($name) {
		$this->name=$name;
		return $this;
	}

	/**
	 * get value for Name 
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * set value for Temp_Path 
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @param mixed $tempPath
	 * @return Stylemodel
	 */
	public function &setTempPath($tempPath) {
		$this->tempPath=$tempPath;
		return $this;
	}

	/**
	 * get value for Temp_Path 
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @return mixed
	 */
	public function getTempPath() {
		return $this->tempPath;
	}

	/**
	 * METHODS
	*/
	
	/**
	 * Grab style data.
	 * @param int $id StyleID
	 * @version 09/28/12
	 * @access public
	 * @return boolean
	*/
	public function GetStyleData($id) {

		//fetch topic data.
		$this->db->select('id, Name, Temp_Path')->from('ebb_style')->where('id', $id);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
		
			$StyleData = $query->row();

			//populate properties with values.
			$this->setId($StyleData->id);
			$this->setName($StyleData->Name);
			$this->setTempPath($StyleData->Temp_Path);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get a list of all styles.
	 * @version 10/01/12
	 * @return boolean
	*/
	public function GetStyles() {
		$styles = array();
		
		//fetch topic data.
		$this->db->select('id, Name, Temp_Path')->from('ebb_style');
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$styles[] = $row;
			}

			return $styles;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * List all records from ebb_topic_watch table.
	 * @param integer $uid User ID to filter by.
	 * @param string $order The column and direction to sort data by.
	 * @param integer $page how many records to show per page.
	 * @param integer $indx where to begin the data range.
	 * @return array the data from ebb_topic_watch.
	 * @version 09/22/12
	 */
	public function ListAll($order, $page, $indx) {
		$users = array();
		
		//fetch topic data.
		$this->db->select('id, Name')->from('ebb_style');
		
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
	 * Get a total count of records for the ebb_topic_watch table.
	 * @return integer
	 * @version 09/28/12
	*/
	public function countAll() {
		$this->db->select('id')->from('ebb_style');
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	/**
	 * Uninstall style from board.
	 * @return boolean TRUE, style uninstalled successfully; FALSE, it failed to uninstall.
	*/
	public function deleteStyle() {
		$this->db->select('id')->from('ebb_users')->where('Style', $this->getId());
		$styleinUseQuery = $this->db->get();
		
		if ($styleinUseQuery->num_rows() > 0) { //see if the selected style is still being used by someone.
			log_message('error', $this->lang->line('styleinuse')); //log error in error log.
			return FALSE;
		} else if ($this->countAll() == 1) { //see if the selected style is the only installed style.
			log_message('error', $this->lang->line('onestyleinstalled')); //log error in error log.
			return FALSE;
		} else {
			$this->db->where('id', $this->getId())->delete('ebb_style');
			return TRUE;
		}
	}
}