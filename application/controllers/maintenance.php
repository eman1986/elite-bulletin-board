<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
	* maintenance.php
	* @package Elite Bulletin Board v3
	* @author Elite Bulletin Board Team <http://elite-board.us>
	* @copyright (c) 2006-2013
	* @license http://opensource.org/licenses/gpl-license.php GNU Public License
	* @version 12/24/2012
*/

class Maintenance extends EBB_Controller {

	function __construct() {
 		parent::__construct();
		
		//ensure only moderators & administrators can access this controller.
		if ($this->groupAccess <> 1 || $this->groupAccess <> 2) {
			//alert user.
			$this->notifications('warning', $this->lang->line('accessdenied'));

			#direct user to main page.
			redirect('/', 'location');
		}

	}
	
	public function warn($user) {
		//LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email', 'form_validation', 'breadcrumb'));
        $this->load->helper(array('form', 'user'));
		
		
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/boards/');
		$this->breadcrumb->append_crumb($this->lang->line("warnuser"), '/maintenance/warn/'.$user);

		//setup validation rules.
		$this->form_validation->set_rules('reason', $this->lang->line('reason'), 'required|xss_clean');
		$this->form_validation->set_rules('msg', $this->lang->line('message'), 'required|min_length[10]|max_length[255]|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;text-align:left;"><p id="validateResSvr">', '</p></div></div>');
		
		//see if any validation rules failed.
			if ($this->form_validation->run() == FALSE) {
				//render to HTML.
				echo $this->twig->render(strtolower(__CLASS__), 'warnuser', array (
				  'boardName' => $this->title,
				  'pageTitle'=> $this->lang->line("modcp").' - '.$this->lang->line("warnuser"),
				  'THEME_NAME' => $this->getStyleName(),
				  'BOARD_URL' => $this->boardUrl,
				  'APP_URL' => $this->boardUrl.APPPATH,
				  'NOTIFY_TYPE' => $this->notifyType,
				  'NOTIFY_MSG' =>  $this->notifyMsg,
				  'LANG' => $this->lng,
				  'groupAccess' => $this->groupAccess,
				  'LANG_INACTIVETITLE' => $this->lang->line('inactivesessiontitle'),
				  'LANG_INACTIVENOTICE' => $this->lang->line('inactivenotice'),
				  'LANG_SECONDS' => $this->lang->line('seconds'),
				  'LANG_CONTINUESESSION' => $this->lang->line('continuesession'),
				  'LANG_YES' => $this->lang->line('yes'),
				  'LANG_NO' => $this->lang->line('no'),
				  'LANG_WELCOME'=> $this->lang->line('loggedinas'),
				  'LANG_WELCOMEGUEST' => $this->lang->line('welcomeguest'),
				  'LOGGEDUSER' => $this->logged_user,
				  'LANG_JSDISABLED' => $this->lang->line('jsdisabled'),
				  'LANG_INFO' => $this->lang->line('info'),
				  'LANG_LOGIN' => $this->lang->line('login'),
				  'LANG_LOGOUT' => $this->lang->line('logout'),
				  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin')),
				  'FORMWARN' => form_open('maintenance/warn', array('name' => 'frmWarnUser')),
				  'VALIDATIONSUMMARY' => validation_errors(),
				  'VALIDATION_USERNAME' => form_error('username'),
				  'VALIDATION_PASSWORD' => form_error('password'),
				  'LANG_USERNAME' => $this->lang->line('username'),
				  'LANG_REGISTER' => $this->lang->line('register'),
				  'LANG_PASSWORD' => $this->lang->line('pass'),
				  'LANG_FORGOT' => $this->lang->line('forgot'),
				  'LANG_REMEMBERTXT' => $this->lang->line('remembertxt'),
				  'LANG_QUICKSEARCH' => $this->lang->line('quicksearch'),
				  'LANG_SEARCH' => $this->lang->line('search'),
				  'LANG_CP' => $this->lang->line('admincp'),
				  'LANG_NEWPOSTS' => $this->lang->line('newposts'),
				  'LANG_HOME' => $this->lang->line('home'),
				  'LANG_HELP' => $this->lang->line('help'),
				  'LANG_MEMBERLIST' => $this->lang->line('members'),
				  'LANG_PROFILE' => $this->lang->line('profile'),
				  'LANG_POWERED' => $this->lang->line('poweredby'),
				  'BREADCRUMB' =>$this->breadcrumb->output(),
				  "LANG_TEXT" => $this->lang->line('warntxt'),
				  "LANG_WARNOPTION" => $this->lang->line('warnopt'),
				  "LANG_RAISEWARN" => $this->lang->line('raisewarn'),
				  "LANG_LOWERWARN" => $this->lang->line('lowerwarn'),
				  "LANG_WARNREASON" => $this->lang->line('warnreason'),
				  "LANG_SUSPENDIONLENGTH" => $this->lang->line('suspensionlength'),
				  "LANG_SUSPENDHINT" => $this->lang->line('suspendhint'),
				  "SUSPENDIONLENGTH" => "$user_r[suspend_length]",
				  "LANG_CONTACTOPTION" => $this->lang->line('contactopt'),
				  "LANG_NOCONTACT" => $this->lang->line('nocontact'),
				  "LANG_PMCONTACT" => $this->lang->line('pmcontact'),
				  "LANG_EMAILCONTACT" => $this->lang->line('email'),
				  "LANG_CONTACT-TEXT" => $this->lang->line('contacttxt'),
				  "LANG_SUBMIT" => $this->lang->line('warnuser'),
				  'USER' => $user
				));
			} else {
				
			}
				
		
	}
	
	/**
	 * marks a topic as locked.
	 * @example index.php/maintenance/lock/5
	 */
	public function lock($id) {
		//load topic model.
		$this->load->model(array('Topicmodel'));
		
		//load entities
		$tData = $this->Topicmodel->GetTopicData($id);
		
		//ensure everything loaded correctly.
		if ($tData) {
			$this->Topicmodel->setLocked(TRUE);
			$this->Topicmodel->setTiD($id);
			$this->Topicmodel->ToggleTopicLock();
			
			//notify user of changes.
			$this->notifications('success', $this->lang->line('lockedtopicsuccess'));
			
			//direct user to topic.
			redirect('/viewtopic/'.$id, 'location');
		} else {
			show_error($this->lang->line('doesntexist'), 403, $this->lang->line('error'));
		}
	}
	
	/**
	 * marks a topic as unlocked.
	 * @example index.php/maintenance/unlock/5
	 */
	public function unlock($id) {
		//load topic model.
		$this->load->model(array('Topicmodel'));
		
		//load entities
		$tData = $this->Topicmodel->GetTopicData($id);
		
		//ensure everything loaded correctly.
		if ($tData) {
			$this->Topicmodel->setLocked(FALSE);
			$this->Topicmodel->setTiD($id);
			$this->Topicmodel->ToggleTopicLock();
			
			//notify user of changes.
			$this->notifications('success', $this->lang->line('unlockedtopicsuccess'));
			
			//direct user to topic.
			redirect('/viewtopic/'.$id, 'location');
		} else {
			show_error($this->lang->line('doesntexist'), 403, $this->lang->line('error'));
		}
	}
	
	/**
	 * delete topics.
	 * @example index.php/maintenance/delete/5
	 */
	public function delete($id) {
		//load topic model.
		$this->load->model(array('Topicmodel'));
		
		//load entities
		$tData = $this->Topicmodel->GetTopicData($id);
		
		//ensure everything loaded correctly.
		if ($tData) {
			
			//see if admin or moderator, they got different access control to validate from.
			if($this->Groupmodel->ValidateAccess(1, 21)){
				$CanDelete = TRUE;
			} else {
				$CanDelete = FALSE;
			}
			
			//if user can delete, delete all data, if not, let them know this.
			if ($CanDelete) {
				$this->Topicmodel->DeleteTopic();
				$this->Topicmodel->DeleteReply(TRUE);
				$this->Topicmodel->DeletePoll();
				
				//display success message.
				$this->notifications('success', $this->lang->line('deletetopicsuccess'));
				
				#direct user to view board.
				redirect('/viewboard/'.$this->Topicmodel->getBid(), 'location');
			} else {
				show_error($this->lang->line('accessdenied'),403,$this->lang->line('error'));
			}

		} else {
			exit(show_error($this->lang->line('doesntexist'), 500, $this->lang->line('error')));
		}
	}
	
	/**
	 * move a topic and its replies to a new board.
	 * @example index.php/maintenance/move/
	 */
	public function move() {
		
		//load topic model.
		$this->load->model(array('Topicmodel'));
		
		$moveTopic = $this->input->post('movetopic', TRUE);
		$origBoard = $this->input->post('origBoard', TRUE);
		$topicID = $this->input->post('topicID', TRUE);
		
		//make sure Topic ID is defined.
		if (!isset($topicID) OR (empty($topicID)) OR (!is_numeric($topicID))) {
			show_error($this->lang->line('notid'),500,$this->lang->line('error'));
		}
		
		//make sure origBoard is defined.
		if (!isset($origBoard) OR (empty($origBoard)) OR (!is_numeric($origBoard))) {
			show_error($this->lang->line('nobid'),500,$this->lang->line('error'));
		} 
		
		//load entities
		$tData = $this->Topicmodel->GetTopicData($topicID);
		
		//ensure everything loaded correctly.
		if ($tData) {
			if ($moveTopic == FALSE) {
				//display error message.
				$this->notifications('error', $this->lang->line('noboard'));
				redirect('/viewboard/'.$origBoard, 'location');
			} elseif ($moveTopic == $origBoard) {
				//display error message.
				$this->notifications('error', $this->lang->line('sameboard'));
				redirect('/viewboard/'.$origBoard, 'location');
			} else {
				$this->Topicmodel->setBid($moveTopic);
				$this->Topicmodel->setTiD($topicID);
				$this->Topicmodel->MoveTopic($origBoard);
			}
		} else {
			exit(show_error($this->lang->line('doesntexist'), 500, $this->lang->line('error')));
		}
	}
		
	public function viewip($user) {
		
	}
}
