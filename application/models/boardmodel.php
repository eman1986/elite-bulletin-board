<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * boardmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 12/23/2012
*/

/**
 * Board Entity
 */
class Boardmodel extends CI_Model {
	
	/**
	 * DATA MEMBERS
	*/

	private $id;
	private $board;
	private $description;
	private $lastUpdate;
	private $postedUser;
    private $tiD;
    private $piD;
    private $lastPage;
	private $type;
	private $category;
	private $smiles;
	private $bbCode;
	private $postIncrement;
	private $image;
	private $bOrder;

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
	 * @return EbbBoardsmodel
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
	 * set value for Board 
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @param mixed $board
	 * @return EbbBoardsmodel
	 */
	public function &setBoard($board) {
		$this->board=$board;
		return $this;
	}

	/**
	 * get value for Board 
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @return mixed
	 */
	public function getBoard() {
		return $this->board;
	}

	/**
	 * set value for Description 
	 *
	 * type:VARCHAR,size:100,default:null
	 *
	 * @param mixed $description
	 * @return EbbBoardsmodel
	 */
	public function &setDescription($description) {
		$this->description=$description;
		return $this;
	}

	/**
	 * get value for Description 
	 *
	 * type:VARCHAR,size:100,default:null
	 *
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * set value for last_update 
	 *
	 * type:VARCHAR,size:14,default:null,nullable
	 *
	 * @param mixed $lastUpdate
	 * @return EbbBoardsmodel
	 */
	public function &setLastUpdate($lastUpdate) {
		$this->lastUpdate=$lastUpdate;
		return $this;
	}

	/**
	 * get value for last_update 
	 *
	 * type:VARCHAR,size:14,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getLastUpdate() {
		return $this->lastUpdate;
	}

	/**
	 * set value for Posted_User 
	 *
	 * type:VARCHAR,size:25,default:null,nullable
	 *
	 * @param mixed $postedUser
	 * @return EbbBoardsmodel
	 */
	public function &setPostedUser($postedUser) {
		$this->postedUser=$postedUser;
		return $this;
	}

	/**
	 * get value for Posted_User 
	 *
	 * type:VARCHAR,size:25,default:null,nullable
	 *
	 * @return mixed
	 */
	public function getPostedUser() {
		return $this->postedUser;
	}

	/**
	 * set value for tid
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @param mixed $tiD
	 * @return Topicmodel
	*/
	public function &setTiD($tiD) {
		$this->tiD=$tiD;
		return $this;
	}

	/**
	 * get value for tid
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @return mixed
	*/
	public function getTiD() {
		return $this->tiD;
	}

	/**
	 * set value for pid
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @param mixed $tiD
	 * @return Topicmodel
	*/
	public function &setPiD($piD) {
		$this->piD=$piD;
		return $this;
	}

	/**
	 * get value for pid
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @return mixed
	*/
	public function getPiD() {
		return $this->piD;
	}

	/**
	 * set value for last_page
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $type
	 * @return EbbBoardsmodel
	 */
	public function &setLastPage($lastPage) {
		$this->lastPage=$lastPage;
		return $this;
	}

	/**
	 * get value for last_page
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getLastPage() {
		return $this->lastPage;
	}

	/**
	 * set value for type 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $type
	 * @return EbbBoardsmodel
	 */
	public function &setType($type) {
		$this->type=$type;
		return $this;
	}

	/**
	 * get value for type 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * set value for Category 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0
	 *
	 * @param mixed $category
	 * @return EbbBoardsmodel
	 */
	public function &setCategory($category) {
		$this->category=$category;
		return $this;
	}

	/**
	 * get value for Category 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0
	 *
	 * @return mixed
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * set value for Smiles 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $smiles
	 * @return EbbBoardsmodel
	 */
	public function &setSmiles($smiles) {
		$this->smiles=$smiles;
		return $this;
	}

	/**
	 * get value for Smiles 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getSmiles() {
		return $this->smiles;
	}

	/**
	 * set value for BBcode 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bbCode
	 * @return EbbBoardsmodel
	 */
	public function &setBbCode($bbCode) {
		$this->bbCode=$bbCode;
		return $this;
	}

	/**
	 * get value for BBcode 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBbCode() {
		return $this->bbCode;
	}

	/**
	 * set value for Post_Increment 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $postIncrement
	 * @return EbbBoardsmodel
	 */
	public function &setPostIncrement($postIncrement) {
		$this->postIncrement=$postIncrement;
		return $this;
	}

	/**
	 * get value for Post_Increment 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getPostIncrement() {
		return $this->postIncrement;
	}

	/**
	 * set value for Image 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $image
	 * @return EbbBoardsmodel
	 */
	public function &setImage($image) {
		$this->image=$image;
		return $this;
	}

	/**
	 * get value for Image 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * set value for B_Order 
	 *
	 * type:TINYINT,size:3,default:0
	 *
	 * @param mixed $bOrder
	 * @return EbbBoardsmodel
	 */
	public function &setBOrder($bOrder) {
		$this->bOrder=$bOrder;
		return $this;
	}

	/**
	 * get value for B_Order 
	 *
	 * type:TINYINT,size:3,default:0
	 *
	 * @return mixed
	 */
	public function getBOrder() {
		return $this->bOrder;
	}

	/**
	 * METHODS
	*/

	public function CreateBoard() {

	}

	public function EditBoard() {

	}

	public function DeleteBoard() {

	}
	
	/**
	 * Get a list of categories & boards to show on the index page.
	 * @return array an array of both the parent boards & the child boards.
	*/
	public function loadBoardIndex() {
		$parent = $child = array();

		//SQL CI style.
		$this->db->select('id, Board')->from('ebb_boards')->where('type', 1)->order_by("B_Order", "asc");
		$query = $this->db->get();
		foreach ($query->result() as $row) {

        	$parent[] = $row;

			//build second query.
			$this->db->select('b.id, b.Board, b.Description, b.last_update, u.Username, b.tid, b.last_page, b.Category')
			  ->from('ebb_boards b')
			  ->join('ebb_users u', 'b.Posted_User=u.id', 'LEFT')
			  ->where('b.type', 2)
			  ->where('b.Category',$row->id)
			  ->order_by("b.B_Order", "asc");
			$query2 = $this->db->get();
			foreach ($query2->result() as $row2) {

				#board rules sql.
				$this->Boardaccessmodel->GetBoardAccess($row2->id);

				#see if user can view the board.
				if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBRead())){
					$child[] = $row2;
				}

			}
		}
		return array("Parent_Boards" => $parent, "Child_Boards" => $child);
	}

    /**
     * Load Entity with values from our database.
     * @param integer $bid
     * @version 06/17/12
	 * @return boolean
     */
	public function GetBoardSettings($bid) {

        //fetch board data.
		$this->db->select('id, Board, Description, last_update, Posted_User, tid, pid, last_page, type, Category, Smiles, BBCode, Post_Increment, Image, B_Order');
		$this->db->from('ebb_boards');
		$this->db->where('id', $bid);
		$query = $this->db->get();		

		//see if we have any records to show.
		if($query->num_rows() > 0) {
			$BoardData = $query->row();
			
			//setup property values.
			$this->setId($BoardData->id);
			$this->setBoard($BoardData->Board);
			$this->setDescription($BoardData->Description);
			$this->setLastUpdate($BoardData->last_update);
			$this->setPostedUser($BoardData->Posted_User);
			$this->setTiD($BoardData->tid);
			$this->setPiD($BoardData->pid);
			$this->setLastPage($BoardData->last_page);
			$this->setType($BoardData->type);
			$this->setCategory($BoardData->Category);
			$this->setSmiles($BoardData->Smiles);
			$this->setBbCode($BoardData->BBCode);
			$this->setPostIncrement($BoardData->Post_Increment);
			$this->setImage($BoardData->Image);
			$this->setBOrder($BoardData->B_Order);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Get an array of sub-boards.
	 * @param int $boardID
	 * @return array
	 * @version 07/16/12
	*/
	public function GetSubBoards($boardID) {

		//setup reply data array.
		$subboards = array();

		#board sql.
		$this->db->select('b.id, b.Board, b.Description, b.last_update, u.Username, b.tid, b.last_page')
		  ->from('ebb_boards b')
		  ->join('ebb_users u', 'b.Posted_User=u.id', 'LEFT')
		  ->where('type', 3)
		  ->where('Category', $boardID)
		  ->order_by("B_Order", "asc");
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$subboards[] = $row;
			}

			return $subboards;
		} else {
			return FALSE;
		}
	}

	/**
	 * Get a list of all replies to a topic.
	 * @version 07/16/12
	 * @param int $bid Topic ID.
	 * @param int $limit amount to show per page.
	 * @param int $start what entry to start from.
	 * @access public
	 * @return array
	*/
	public function GetTopics($bid, $limit, $start) {

		//build data array.
		$topics = array();

		//SQL to get all topics from defined board.
		$this->db->select('t.bid, t.last_update, t.Topic, u.Username, t.posted_user, t.last_page, t.pid, t.tid, t.Views, t.topic_type, t.important, t.Locked')
		  ->from('ebb_topics t')
		  ->join('ebb_users u', 't.author=u.id', 'LEFT')
		  ->where('bid', $bid)
		  ->order_by('last_update', 'desc')
		  ->limit($limit, $start);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {

			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$topics[] = $row;
			}

			return $topics;
		} else {
			return FALSE;
		}
	}

}
