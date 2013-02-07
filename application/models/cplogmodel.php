<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * cplogmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 01/07/2013
*/

class Cplogmodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/
	
	private $id;
	private $user;
	private $action;
	private $date;
	private $ip;

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
	 * set value for User 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null
	 *
	 * @param mixed $user
	*/
	public function setUser($user) {
		$this->user=$user;
	}

	/**
	 * get value for User 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null
	 *
	 * @return mixed
	*/
	public function getUser() {
		return $this->user;
	}

	/**
	 * set value for Action 
	 *
	 * type:VARCHAR,size:255,default:null
	 *
	 * @param mixed $action
	*/
	public function setAction($action) {
		$this->action=$action;
	}

	/**
	 * get value for Action 
	 *
	 * type:VARCHAR,size:255,default:null
	 *
	 * @return mixed
	*/
	public function getAction() {
		return $this->action;
	}

	/**
	 * set value for Date 
	 *
	 * type:VARCHAR,size:14,default:
	 *
	 * @param mixed $date
	 */
	public function setDate($date) {
		$this->date=$date;
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
	 * set value for IP 
	 *
	 * type:VARCHAR,size:45,default:
	 *
	 * @param mixed $ip
	*/
	public function setIp($ip) {
		$this->ip=$ip;
	}

	/**
	 * get value for IP 
	 *
	 * type:VARCHAR,size:45,default:
	 *
	 * @return mixed
	*/
	public function getIp() {
		return $this->ip;
	}
	
	/**
	 * METHODS
	*/

	/**
	 * Logs an administration action.
	 * @return integer the record ID.
	*/
	public function logAction() {
		#setup values.
		$data = array(
		  'User' => $this->getUser(),
		  'Action' => $this->getAction(),
		  'Date' => $this->getDate(),
		  'IP' => $this->getIp()
        );

		#add new topic.
		$this->db->insert('ebb_cplog', $data);
		
		//get tid
		return $this->db->insert_id();
	}
	
	/**
	 * List all records from ebb_cplog table.
	 * @param string $tZone The user's time zone.
	 * @param string $dtFormat The user's date/time format.
	 * @param string $order The column and direction to sort data by.
	 * @param integer $page how many records to show per page.
	 * @param integer $indx where to begin the data range.
	 * @return array the data from ebb_cplog.
	*/
	public function listAll($tZone, $dtFormat, $order, $page, $indx) {
		$audit = array();
		
		//fetch topic data.
		$this->db->select('u.Username, l.IP, l.Date, l.Action')
		  ->from('ebb_cplog l')
		  ->join('ebb_users u', 'l.User=u.id', 'LEFT');
		
		if (!is_null($order)) {
			if ($order == "formattedDateTime DESC") {
				$this->db->order_by('Date DESC');
			} elseif ($order == "formattedDateTime ASC") {
				$this->db->order_by('Date ASC');
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
			$row->formattedDateTime = datetimeFormatter($row->Date, $dtFormat, $tZone);
			$audit[] = $row;
		}
		
		return $audit;
	}
	
	/**
	 * Get a total count of records for the ebb_cplog table.
	 * @return integer
	*/
	public function countAll() {
		$this->db->select('id')
		  ->from('ebb_cplog');
		
		$query = $this->db->get();
		
		return $query->num_rows();
	}
	
	/**
	 * Clears the ebb_cplog table.
	*/
	public function clearAll() {
		$this->db->truncate('ebb_cplog'); 
	}

}