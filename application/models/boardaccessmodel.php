<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * boardaccessmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @verson 02/15/2013
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
	 * Create board access rules
	*/
	public function CreateAccessRules() {
		
		$data = array(
		  'B_Read' => $this->getBRead(),
		  'B_Post' => $this->getBPost(),
		  'B_Reply' => $this->getBReply(),
		  'B_Vote' => $this->getBVote(),
		  'B_Poll' => $this->getBPoll(),
		  'B_Attachment' => $this->getBAttachment(),
		  'B_id' => $this->getBId()
		);

		#create new set of rules for board.
		$this->db->insert('ebb_board_access', $data);

		return TRUE;
	}

	/**
	 * Update board access rules
	 * @param array $data new data for record.
	*/
	public function UpdateAccessRules($data = NULL) {
		#see if any specific field was defined.
		if (is_null($data)) {
			$data = array(
			  'B_Read' => $this->getBRead(),
			  'B_Post' => $this->getBPost(),
			  'B_Reply' => $this->getBReply(),
			  'B_Vote' => $this->getBVote(),
			  'B_Poll' => $this->getBPoll(),
			  'B_Attachment' => $this->getBAttachment()
			);
		}
		
		#update board access.
		$this->db->where('B_id', $this->getBId());
		$this->db->update('ebb_board_access', $data);
	}
	
	/**
	 * Loads Entity weith data from database.
	 * @param int $bid BoardID
	 * @return boolean
	 */
	public function GetBoardAccess($bid) {

		//fetch board access data.
		$this->db->select('B_Read, B_Post, B_Reply, B_Vote, B_Poll, B_Attachment, B_id');
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
			$this->setBAttachment($BoardAccessData->B_Attachment);
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
