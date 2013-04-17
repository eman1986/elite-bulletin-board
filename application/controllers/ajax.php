<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * ajax.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/17/2013
*/

/**
 * Ajax Controller
 * @abstract EBB_Controller 
 */
class Ajax extends EBB_Controller {
	
	public function __construct() {
 		parent::__construct();
		
		//see if user is calling directly or by AJAX.
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		//see if user is logged in.
		if ($this->logged_user == "guest") {
			exit($this->lang->line('guesterror'));
		}
		
		$this->load->helper(array('posting'));
	}

	/**
	 * keep the login session alive.
	 * @example index.php/ajax/keepalive
	*/
	public function keepalive() {
		//see if user is logged in.
		if ($this->logged_user == "guest") {
			exit($this->lang->line('guesterror'));
		}

		if ($this->user->validateLoginSession($this->session->userdata('ebbLastActive'), $this->session->userdata('ebbLoginKey'), 0)) {
			echo 'OK';
		}
	}

	/**
	 * search for similar topics
	 * @example index.php/ajax/SimilarTopics
	*/
	public function SimilarTopics() {
		
		//LOAD LIBRARIES
		$this->load->model(array('Boardaccessmodel'));
		$this->load->helper(array('user', 'posting'));

		$q = $this->input->post('topic', TRUE);

		//flood check.
		if ($this->groupAccess == 3 && flood_check("search", $this->Usermodel->getLastSearch())) {
			exit($this->lang->line('flood'));
		} else {
			if ($q == "") {
				exit($this->lang->line('nokeyword'));
			} else {
				#update search flood info.
				$data = array(
				"last_search" => time()
				);
				$this->db->where('Username', $this->logged_user);
				$this->db->update('ebb_users', $data);

				//search for similar topics.
				$this->db->select('bid, tid, author, Topic');
				$this->db->from('ebb_topics');
				$this->db->like('Topic', $q, 'after');
				$this->db->or_like('Body', $q, 'after');
				$query = $this->db->get();

				//see if we have any records to show.
				if($query->num_rows() > 0) {
					echo '<strong>'.$this->lang->line('relatedtopics').'</strong><hr />';

					//loop through results.
					foreach ($query->result() as $row) {

						//get rules for board.
						$this->Boardaccessmodel->GetBoardAccess($row->bid);

						//see if the user can access this spot.
						if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBRead())){
							echo '<em>'.anchor('/viewtopic/'.$row->tid, $row->Topic).'</em> - '.$row->author.'<br />';
						}
					}
				} else {
					echo $this->lang->line('nosimilar');
				} //END results.
			} //END query keyword check.
		} //END flood check.
	}
	
	/**
	 * preview topic/post.
	 * @example index.php/ajax/PreviewTopic
	*/
	public function PreviewTopic() {
		//get our variable needed to grab the data from our editor.
		$previewPost = $this->input->post('data', TRUE);

		//see if the user added anytihng to preview.
		if($previewPost == ""){
			exit($this->lang->line('notopicbody'));
		}else{
			#format string.
			$formatMsg = nl2br(smiles(BBCode(censorFilter($previewPost), true)));

			#output formatted data.
			echo $formatMsg;
		}
	}

	/**
	 * perform check on settings.
	 * @example index.php/ajax/PrefCheck/action
	*/	
	public function PrefCheck($action) {

		switch($action) {
			case 'attachment':
				#see if user can add an attachment.
				if(!$this->Groupmodel->validateAccess(1, 26)){
					die('Attachments hsa been disabled by the site administrator.');
				}else{
					echo 'OK';
				}
			break;
			default:
				die($this->lang->line('invalidaction'));
			break;

		}
	}
	
	/**
	 * list of smiles.
	 * @example index.php/ajax/smiles
	*/
	public function smiles() {
//		$this->load->model('Topicmodel');
//		$smiles = $this->Topicmodel->ShowSmiles();
		echo 'UNDER WORK!';
	}
	
	/**
	 * search for users.
	 * @example index.php/ajax/searchusernames/foo
	*/
	public function searchusernames($user) {
		$this->load->model('Relationshipmodel');
		
		$usersArr = $usrListArr = array();
		$foesArr = $this->Relationshipmodel->getFoeListByUser($this->userID);
		
		//see if this user is an admin user.
		if ($this->groupAccess == 1) {
			$usersArr = $this->Usermodel->searchUser($user, TRUE);
		} else {
			$usersArr = $this->Usermodel->searchUser($user, FALSE);
		}
		
		//see if user has any foes.
		if (count($foesArr) == 0) {
			echo json_encode($usersArr);
		} else {
			foreach ($usersArr as $user) {
				foreach ($foesArr as $foe) {
					if ($foe->username <> $user->id) {
						$usrListArr[] = $user;
					}
				}
			}
			echo json_encode($usrListArr);
		}
	}
	
}