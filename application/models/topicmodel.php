<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * topicmodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 12/19/2012
*/

/**
 * Topic Entity
 */
class Topicmodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/
	
	private $author;
	private $uid;
	private $tiD;
	private $piD;
	private $bid;
	private $topic;
	private $body;
	private $topicType;
	private $important;
	private $ip;
	private $originalDate;
	private $lastUpdate;
	private $postedUser;
	private $postLink;
	private $locked;
	private $views;
	private $question;
	private $disableBbCode;
	private $disableSmiles;
	private $email;
	private $author_gid;
	private $author_postcount;
	private $author_warninglevel;
	private $author_avatar;
	private $author_signature;
	private $author_ctitle;
	private $author_group_profile;
	private $author_group_access;
		
    public function __construct() {
        parent::__construct();
    }

	/**
	 * PROPERTIES
	*/

	/**
	 * set value for author
	 *
	 * type:VARCHAR,size:25,default:
	 *
	 * @param mixed $author
	 * @return Topicmodel
	*/
	public function &setAuthor($author) {
		$this->author=$author;
		return $this;
	}

	/**
	 * get value for author
	 *
	 * type:VARCHAR,size:25,default:
	 *
	 * @return mixed
	*/
	public function getAuthor() {
		return $this->author;
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
	 * @param mixed $piD
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
	 * set value for bid
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0
	 *
	 * @param mixed $bid
	 * @return Topicmodel
	*/
	public function &setBid($bid) {
		$this->bid=$bid;
		return $this;
	}

	/**
	 * get value for bid
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0
	 *
	 * @return mixed
	*/
	public function getBid() {
		return $this->bid;
	}

	/**
	 * set value for Topic
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @param mixed $topic
	 * @return Topicmodel
	*/
	public function &setTopic($topic) {
		$this->topic=$topic;
		return $this;
	}

	/**
	 * get value for Topic
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @return mixed
	*/
	public function getTopic() {
		return $this->topic;
	}

	/**
	 * set value for Body
	 *
	 * type:TEXT,size:65535,default:null
	 *
	 * @param mixed $body
	 * @return Topicmodel
	*/
	public function &setBody($body) {
		$this->body=$body;
		return $this;
	}

	/**
	 * get value for Body
	 *
	 * type:TEXT,size:65535,default:null
	 *
	 * @return mixed
	*/
	public function getBody() {
		return $this->body;
	}

	/**
	 * set value for topic_type
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $type
	 * @return Topicmodel
	*/
	public function &setTopicType($topicType) {
		$this->topicType = $topicType;
		return $this;
	}

	/**
	 * get value for topic_type
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	*/
	public function getTopicType() {
		return $this->topicType;
	}

	/**
	 * set value for important
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $important
	 * @return Topicmodel
	*/
	public function &setImportant($important) {
		$this->important=$important;
		return $this;
	}

	/**
	 * get value for important
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	*/
	public function getImportant() {
		return $this->important;
	}

	/**
	 * set value for IP
	 *
	 * type:VARCHAR,size:40,default:
	 *
	 * @param mixed $ip
	 * @return Topicmodel
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
	 * set value for Original_Date
	 *
	 * type:VARCHAR,size:14,default:
	 *
	 * @param mixed $originalDate
	 * @return Topicmodel
	*/
	public function &setOriginalDate($originalDate) {
		$this->originalDate=$originalDate;
		return $this;
	}

	/**
	 * get value for Original_Date
	 *
	 * type:VARCHAR,size:14,default:
	 *
	 * @return mixed
	*/
	public function getOriginalDate() {
		return $this->originalDate;
	}

	/**
	 * set value for last_update
	 *
	 * type:VARCHAR,size:14,default:
	 *
	 * @param mixed $lastUpdate
	 * @return Topicmodel
	*/
	public function &setLastUpdate($lastUpdate) {
		$this->lastUpdate=$lastUpdate;
		return $this;
	}

	/**
	 * get value for last_update
	 *
	 * type:VARCHAR,size:14,default:
	 *
	 * @return mixed
	*/
	public function getLastUpdate() {
		return $this->lastUpdate;
	}

	/**
	 * set value for Posted_User
	 *
	 * type:VARCHAR,size:25,default:
	 *
	 * @param mixed $postedUser
	 * @return Topicmodel
	*/
	public function &setPostedUser($postedUser) {
		$this->postedUser=$postedUser;
		return $this;
	}

	/**
	 * get value for Posted_User
	 *
	 * type:VARCHAR,size:25,default:
	 *
	 * @return mixed
	*/
	public function getPostedUser() {
		return $this->postedUser;
	}

	/**
	 * set value for Post_Link
	 *
	 * type:VARCHAR,size:100,default:
	 *
	 * @param mixed $postLink
	 * @return Topicmodel
	*/
	public function &setPostLink($postLink) {
		$this->postLink=$postLink;
		return $this;
	}

	/**
	 * get value for Post_Link
	 *
	 * type:VARCHAR,size:100,default:
	 *
	 * @return mixed
	*/
	public function getPostLink() {
		return $this->postLink;
	}

	/**
	 * set value for Locked
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $locked
	 * @return Topicmodel
	*/
	public function &setLocked($locked) {
		$this->locked=$locked;
		return $this;
	}

	/**
	 * get value for Locked
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	*/
	public function getLocked() {
		return $this->locked;
	}

	/**
	 * set value for Views
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0
	 *
	 * @param mixed $views
	 * @return Topicmodel
	*/
	public function &setViews($views) {
		$this->views=$views;
		return $this;
	}

	/**
	 * get value for Views
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:0
	 *
	 * @return mixed
	*/
	public function getViews() {
		return $this->views;
	}

	/**
	 * set value for Question
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @param mixed $question
	 * @return Topicmodel
	*/
	public function &setQuestion($question) {
		$this->question=$question;
		return $this;
	}

	/**
	 * get value for Question
	 *
	 * type:VARCHAR,size:50,default:
	 *
	 * @return mixed
	*/
	public function getQuestion() {
		return $this->question;
	}

	/**
	 * set value for disable_bbcode
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $disableBbCode
	 * @return Topicmodel
	*/
	public function &setDisableBbCode($disableBbCode) {
		$this->disableBbCode=$disableBbCode;
		return $this;
	}

	/**
	 * get value for disable_bbcode
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	*/
	public function getDisableBbCode() {
		return $this->disableBbCode;
	}

	/**
	 * set value for disable_smiles
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @param mixed $disableSmiles
	 * @return Topicmodel
	*/
	public function &setDisableSmiles($disableSmiles) {
		$this->disableSmiles=$disableSmiles;
		return $this;
	}

	/**
	 * get value for disable_smiles
	 *
	 * type:BIT,size:0,default:0
	 *
	 * @return mixed
	*/
	public function getDisableSmiles() {
		return $this->disableSmiles;
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
	 * set value for id
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @param mixed $uid
	 * @return Usermodel
	 */
	public function &setUId($uid) {
		$this->uid=$uid;
		return $this;
	}

	/**
	 * get value for id
	 *
	 * type:MEDIUMINT UNSIGNED,size:8,default:null,primary,unique,autoincrement
	 *
	 * @return mixed
	 */
	public function getUId() {
		return $this->uid;
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
		$this->author_gid=$gid;
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
		return $this->author_gid;
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
		$this->author_postcount=$postCount;
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
		return $this->author_postcount;
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
		$this->author_warninglevel=$warningLevel;
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
		return $this->author_warninglevel;
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
		$this->author_avatar=$avatar;
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
		return $this->author_avatar;
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
		$this->author_signature=$sig;
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
		return $this->author_signature;
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
		$this->author_ctitle=$customTitle;
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
		return $this->author_ctitle;
	}

	/**
	 * set value for profile
	 *
	 * type:VARCHAR,size:30,default:null
	 *
	 * @param mixed $groupProfile
	 * @return Groupmodel
	 */
	public function &setGroupProfile($groupProfile) {
		$this->author_group_profile=$groupProfile;
		return $this;
	}

	/**
	 * get value for profile
	 *
	 * type:VARCHAR,size:30,default:null
	 *
	 * @return mixed
	 */
	public function getGroupProfile() {
		return $this->author_group_profile;
	}

	/**
	 * set value for access_level
	 *
	 * type:TINYINT,size:1,default:null
	 *
	 * @param mixed $groupAccess
	 * @return Groupmodel
	 */
	public function &setGroupAccess($groupAccess) {
		$this->author_group_access=$groupAccess;
		return $this;
	}

	/**
	 * get value for access_level
	 *
	 * type:TINYINT,size:1,default:null
	 *
	 * @return mixed
	 */
	public function getGroupAccess() {
		return $this->author_group_access;
	}

	/**
	 * METHODS
	*/
	
	/**
	 * Creates a new topic
	 * @access public
	 * @version 05/23/12 
	 * @return integer
	*/
	public function CreateTopic() {
		#setup values.
		$data = array(
		  'author' => $this->getAuthor(),
		  'bid' => $this->getBid(),
		  'Topic' => $this->getTopic(),
		  'Body' => $this->getBody(),
		  'topic_type' => $this->getTopicType(),
		  'important' => $this->getImportant(),
		  'IP' => $this->getIp(),
		  'Original_Date' => $this->getOriginalDate(),
		  'last_update' => $this->getLastUpdate(),
		  'Posted_User' => $this->getPostedUser(),
		  'Locked' => $this->getLocked(),
		  'Views' => $this->getViews(),
		  'Question' => $this->getQuestion(),
		  'disable_bbcode' => $this->getDisableBbCode(),
		  'disable_smiles' => $this->getDisableSmiles()
        );

		#add new topic.
		$this->db->insert('ebb_topics', $data);
		
		//get tid
		return $this->db->insert_id();
	}
	
	/**
	 * Create poll option.
	 * @param string $optionValue The poll option value.
	 * @param integer $topicId Topic ID
	 * @access public
	 * @version 05/29/12
	*/
	public function CreatePoll($optionValue, $topicId) {
		#setup values.
		$data = array(
		  'option_value' => $optionValue,
		  'tid' => $topicId
        );

		#add new topic.
		$this->db->insert('ebb_poll', $data);
	}
	
	/**
	 * Creates a reply
	 * @return integer
	 * @version 06/05/12 
	*/
	public function CreateReply() {
		#setup values.
		$data = array(
		  'author' => $this->getAuthor(),
		  'bid' => $this->getBid(),
		  'tid' => $this->getTiD(),
		  'Body' => $this->getBody(),
		  'IP' => $this->getIp(),
		  'Original_Date' => $this->getOriginalDate(),
		  'disable_bbcode' => $this->getDisableBbCode(),
		  'disable_smiles' => $this->getDisableSmiles()
        );

		#add new topic.
		$this->db->insert('ebb_posts', $data);
		
		//get tid
		return $this->db->insert_id();
	}
	
	/**
	 * Update topic data.
	 * @version 06/13/12
	 */
	public function ModifyTopic() {
		#setup values.
		$data = array(
		  'Topic' => $this->getTopic(),
		  'Body' => $this->getBody(),
		  'important' => $this->getImportant(),
		  'disable_bbcode' => $this->getDisableBbCode(),
		  'disable_smiles' => $this->getDisableSmiles()
        );
		
		#update topic.
		$this->db->where('tid', $this->getTiD());
		$this->db->update('ebb_topics', $data);
	}
	
	/**
	 * Set a topic as locked or unlocked.
	 * @version 07/03/12
	 * @access public
	 * @return boolean 
	 */
	public function ToggleTopicLock() {
		if (!is_bool($this->getLocked())) {
			return FALSE;
		} else {
			#setup values.
			$data = array(
			  'Locked' => ($this->getLocked()) ? 1 : 0
			);

			#update topic.
			$this->db->where('tid', $this->getTiD());
			$this->db->update('ebb_topics', $data);
			return TRUE;
		}
	}
	
	/**
	 * Increase view count for topic.
	*/
	public function increaseTopicView() {
		
		#setup values.
		$data = array(
		  'Views' => ($this->getViews() + 1)
		);
		
		#update topic.
		$this->db->where('tid', $this->getTiD());
		$this->db->update('ebb_topics', $data);
	}
	
	
	/**
	 * Moves topic & replies to new defined board.
	 * @param integer $origBoard The previous Board ID
	 * @access public
	 * @version 07/10/12
	*/
	public function MoveTopic($origBoard) {
		#setup values.
		$tData = array(
		  'bid' => $this->getBid()
        );
		
		#update topic.
		$this->db->where('tid', $this->getTiD());
		$this->db->update('ebb_topics', $tData);
		
		$this->db->select('pid')
		  ->from('ebb_posts')
		  ->where('tid', $this->getTiD());
		$replyQuery = $this->db->get();
		
		//update the replies for this topic.
		foreach ($replyQuery->result() as $posts) {
			#setup values.
			$pData = array(
			'bid' => $this->getBid()
			);

			#update reply.
			$this->db->where('tid', $posts->pid);
			$this->db->update('ebb_posts', $pData);
		}
		
		//update ebb_boards last post data.
		$this->db->select('id')
		  ->from('ebb_boards')
		  ->where('id', $origBoard);
		$boardQuery = $this->db->get();
		
		//see if board has any previous topics, if so update to use that.
		if($boardQuery->num_rows() > 0) {
			#setup values.
			$bData = array(
			  'last_update' => null,
			  'Posted_User' => null,
			  'tid' => null
			);
		} else {
			$this->db->select('last_update, posted_user, tid')
			  ->from('ebb_topics')
			  ->where('bid', $origBoard)
			  ->order_by('last_update', 'desc')
			  ->limit(1);
			$topicQuery = $this->db->get();
			$TopicData = $topicQuery->row();
			
			#setup values.
			$bData = array(
			  'last_update' => $TopicData->last_update,
			  'Posted_User' => $TopicData->posted_user,
			  'tid' => $TopicData->tid
			);
		}
		
		#update old board location.
		$this->db->where('id', $origBoard);
		$this->db->update('ebb_boards', $bData);
		
		//see if new board location needs to get updated.
		$this->db->select('last_update')
		  ->from('ebb_boards')
		  ->where('id', $this->getBid());
		$newBoardQuery = $this->db->get();
		$newBoardData = $newBoardQuery->row();
		
		$this->db->select('last_update, posted_user, tid')
		  ->from('ebb_topics')
		  ->where('tid', $this->getTiD())
		  ->limit(1);
		$tQuery = $this->db->get();
		$tRes = $tQuery->row();
		
		//if no topic existed there before or the date is newer, update the board.
		if($newBoardQuery->num_rows() == 0 || $newBoardData->last_update < $tRes->last_update) {
			#setup values.
			$nbData = array(
			  'last_update' => $tRes->last_update,
			  'Posted_User' => $tRes->posted_user,
			  'tid' => $tRes->tid
			);
			
			#update new board location.
			$this->db->where('id', $this->getBid());
			$this->db->update('ebb_boards', $nbData);
		}
	}
	
	public function ModifyPoll() {
		//TODO: implement this either in RC 3.
	}
	
	/**
	 * Get a list of Smiles.
	 * @param boolean $showAll Do we want to show all?
	 * @param integer $limit how many do we want to show?
	 * @return array An array of smiles from database.
	*/
	public function ShowSmiles($showAll=false, $limit=30) {
	  $smiles = array();
	  
		$this->db->select('code, img_name')
		  ->from('ebb_smiles');
		
		//see if we want to limit the amount to show.
		if ($showAll) {
			$this->db->limit($limit);
		}
		
		$query = $this->db->get();
		
		foreach ($query->result() as $row) {
			$smiles[] = $row;
		}
		return $smiles;
	}
	
	/**
	 * Update reply data.
	 * @version 06/13/12
	 */
	public function ModifyReply() {
		#setup values.
		$data = array(
		  'Body' => $this->getBody(),
		  'disable_bbcode' => $this->getDisableBbCode(),
		  'disable_smiles' => $this->getDisableSmiles()
        );
		
		#update reply.
		$this->db->where('pid', $this->getPiD());
		$this->db->update('ebb_posts', $data);
	}
	
	/**
	 * Delete topic data.
	 * @version 06/18/12
	 * @return boolean
	 */
	public function DeleteTopic() {
		
		$this->db->select('Filename')
		  ->from('ebb_attachments')
		  ->where('tid', $this->getTiD());
		$query = $this->db->get();
		
		//see if we have any records to delete.
		if($query->num_rows() > 0) {
			foreach ($query->result() as $attachRow) {
				$success = unlink($this->config->item('upload_path').$attachRow->Filename);
				
				//see if the file successfully deleted.
				if ($success) {
					//remove entry from db.
					$this->db->where('tid', $this->getTiD())
					  ->where('Filename', $attachRow->Filename)
					  ->delete('ebb_attachments');
				}
			}

			$this->db->where('tid', $this->getTiD())
			  ->delete('ebb_topics');

			return TRUE;
		} else {
			//no attachments, so just delete the topic.
			$this->db->where('tid', $this->getTiD())
			  ->delete('ebb_topics');
			return TRUE;
		}
		
	}
	
	/**
	 * Delete poll data for defined Topic ID.
	 * @version 06/18/12
	 */
	public function DeletePoll() {
		$this->db->where('tid', $this->getTiD())
		  ->delete('ebb_poll');
		
		$this->db->where('tid', $this->getTiD())
		  ->delete('ebb_votes');
	}
	
	/**
	 * Delete Replies.
	 * @param boolean $deleteAll Delete all replies or just one?
	 * @version 06/18/12
	 * @return boolean
	 */
	public function DeleteReply($deleteAll = FALSE) {
		//see if we want to delete all replies associated with a topic or just one reply.
		if ($deleteAll) {
			
			$this->db->select('pid')
			  ->from('ebb_posts')
			  ->where('tid', $this->getTiD());
			$rQuery = $this->db->get();
			
			//see if we have any replies to delete.
			if($rQuery->num_rows() == 0) {
				return TRUE; //no replies, just exit then.
			} else {
				//loop through data and clear attachments.
				foreach ($rQuery->result() as $replyRow) {
					$this->db->select('Filename')
					  ->from('ebb_attachments')
					  ->where('pid', $replyRow->pid);
					$aQuery = $this->db->get();
					
					//see if we have any records to show.
					if($aQuery->num_rows() > 0) {
						
						foreach ($aQuery->result() as $attachRow) {
							$success = unlink($this->config->item('upload_path').$attachRow->Filename);
							//see if the file successfully deleted.
							if ($success) {
								//remove entry from db.
								$this->db->where('pid', $replyRow->pid)
								  ->where('Filename', $attachRow->Filename)
								  ->delete('ebb_attachments');
							}
						}
					}
				}
				//delete all replies tied to defined topic id.
				$this->db->where('tid', $this->getTiD())
				  ->delete('ebb_posts');
				return TRUE;
			}
		} else {
			$this->db->select('Filename')
			  ->from('ebb_attachments')
			  ->where('pid', $this->getPiD());
			$query = $this->db->get();
			
			//see if we have any records to show.
			if($query->num_rows() > 0) {
				foreach ($query->result() as $attachRow) {
					$success = unlink($this->config->item('upload_path').$attachRow->Filename);
					
					//see if the file successfully deleted.
					if ($success) {
						//remove entry from db.
						$this->db->where('pid', $this->getPiD())
						  ->where('Filename', $attachRow->Filename)
						  ->delete('ebb_attachments');
					}
					
				}
				$this->db->where('pid', $this->getPiD())
				  ->delete('ebb_posts');

				return TRUE;
			} else {
				//no attachments, just delete the post.
				$this->db->where('pid', $this->getPiD())
				  ->delete('ebb_posts');
			}
		}
	}

	/**
	 * Grab topic data.
	 * @param int $tid TopicID
	 * @version 09/26/12
	 * @access public
	 * @return boolean
	*/
	public function GetTopicData($tid) {

		//fetch topic data.
		$this->db->select('t.tid, t.bid, t.author, u.Username, t.Topic, t.Body, t.topic_type, t.important, t.IP, t.Original_Date, t.last_update, t.posted_user, t.pid, t.Locked, t.Views, t.Question, t.disable_bbcode, t.disable_smiles, u.Email, u.Post_Count, u.warning_level, u.Avatar, u.Sig, u.Custom_Title, g.profile, g.access_level')
		  ->from('ebb_topics t')
		  ->join('ebb_users u', 't.author=u.id', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT')
		  ->where('tid', $tid);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
		
			$TopicData = $query->row();

			//populate properties with values.
			$this->setAuthor($TopicData->Username);
			$this->setUId($TopicData->author);
			$this->setEmail($TopicData->Email);
			$this->setBid($TopicData->bid);
			$this->setBody($TopicData->Body);
			$this->setDisableBbCode($TopicData->disable_bbcode);
			$this->setDisableSmiles($TopicData->disable_smiles);
			$this->setImportant($TopicData->important);
			$this->setIp($TopicData->IP);
			$this->setLastUpdate($TopicData->last_update);
			$this->setLocked($TopicData->Locked);
			$this->setOriginalDate($TopicData->Original_Date);
			$this->setPostedUser($TopicData->posted_user);
			$this->setQuestion($TopicData->Question);
			$this->setTiD($TopicData->tid);
			$this->setTopic($TopicData->Topic);
			$this->setTopicType($TopicData->topic_type);
			$this->setViews($TopicData->Views);
			$this->setAvatar($TopicData->Avatar);
			$this->setPostCount($TopicData->Post_Count);
			$this->setSig($TopicData->Sig);
			$this->setWarningLevel($TopicData->warning_level);
			$this->setCustomTitle($TopicData->Custom_Title);
			$this->setGroupAccess($TopicData->access_level);
			$this->setGroupProfile($TopicData->profile);
			
			return TRUE;
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
	public function ListAllSubscriptionsByUser($uid, $order, $page, $indx) {
		$users = array();
		
		//fetch topic data.
		$this->db->select('tw.tid, t.Topic, b.Board')
		  ->from('ebb_topic_watch tw')
		  ->join('ebb_topics t', 'tw.tid=t.tid', 'LEFT')
		  ->join('ebb_boards b', 't.bid=b.id', 'LEFT')
		  ->where('tw.username', $uid);
		
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
	 * @param integer $uid User ID to filter by.
	 * @return integer
	 * @version 09/22/12
	 */
	public function countAllSubscriptionsByUser($uid) {
		$this->db->select('tid')
		  ->from('ebb_topic_watch')
		  ->where('username', $uid);
		
		$query = $this->db->get();
		
		return $query->num_rows();
	}
	
	/**
	 * Get count from search query.
	 * @param string $keyword what to search for.
	 * @param string $author the author to search by.
	 * @param integer $board the specific boad to search from.
	 * @return integer the total count of records returned.
	*/
	public function SearchTopicCount($keyword, $author = null, $board = null) {
		//fetch topic data.
		$this->db->select('t.tid')
		  ->from('ebb_topics t')
		  ->join('ebb_users u', 't.author=u.id', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT');
		
		if (!is_null($keyword)) {
			$this->db->like('t.Body', $keyword)
			  ->or_like('t.Topic', $keyword);
		}
		
		if (!is_null($board)) {
			$this->db->or_where("t.bid", $board);
		}
		if (!is_null($author)) {
			$this->db->or_where('u.Username', $author);
		}
		
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	/**
	 * Search for topics.
	 * @param string $keyword what to search for.
	 * @param integer $limit amount to show per page.
	 * @param integer $start what entry to start from.
	 * @param string $author the author to search by.
	 * @param integer $board the specific boad to search from.
	 * @return boolean|array array of values if TRUE; FALSE if no values given by query.
	*/
	public function SearchTopic($keyword, $limit, $start, $author = null, $board = null) {
		//setup search result data array.
		$searchResults = array();
		
		//fetch topic data.
		$this->db->select('t.tid, t.bid, u.Username, u.id AS uid, t.Topic, t.Body, t.topic_type, t.important, t.IP, t.Original_Date, t.last_update, t.pid, t.Locked, t.Views, b.id, b.Board, g.profile, g.access_level')
		  ->from('ebb_topics t')
		  ->join('ebb_users u', 't.author=u.id', 'LEFT')
		  ->join('ebb_boards b', 'b.id=t.bid', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT');
		
		if (!is_null($keyword)) {
			$this->db->like('t.Body', $keyword)
			  ->or_like('t.Topic', $keyword);
		}
		
		if (!is_null($board)) {
			$this->db->or_where("t.bid", $board);
		}
		if (!is_null($author)) {
			$this->db->or_where('u.Username', $author);
		}
		
		if (!is_null($limit) && !is_null($start)) {
			$this->db->limit($limit, $start);
		}
		$query = $this->db->get();
		
		//see if we have any records to show.
		if ($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$searchResults[] = $row;
			}
			return $searchResults;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get count from search query.
	 * @param string $keyword what to search for.
	 * @param string $author the author to search by.
	 * @param integer $board the specific boad to search from.
	 * @return integer the total count of records returned.
	*/
	public function SearchPostCount($keyword, $author = null, $board = null) {
		//fetch topic data.
		$this->db->select('p.pid')
		  ->from('ebb_posts p')
		  ->join('ebb_topics t', 'p.tid=t.tid', 'LEFT')
		  ->join('ebb_users u', 'p.author=u.id', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT');
		
		if (!is_null($keyword)) {
			$this->db->like('p.Body', $keyword)
			  ->or_like('t.Topic', $keyword);
		}
		
		if (!is_null($board)) {
			$this->db->or_where("t.bid", $board);
		}
		if (!is_null($author)) {
			$this->db->or_where('u.Username', $author);
		}
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	/**
	 * Search for reply posts.
	 * @param string $keyword what to search for.
	 * @param integer $limit amount to show per page.
	 * @param integer $start what entry to start from.
	 * @param string $author the author to search by.
	 * @param integer $board the specific boad to search from.
	 * @return boolean|array array of values if TRUE; FALSE if no values given by query.
	*/
	public function SearchPost($keyword, $limit, $start, $author = null, $board = null) {
		//setup search result data array.
		$searchResults = array();

		//SQL to get all topics from defined board.
		$this->db->select('u.Username, u.id AS uid, p.pid, p.tid, p.bid, t.Topic, p.Body, p.IP, p.Original_Date, b.id, b.Board, g.profile, g.access_level')
		  ->from('ebb_posts p')
		  ->join('ebb_topics t', 'p.tid=t.tid', 'LEFT')
		  ->join('ebb_users u', 'p.author=u.id', 'LEFT')
		  ->join('ebb_boards b', 'b.id=p.bid', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT');
		  
		
		if (!is_null($keyword)) {
			$this->db->like('p.Body', $keyword)
			  ->or_like('t.Topic', $keyword);
		}
		
		if (!is_null($board)) {
			$this->db->or_where("p.bid", $board);
		}
		if (!is_null($author)) {
			$this->db->or_where('u.Username', $author);
		}
		if(!is_null($limit) && !is_null($start)) {
			$this->db->limit($limit, $start);
		}	
		$query = $this->db->get();
		
		//see if we have any records to show.
		if ($query->num_rows() > 0) {
			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$searchResults[] = $row;
			}
			return $searchResults;
		} else {
			return FALSE;
		}
	}
	/**
	 * Get a list of topics & posts created ince a user's last visit.
	 * @param string $lastActive last visit timestamp
	 * @return array an array of topics & posts.
	*/
	public function getTopicsSinceLastActive($lastActive, $user) {
		//fetch topic data.
		$this->db->select('t.tid, t.bid, u.Username, u.id AS uid, t.Topic, t.Body, t.topic_type, t.important, t.IP, t.Original_Date, t.last_update, t.pid, t.Locked, t.Views, b.id, b.Board, g.profile, g.access_level')
		  ->from('ebb_topics t')
		  ->join('ebb_users u', 't.author=u.id', 'LEFT')
		  ->join('ebb_boards b', 'b.id=t.bid', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT')
		  ->where('t.author !=', $user)
		  ->where('t.Original_Date >=', $lastActive-3600*24*30)
		  ->or_where('t.last_update >=', $lastActive-3600*24*30);
		$tQuery = $this->db->get();
		
		//SQL to get all topics from defined board.
		$this->db->select('u.Username, u.id AS uid, p.pid, p.tid, p.bid, t.Topic, p.Body, p.IP, p.Original_Date, b.id, b.Board, g.profile, g.access_level')
		  ->from('ebb_posts p')
		  ->join('ebb_topics t', 'p.tid=t.tid', 'LEFT')
		  ->join('ebb_users u', 'p.author=u.id', 'LEFT')
		  ->join('ebb_boards b', 'b.id=p.bid', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT')
		  ->where('p.author !=', $user)
		  ->where('p.Original_Date >=', $lastActive-3600*24*30);
		$pQuery = $this->db->get();
		
		return array_merge($tQuery->result(), $pQuery->result());
	}
	
	/**
	 * Check read status on a selected board.
	 * @param integer $tid - Topic ID to select a board.
	 * @param string $user - Username to check against.
	 * @return integer 1, topic is read; 0, topic unread.
	*/
	function readTopicStat($tid, $user) {
		$this->db->select('t.tid')
		  ->from('ebb_topics t')
		  ->join('ebb_read_topic rt', 't.tid=rt.Topic', 'LEFT')
		  ->where('rt.User', $user)
		  ->where('t.tid', $tid);
		$query = $this->db->get();
		
		return $query->num_rows();
	}
	
	/**
	 * Get a single reply record.
	 * @param integer $pid Post ID
	 * @version 07/16/12
	 * @return boolean
	 */
	public function GetReplyData($pid){
		//fetch topic data.
		$this->db->select('p.author, u.Username, p.pid, p.tid, p.bid, p.Body, p.IP, p.Original_Date, p.disable_smiles, p.disable_bbcode, t.Topic')
		  ->from('ebb_posts p')
		  ->join('ebb_topics t', 'p.tid=t.tid', 'LEFT')
		  ->join('ebb_users u', 'p.author=u.id', 'LEFT')
		  ->where('p.pid', $pid);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
		
			$PostData = $query->row();

			//populate properties with values.
			$this->setAuthor($PostData->Username);
			$this->setUId($PostData->author);
			$this->setTopic($PostData->Topic);
			$this->setPiD($PostData->pid);
			$this->setTiD($PostData->tid);
			$this->setBid($PostData->bid);
			$this->setBody($PostData->Body);
			$this->setDisableBbCode($PostData->disable_bbcode);
			$this->setDisableSmiles($PostData->disable_smiles);
			$this->setIp($PostData->IP);
			$this->setOriginalDate($PostData->Original_Date);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Get a list of all replies to a topic.
	 * @version 09/26/12
	 * @param int $tid Topic ID.
	 * @param int $limit amount to show per page.
	 * @param int $start what entry to start from.
	 * @access public
	 * @return array|boolean array with post values if TRUE; FALSE if no results were returned from query.
	*/
	public function GetReplies($tid, $limit, $start) {

		//setup reply data array.
		$replies = array();

		//SQL to get all topics from defined board.
		$this->db->select('u.Username, p.author, p.pid, p.tid, p.bid, p.Body, p.IP, p.Original_Date, p.disable_smiles, p.disable_bbcode, u.Post_Count, u.warning_level, u.Email, u.Avatar, u.Sig, u.Custom_Title, g.profile, g.access_level')
		  ->from('ebb_posts p')
		  ->join('ebb_users u', 'p.author=u.id', 'LEFT')
		  ->join('ebb_permission_profile g', 'g.id=u.gid', 'LEFT')
		  ->where('p.tid', $tid);
		
		if(!is_null($limit) && !is_null($start)) {
			$this->db->limit($limit, $start);
		}
		  
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {

			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$replies[] = $row;
			}

			return $replies;
		} else {
			return FALSE;
		}
	}
	
    /**
     * Get Poll Options
     * @param integer $tid TopicID
     * @return array,boolean
	 * @version 06/13/12
    */
	public function GetPoll($tid) {
		
        //setup reply data array.
		$pollOpt = array();

		//SQL to get all topics from defined board.
		$this->db->select('option_value, option_id')
		  ->from('ebb_poll')
		  ->where('tid', $tid);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {

			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$pollOpt[] = $row;
			}

			return $pollOpt;
		} else {
			return FALSE;
		}
        
	}

	/**
	 * Saves Vote Cast in the DataBase.
	 * @param string $user User who casted the vote
	 * @param integer $tid the topic that holding the vote
	 * @param integer $vote The vote value user selected
	 * @version 01/12/12
	*/
	public function CastVote($user, $tid, $vote) {

		$data = array(
		  'Username' => $user,
		  'tid' => $tid,
		  'Vote' => $vote
		);
		$this->db->insert('ebb_votes', $data);

	}
    
	/**
	 * Show the results of a poll.
	 * @param integer $tid Topic ID.
	 * @return boolean|array
	 * @version 06/06/12
	*/
    public function ShowPollResults($tid) {
       
        //setup reply data array.
		$pollRes = array();

		//SQL to get all topics from defined board.
		$this->db->select('Poll_Option, option_id');
		$this->db->from('ebb_poll');
		$this->db->where('tid', $tid);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {

			//loop through data and bind to an array.
			foreach ($query->result() as $row) {
				$pollRes[] = $row;
			}

			return $pollRes;
		} else {
			return FALSE;
		}
        
    }
}