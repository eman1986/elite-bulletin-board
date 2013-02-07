<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * boardaccessmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright  (c) 2006-2011
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @verson 07/02/2012
*/

/**
 * Board Access Entity
 */
class Boardaccessmodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/

	private $bRead;
	private $bPost;
	private $bReply;
	private $bVote;
	private $bPoll;
	private $bDelete;
	private $bEdit;
	private $bAttachment;
	private $bId;

	public function __construct() {
        parent::__construct();
    }

	/**
	 * PROPERTIES
	*/

	/**
	 * set value for B_Read 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bRead
	 * @return EbbBoardAccessmodel
	 */
	public function &setBRead($bRead) {
		$this->bRead=$bRead;
		return $this;
	}

	/**
	 * get value for B_Read 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBRead() {
		return $this->bRead;
	}

	/**
	 * set value for B_Post 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bPost
	 * @return EbbBoardAccessmodel
	 */
	public function &setBPost($bPost) {
		$this->bPost=$bPost;
		return $this;
	}

	/**
	 * get value for B_Post 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBPost() {
		return $this->bPost;
	}

	/**
	 * set value for B_Reply 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bReply
	 * @return EbbBoardAccessmodel
	 */
	public function &setBReply($bReply) {
		$this->bReply=$bReply;
		return $this;
	}

	/**
	 * get value for B_Reply 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBReply() {
		return $this->bReply;
	}

	/**
	 * set value for B_Vote 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bVote
	 * @return EbbBoardAccessmodel
	 */
	public function &setBVote($bVote) {
		$this->bVote=$bVote;
		return $this;
	}

	/**
	 * get value for B_Vote 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBVote() {
		return $this->bVote;
	}

	/**
	 * set value for B_Poll 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bPoll
	 * @return EbbBoardAccessmodel
	 */
	public function &setBPoll($bPoll) {
		$this->bPoll=$bPoll;
		return $this;
	}

	/**
	 * get value for B_Poll 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBPoll() {
		return $this->bPoll;
	}

	/**
	 * set value for B_Delete 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bDelete
	 * @return EbbBoardAccessmodel
	 */
	public function &setBDelete($bDelete) {
		$this->bDelete=$bDelete;
		return $this;
	}

	/**
	 * get value for B_Delete
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBDelete() {
		return $this->bDelete;
	}
	
	/**
	 * set value for B_Edit
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $bEdit
	 * @return EbbBoardAccessmodel
	 */
	public function &setBEdit($bEdit) {
		$this->bEdit=$bEdit;
		return $this;
	}

	/**
	 * get value for B_Edit 
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	 */
	public function getBEdit() {
		return $this->bEdit;
	}

	/**
	 * set value for B_Attachment 
	 *
	 * type:BIT,size:0,default:null
	 *
	 * @param mixed $bAttachment
	 * @return EbbBoardAccessmodel
	 */
	public function &setBAttachment($bAttachment) {
		$this->bAttachment=$bAttachment;
		return $this;
	}

	/**
	 * get value for B_Attachment 
	 *
	 * type:BIT,size:0,default:null
	 *
	 * @return mixed
	 */
	public function getBAttachment() {
		return $this->bAttachment;
	}

	/**
	 * set value for B_id 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0,primary,unique
	 *
	 * @param mixed $bId
	 * @return EbbBoardAccessmodel
	 */
	public function &setBId($bId) {
		$this->bId=$bId;
		return $this;
	}

	/**
	 * get value for B_id 
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0,primary,unique
	 *
	 * @return mixed
	 */
	public function getBId() {
		return $this->bId;
	}

	/**
	 * METHODS
	*/

	/**
	 * Loads Entity weith data from database.
	 * @param int $bid BoardID
	 * @version 06/17/12
	 * @return boolean
	 */
	public function GetBoardAccess($bid) {

		//fetch board access data.
		$this->db->select('B_Read, B_Post, B_Reply, B_Vote, B_Poll, B_Delete, B_Edit, B_Attachment, B_id');
		$this->db->from('ebb_board_access');
		$this->db->where('B_id', $bid);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
			$BoardAccessData = $query->row();
			
			//setup property values.
			$this->setBId($BoardAccessData->B_id);
			$this->setBRead($BoardAccessData->B_Read);
			$this->setBPost($BoardAccessData->B_Post);
			$this->setBReply($BoardAccessData->B_Reply);
			$this->setBVote($BoardAccessData->B_Vote);
			$this->setBPoll($BoardAccessData->B_Poll);
			$this->setBDelete($BoardAccessData->B_Delete);
			$this->setBEdit($BoardAccessData->B_Edit);
			$this->setBAttachment($BoardAccessData->B_Attachment);
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
