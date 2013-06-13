<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * pmmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 02/08/2013
*/

class Informationtickermodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/
	
	private $id;
	private $information;
	
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
	 * set value for information 
	 *
	 * type:VARCHAR,size:50,default:null
	 *
	 * @param mixed $information
	 */
	public function setInformation($information) {
		$this->information=$information;
	}

	/**
	 * get value for information 
	 *
	 * type:VARCHAR,size:50,default:null
	 *
	 * @return mixed
	 */
	public function getInformation() {
		return $this->information;
	}
	
	/**
	 * METHODS
	*/

	/**
	 * List all records from ebb_information_ticker table.
	 * @param string $order The column and direction to sort data by.
	 * @param integer $page how many records to show per page.
	 * @param integer $indx where to begin the data range.
	 * @return array the data from ebb_cplog.
	*/
	public function listAll($order, $page, $indx) {
		$audit = array();
		
		//fetch topic data.
		$this->db->select('id, information')
		  ->from('ebb_information_ticker');

		if (!is_null($order)) {
			$this->db->order_by($order);
		}
		
		if (!is_null($page) && !is_null($indx)) {
			$this->db->limit($page, $indx);
		}

		$query = $this->db->get();

		//loop through data and bind to an array.
		foreach ($query->result() as $row) {
			$audit[] = $row;
		}
		
		return $audit;
	}
	
	/**
	 * Get a total count of records for the ebb_information_ticker table.
	 * @return integer
	*/
	public function countAll() {
		$this->db->select('id')
		  ->from('ebb_information_ticker');
		
		$query = $this->db->get();
		
		return $query->num_rows();
	}
	
	/**
	 * Create a new Ticker record.
	 * @return integer The New ID
	*/
	public function create() {
		#setup values.
		$data = array(
		  'information' => $this->getInformation()
        );

		#add new message.
		$this->db->insert('ebb_information_ticker', $data);
		
		//get id
		return $this->db->insert_id();
	}
	
	/**
	 * Deletes the selected record from ebb_information_ticker.
	*/
	public function delete() {
		$this->db->where('id', $this->getId())
			  ->delete('ebb_information_ticker');
	}
	
}