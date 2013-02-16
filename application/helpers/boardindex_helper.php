<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * boardindex_helper.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @verson 02/15/2013
*/

/**
 * Get a total of various things.
 * @version 06/27/12
 * @param int $id any kind of integer.
 * @param string $type The total we're looking for.
 * @access public
*/
function GetCount($id, $type) {

	//grab Codeigniter objects.
	$ci =& get_instance();

	#see what total we want to grab.
	switch($type) {
		case 'TopicCount':
			$ci->db->select('tid')->from('ebb_topics')->where('bid', $id);
			$topicQ = $ci->db->get();
			return number_format($topicQ->num_rows());
		break;
		case 'PostCount':
			$ci->db->select('pid')->from('ebb_posts')->where('bid', $id);
			$postQ = $ci->db->get();
			return number_format($postQ->num_rows());
		break;
		case 'TopicReplies':
			$ci->db->select('pid')->from('ebb_posts')->where('tid', $id);
			$postQ = $ci->db->get();
			return number_format($postQ->num_rows());
		break;
		case 'TopicViews':
			return number_format($id);
		break;
		case 'PollCount':
			$ci->db->select('tid')->from('ebb_votes')->where('tid', $id);
			$pollQ = $ci->db->get();
			return number_format($pollQ->num_rows());
		break;
		default:
			return FALSE;//invalid choice.
		break;
	}	
}

/**
 * Get a total of votes casted for a defined option.
 * @param integer $vote the vote we're calculating.
 * @param integer $tid the topic id tosearch by
 * @return integer
 * @version 06/27/12
 */
function CalcVotes($vote, $tid) {
	//grab Codeigniter objects.
	$ci =& get_instance();
	$ci->db->select('tid')->from('ebb_votes')->where('Vote', $vote)->where('tid', $tid);
	$pollQ = $ci->db->get();
	return $pollQ->num_rows();
}


/**
 * Check to see defined user casted a vote.
 * @param integer $usr Logged In User
 * @param integer $tid TopicID
 * @return boolean
 * @version 07/16/12
 */
function CheckVoteStatus($usr, $tid) {
	
	//set to false if user is not logged in.
	if ($usr == "guest") {
		return FALSE;
	} else {
		//grab Codeigniter objects.
		$ci =& get_instance();
		$ci->db->select('tid')->from('ebb_votes')->where('Username', $usr)->where('tid', $tid);
		$voteQ = $ci->db->get();

		//see if logged in user already voted.
		if ($voteQ->num_rows() == 1) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

/**
 * Check read status on a selected board.
 * @param integer $tid Topic ID to select a topic.
 * @param integer $user Username to check against.
 * @return integer 1, topic is read; 0, topic unread.
*/
function readTopicStat($tid, $user) {
	#obtain codeigniter object.
	$ci =& get_instance();
	
	#see if user is guest.
	if ($user == "guest") {
		return 1;
	} else {
		$ci->load->model('Topicmodel', 'tm');
		return $ci->tm->readTopicStat($tid, $user);
	}
}

/**
 *See if topic has an attachment.
 * @param integer $tid TopicID
 * @param integer $pid PostID (0 by default)
 * @return boolean
 * @version 06/27/12
 */
function HasAttachment($tid) {

	#obtain codeigniter object.
	$ci =& get_instance();

	//see if topic has attachment.
	$ci->db->select('id')->from('ebb_attachments')->where('tid', $tid);
	$topicQ = $ci->db->get();

	//see if we have any attachments.
	if($topicQ->num_rows() > 0) {
		return TRUE;
	} else {
		#get reply count.
		$ci->db->select('pid, tid')->from('ebb_posts')->where('tid', $tid);
		$postQ = $ci->db->get();

		if ($postQ->num_rows() == 0) {
			return FALSE;
		} else {
			#see if any attachments are added to the reply of a topic.
			foreach ($postQ->result() as $row) {
				$ci->db->select('id')->from('ebb_attachments')->where('pid', $row->pid);
				$attachQ = $ci->db->get();

				if ($attachQ->num_rows() > 0) {
					$attachPost = TRUE;
					break;
				} else {
					$attachPost = FALSE;
				}
			}
		}
		
		return $attachPost;
	} //END Topic Attachment Check.
}

/**
 * Get a count of sub-boards.
 * @param int $boardID
 * @return int
 * @TODO place this in Boardmodel class.
 */
function GetSubBoardCount($boardID) {
	
	#obtain codeigniter object.
	$ci =& get_instance();

	//SQL grabbing count of all topics for this board.
	$ci->db->select('id')->from('ebb_boards')->where('type', 3)->where('Category', $boardID);
	$subBoardQ = $ci->db->get();
	return $subBoardQ->num_rows();
	
}

/**
 * Obtains a few stats about the board.
 * @version 06/27/12
 * @return integer|string
*/
function boardStats($type){

    //grab Codeigniter objects.
	$ci =& get_instance();

	#see what we're counting.
	switch($type){
	    case 'member':
			#get member count.
			$ci->db->select('id')->from('ebb_users')->where('active', 1);
			$memberQ = $ci->db->get();
			return number_format($memberQ->num_rows());
	    break;
	    case 'topic':
			#get topic count.
			$ci->db->select('tid')->from('ebb_topics');
			$topicQ = $ci->db->get();
			return number_format($topicQ->num_rows());
		break;
	    case 'post':
			#get post count.
			$ci->db->select('pid')->from('ebb_posts');
			$postQ = $ci->db->get();
			return number_format($postQ->num_rows());
	    break;
	    case 'newuser':
			#get newest user.
			$ci->db->select('Username')->from('ebb_users')->where('active', 1)->order_by("Date_Joined", "desc")->limit(1);
			$query = $ci->db->get();
			$newUser = $query->row();
			return $newUser->Username;
	    break;
		case 'guestonline':
			//get total guest online.
			$ci->db->distinct('ip')->from('ebb_online')->where('Username IS NULL');
			return number_format($ci->db->count_all_results());
		break;
		case 'memberonline':
			//get total members online.
			$ci->db->distinct('Username')->from('ebb_online')->where('ip IS NULL');
			$memberQ = $ci->db->get();
			return number_format($memberQ->num_rows());
		break;
	    default:
	        return (0);
	    break;
	}
}

/**
 * Will list all sub-boards linked to a parent board.
 * @param int $boardID - Board ID to search for any sub-boards.
 * @return string
 * @TODO place this in Boardmodel class.
*/
function getSubBoard($boardID) {

	//grab Codeigniter objects.
	$ci =& get_instance();
	
	$ci->db->select('id, Board')->from('ebb_boards')->where('type', 3)->where('Category', $boardID)->order_by("B_Order", "asc");
	$subBoardQuery = $ci->db->get();
	$countSub = $subBoardQuery->num_rows();
	
	if($countSub == 0){
		$subBoard = '';
	}else{
		$subBoard = $ci->lang->line('subboards').":&nbsp;";

		#counter variable.
		$counter = 0;

		foreach ($subBoardQuery->result() as $row) {

			#see if we've reached the end of our query results.
			if($countSub == 1){
				$marker = '';
			}elseif($counter < $countSub - 1){
				$marker = ',&nbsp;';
			}else{
				$marker = '';
			}

			#board rules sql.
			$ci->Boardaccessmodel->GetBoardAccess($row->id);

			#see if user can view the board.
			if ($ci->Groupmodel->validateAccess(0, $ci->Boardaccessmodel->getBRead())){
				$subBoard .= sprintf("<em>%s</em>%s", anchor('/viewboard/'.$row->id, $row->Board), $marker);
			}
			
			//increment counter.
			$counter++;
		} #END forloop
	}

	return $subBoard;
}

/**
 * Format our topic body.
 * @param boolean $topic_smiles Topic Settings to Allow Smiles.
 * @param boolean $board_smiles Board Settings to Allow Smiles.
 * @param boolean $topic_bbcode Topic Settings to Allow BBCode.
 * @param boolean $board_bbcode Board Settings to Allow BBCode.
 * @param boolean $board_Image Board Settings to Allow Images.
 * @param mixed $body Topic Body to format.
 * @param boolean printable Is this in the print mode?
 * @version 06/21/12
 * @return string
 */
function FormatTopicBody($topic_smiles, $board_smiles, $topic_bbcode, $board_bbcode, $board_Image, $body, $printable=false) {
	$topicBody = $body;

	#see if user wish to allow smiles.
	if($topic_smiles == 0){
		#see if board allows smiles.
		if ($board_smiles == 1){
			$topicBody = smiles($topicBody);
		}
	}

	#see if user wish to allow bbcode.
	if($topic_bbcode == 0){
		#see if board allow BBCode formatting.
		if ($board_bbcode == 1){
			//is this in print-mode?
			if ($printable) {
				$topicBody = BBCode_print($topicBody);
			} else {
				$topicBody = BBCode($topicBody);
			}
		}

		#see if board allows use of [img] tag.
		if ($board_Image == 1){
			if (!$printable) {
				$topicBody = BBCode($topicBody, true);
			}
		}
	}

	return ($topicBody);
}