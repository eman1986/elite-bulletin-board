<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * boards.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/12/2013
*/

class Boards extends EBB_Controller {

	function __construct() {
 		parent::__construct();
		
		//see if user is an administrator.
		if ($this->groupAccess != 1) {
			exit($this->lang->line('accessdenied'));
		}
		
		//load breadcrumb library
		$this->load->helper(array('admin', 'form'));
		
		//load breadcrumb library
		$this->load->library('breadcrumb');
		
		//load audit & board model.
		$this->load->model(array('Boardmodel', 'Cplogmodel'));
	}
	
	/**
	 * board setup.
	 * @example index.php/ACP/boards/setup
	*/
	public function setup() {
		$bInx = $this->Boardmodel->loadBoardHier();

		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("admincp"), '/ACP/');
		$this->breadcrumb->append_crumb($this->lang->line("boardsetup"), '/ACP/board/setup');
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'setup', array (
            'boardName' => $this->title,
			'THEME_NAME' => $this->getStyleName(),
			'STYLELIST' => $this->styleList,
            'pageTitle'=> $this->lang->line('admincp'),
			'INDEX_PAGE' => $this->config->item('index_page'),
            'BOARD_URL' => $this->boardUrl,
            'APP_URL' => $this->boardUrl.APPPATH,
            'NOTIFY_TYPE' => $this->notifyType,
            'NOTIFY_MSG' =>  $this->notifyMsg,
            'LANG' => $this->lng,
            'TimeFormat' => $this->dateTimeFormat,
            'TimeZone' => $this->timeZone,
			'LANG_INACTIVETITLE' => $this->lang->line('inactivesessiontitle'),
			'LANG_INACTIVENOTICE' => $this->lang->line('inactivenotice'),
			'LANG_SECONDS' => $this->lang->line('seconds'),
			'LANG_CONTINUESESSION' => $this->lang->line('continuesession'),
			'LANG_YES' => $this->lang->line('yes'),
			'LANG_NO' => $this->lang->line('no'),
            'LANG_WELCOME'=> $this->lang->line('loggedinas'),
            'LANG_WELCOMEGUEST' => $this->lang->line('welcomeguest'),
            'LOGGEDUSER' => $this->logged_user,
			'LOGGEDUSERID' => $this->userID,
            'LANG_JSDISABLED' => $this->lang->line('jsdisabled'),
            'LANG_INFO' => $this->lang->line('info'),
            'LANG_LOGIN' => $this->lang->line('login'),
            'LANG_LOGOUT' => $this->lang->line('logout'),
            'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin', 'id' => 'loginForm')),
            'LANG_USERNAME' => $this->lang->line('username'),
            'LANG_REGISTER' => $this->lang->line('register'),
            'LANG_PASSWORD' => $this->lang->line('pass'),
            'LANG_FORGOT' => $this->lang->line('forgot'),
            'LANG_REMEMBERTXT' => $this->lang->line('remembertxt'),
            'LANG_QUICKSEARCH' => $this->lang->line('quicksearch'),
            'LANG_SEARCH' => $this->lang->line('search'),
			'LANG_ADVSEARCH' => $this->lang->line('advsearch'),
			'LANG_PMS' => $this->lang->line('pm'),
			'LANG_PMINBOX' => $this->lang->line('inbox'),
			'LANG_PMARCHIVE' => $this->lang->line('archive'),
			'UNREADPMCOUNT' => $this->newPMCount,
			'LANG_UNREADPM' => $this->lang->line('newpm'),
			'LANG_POSTPM' => $this->lang->line('PostPM'),
            'LANG_CP' => $this->lang->line('admincp'),
            'LANG_NEWPOSTS' => $this->lang->line('newposts'),
            'LANG_HOME' => $this->lang->line('home'),
            'LANG_HELP' => $this->lang->line('help'),
            'LANG_MEMBERLIST' => $this->lang->line('members'),
            'LANG_UOPTIONS' => $this->lang->line('uoptions'),
		    'LANG_CHANGETHEME' => $this->lang->line('changetheme'),
            'LANG_POWERED' => $this->lang->line('poweredby'),
			'LANG_BOARDMENU' => $this->lang->line('boardmenu'),
			'LANG_BOARDSETUP' => $this->lang->line('boardsetup'),
			'LANG_NEWBOARD' => $this->lang->line('newboard'),
			'LANG_NEWPARENTBOARD' => $this->lang->line('newparentboard'),
			'LANG_NEWSUBBOARD' => $this->lang->line('newsubboard'),
			'LANG_GENERALMENU' => $this->lang->line('generalmenu'),
			'LANG_MANAGESTYLES' => $this->lang->line('managestyles'),
			'LANG_NEWSLETTER' => $this->lang->line('newsletter'),
			'LANG_SPAMLIST' => $this->lang->line('spamlist'),
			'LANG_CENSOR' => $this->lang->line('censor'),
			'LANG_GROUPMENU' => $this->lang->line('groupmenu'),
			'LANG_GROUPSETUP' => $this->lang->line('groupsetup'),
			'LANG_MANAGEPROFILE' => $this->lang->line('manageprofile'),
			'LANG_NEWGROUP' => $this->lang->line('addgroup'),'LANG_USERSETTINGS' => $this->lang->line('usersettings'),
			'LANG_BOARDSETTINGS' => $this->lang->line('boardsettings'),
			'LANG_ANNOUNCEMENTSETTINGS' => $this->lang->line('announcementsettings'),
			'LANG_MAILSETTINGS' => $this->lang->line('mailsettings'),
			'LANG_ATTACHMENTSETTINGS' => $this->lang->line('attachmentsettings'),
			'LANG_USERMENU' => $this->lang->line('usermenu'),
			'LANG_BANLIST' => $this->lang->line('banlist'),
			'LANG_BLACKLIST' => $this->lang->line('blacklist'),
			'LANG_USERPRUNE' => $this->lang->line('userprune'),
			'LANG_ACTIVATE' => $this->lang->line('activateacct'),
			'LANG_WARNLOG' => $this->lang->line('warninglist'),
			'LANG_USERMGR' => $this->lang->line('manageusers'),
			'LANG_SETTINGS' => $this->lang->line('settings'),
            'groupAccess' => $this->groupAccess,
			'BREADCRUMB' => $this->breadcrumb->output(),
			'LANG_EDIT' => $this->lang->line('modifyboard'),
			'LANG_DELETE' => $this->lang->line('delboard'),
			'LANG_REORDER' => $this->lang->line('reorder'),
			'LANG_CONFIRM_DELETION' => $this->lang->line('condel'),
			'LANG_DELETE_CATEGORY_WARNING' => $this->lang->line('catdelwarning'),
			'Category' => $bInx['Parent_Boards'],
			'Boards' => $bInx['Child_Boards'],
			'SubBoards' => $bInx['SubChild_Boards']
		));
	}
	
	/**
	 * reorder form.
	 * @example index.php/ACP/boards/reorder/1/2
	*/
	public function reorder($type, $id) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		if ($type == 1) {
			$bInx = $this->Boardmodel->ListParentBoards();
		} elseif ($type == 2) {
			$bInx = $this->Boardmodel->ListBoards($id);
		} elseif ($type == 3) {
			$bInx = $this->Boardmodel->ListSubBoards($id);
		} else {
			exit(show_error($this->lang->line('ajaxerror'), 500, $this->lang->line('error')));
		}

		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'reorder', array (
            'boardName' => $this->title,
			'THEME_NAME' => $this->getStyleName(),
			'STYLELIST' => $this->styleList,
            'pageTitle'=> $this->lang->line('admincp'),
			'INDEX_PAGE' => $this->config->item('index_page'),
            'BOARD_URL' => $this->boardUrl,
            'APP_URL' => $this->boardUrl.APPPATH,
            'groupAccess' => $this->groupAccess,
			'LANG_REORDER' => $this->lang->line('reorder'),
			'LANG_TEXT' => $this->lang->line('reorderText'),
			'LANG_FORMFAILURE' => $this->lang->line('reorderfail'),
			'BOARDS' => $bInx
		));
	}
	
	/**
	 * processes reorder request.
	 * @example index.php/ACP/boards/reorderboard/1/0
	*/
	public function reorderboard($id, $order) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$this->Boardmodel->setId((int)$id);

		$Updatedata = array(
		  'B_Order' => (int)$order
		  );
		
		$this->Boardmodel->UpdateBoard($Updatedata);
		
		#log this into our audit system.
		$this->Cplogmodel->setUser($this->userID);
		$this->Cplogmodel->setAction("Reordered Board");
		$this->Cplogmodel->setDate(time());
		$this->Cplogmodel->setIp(detectProxy());
		$this->Cplogmodel->logAction();
		
		// validation ok
		$data = array(
				'status' => 'success', 
				'msg' =>  $this->lang->line('reordersuccess')
		);
		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * New board form.
	 * @example index.php/ACP/boards/create/1
	*/
	public function create($type) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render("acp/".strtolower(__CLASS__), 'create', array (
		  'ACPNEWBOARDFORM' => form_open('ACP/boards/createSubmit/', array('name' => 'frmNewBoard', 'id' => 'frmNewBoard', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'TYPE' => $type,
		  "LANG_BOARDNAME" => $this->lang->line('boardname'),
		  "LANG_DESCRIPTION" => $this->lang->line('description'),
		  "LANG_READACCESS" => $this->lang->line('boardread'),
		  "READACCESS_SELECT" => BoardReadAccessSelect(),
		  "LANG_WRITEACCESS" => $this->lang->line('boardwrite'),
		  "WRITEACCESS_SELECT" => BoardWriteAccessSelect(),
		  "LANG_REPLYACCESS" => $this->lang->line('boardreply'),
		  "REPLYACCESS_SELECT" => BoardReplyAccessSelect(),
		  "LANG_VOTEACCESS" => $this->lang->line('boardvote'),
		  "VOTEACCESS_SELECT" => BoardVoteAccessSelect(),
		  "LANG_POLLACCESS" => $this->lang->line('boardpoll'),
		  "POLLACCESS_SELECT" => BoardPollAccessSelect(),
		  "LANG_CATEGORY" => $this->lang->line('parentboard'),
		  "CATEGORY_SELECT" => parentBoardSelection($type == 2 ? "parent" : "child"),
		  "LANG_POSTINCREMENT" => $this->lang->line('postincrement'),
		  "POSTINCREMENT_SELECT" => booleanSelect("question", "increment", NULL, 'id="increment"'),
		  "LANG_BBCODE" => $this->lang->line('bbcode'),
		  "BBCODE_SELECT" => booleanSelect("toggle", "bbcode", NULL, 'id="bbcode"'),
		  "LANG_SMILES" => $this->lang->line('smiles'),
		  "SMILES_SELECT" => booleanSelect("toggle", "smiles", NULL, 'id="smiles"'),
		  "LANG_IMG" => $this->lang->line('img'),
		  "IMG_SELECT" => booleanSelect("toggle", "img", NULL, 'id="img"'),
		  "LANG_NEWBOARD" => $this->lang->line('addboard'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * New board form submit action.
	 * @example index.php/ACP/boards/createSubmit
	*/
	public function createSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');
		$this->load->model('Boardaccessmodel');
		
		$type = $this->input->post('type', TRUE);

		$this->form_validation->set_rules('board_name', $this->lang->line('boardname'), 'required|max_length[50]|xss_clean');
		if ($type == 2 || $type == 3) {
			$this->form_validation->set_rules('description', $this->lang->line('description'), 'required|max_length[100]|xss_clean');
			$this->form_validation->set_rules('category', $this->lang->line('parentboard'), 'required|xss_clean');
		}
		
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			$this->Boardmodel->setBoard($this->input->post('board_name', TRUE));
			$this->Boardmodel->setDescription($type == 2 || $type== 3 ? $this->input->post('description', TRUE) : NULL);
			$this->Boardmodel->setCategory($type == 2 || $type== 3 ? $this->input->post('category', TRUE) : 0);
			$this->Boardmodel->setPostIncrement($type == 2 || $type== 3 ? $this->input->post('increment', TRUE) : 0);
			$this->Boardmodel->setBbCode($type == 2 || $type== 3 ? $this->input->post('bbcode', TRUE) : 0);
			$this->Boardmodel->setSmiles($type == 2 || $type== 3 ? $this->input->post('smiles', TRUE) : 0);
			$this->Boardmodel->setImage($type == 2 || $type== 3 ? $this->input->post('img', TRUE) : 0);
			$this->Boardmodel->setType($type);

			#create board.
			$boardId = $this->Boardmodel->CreateBoard();
			
			$this->Boardaccessmodel->setBRead($this->input->post('readaccess', TRUE));
			$this->Boardaccessmodel->setBPost($type == 2 || $type== 3 ? $this->input->post('writeaccess', TRUE) : 4);
			$this->Boardaccessmodel->setBReply($type == 2 || $type== 3 ? $this->input->post('replyaccess', TRUE) : 4);
			$this->Boardaccessmodel->setBPoll($type == 2 || $type== 3 ? $this->input->post('pollaccess', TRUE) : 4);
			$this->Boardaccessmodel->setBVote($type == 2 || $type== 3 ? $this->input->post('voteaccess', TRUE) : 4);
			$this->Boardaccessmodel->setBAttachment($type == 2 || $type== 3 ? $this->input->post('attachaccess', TRUE) : 4);
			$this->Boardaccessmodel->setBId($boardId);
			
			#create rules for new board.
			$this->Boardaccessmodel->CreateAccessRules();
			
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Created New Board: ".$this->input->post('board_name', TRUE));
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('addboardsuccess'),
            		'fields' => array(
                    	'board_name' => '',
                    	'description' => '',
                    	'category' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
                    	'board_name' => form_error('board_name'),
                    	'description' => form_error('description'),
                    	'category' => form_error('category')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * edit board form
	 * @param integer $id Board ID
	 * @example index.php/ACP/boards/edit
	*/
	public function edit($id) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('form', 'form_select'));
		$this->load->model('Boardaccessmodel');
		
		$boardData = $this->Boardmodel->GetBoardSettings($id);
		$boardAcc = $this->Boardaccessmodel->GetBoardAccess($id);
		
		if ($boardData && $boardAcc) {
			//render to HTML.
			echo $this->twig->render("acp/".strtolower(__CLASS__), 'edit', array (
			  'ACPEDITBOARDFORM' => form_open('ACP/boards/editSubmit/', array('name' => 'frmEditBoard', 'id' => 'frmEditBoard', 'class' => 'form')),
			  'LANG_ERROR' => $this->lang->line('error'),
			  'TYPE' => $this->Boardmodel->getType(),
			  'BOARDID' => $id,
			  "LANG_BOARDNAME" => $this->lang->line('boardname'),
			  "BOARDNAME" => $this->Boardmodel->getBoard(),
			  "LANG_DESCRIPTION" => $this->lang->line('description'),
			  "DESCRIPTION" => $this->Boardmodel->getDescription(),
			  "LANG_READACCESS" => $this->lang->line('boardread'),
			  "READACCESS_SELECT" => BoardReadAccessSelect($this->Boardaccessmodel->getBRead()),
			  "LANG_WRITEACCESS" => $this->lang->line('boardwrite'),
			  "WRITEACCESS_SELECT" => BoardWriteAccessSelect($this->Boardaccessmodel->getBPost()),
			  "LANG_REPLYACCESS" => $this->lang->line('boardreply'),
			  "REPLYACCESS_SELECT" => BoardReplyAccessSelect($this->Boardaccessmodel->getBReply()),
			  "LANG_VOTEACCESS" => $this->lang->line('boardvote'),
			  "VOTEACCESS_SELECT" => BoardVoteAccessSelect($this->Boardaccessmodel->getBVote()),
			  "LANG_POLLACCESS" => $this->lang->line('boardpoll'),
			  "POLLACCESS_SELECT" => BoardPollAccessSelect($this->Boardaccessmodel->getBPoll()),
			  "LANG_CATEGORY" => $this->lang->line('parentboard'),
			  "CATEGORY_SELECT" => parentBoardSelection($this->Boardmodel->getType() == 2 ? "parent" : "child", $this->Boardmodel->getCategory(), $id),
			  "LANG_POSTINCREMENT" => $this->lang->line('postincrement'),
			  "POSTINCREMENT_SELECT" => booleanSelect("question", "increment", $this->Boardmodel->getPostIncrement(), 'id="increment"'),
			  "LANG_BBCODE" => $this->lang->line('bbcode'),
			  "BBCODE_SELECT" => booleanSelect("toggle", "bbcode", $this->Boardmodel->getBbCode(), 'id="bbcode"'),
			  "LANG_SMILES" => $this->lang->line('smiles'),
			  "SMILES_SELECT" => booleanSelect("toggle", "smiles", $this->Boardmodel->getSmiles(), 'id="smiles"'),
			  "LANG_IMG" => $this->lang->line('img'),
			  "IMG_SELECT" => booleanSelect("toggle", "img", $this->Boardmodel->getImage(), 'id="img"'),
			  "LANG_EDITBOARD" => $this->lang->line('modifyboard'),
			  'LANG_FORMFAILURE' => $this->lang->line('formfail')
			));
		} else {
			show_error($this->lang->line('doesntexist'),404,$this->lang->line('error'));
		}
	}
	
	/**
	 * process edit form.
	 * @example index.php/ACP/boards/editSubmit
	*/
	public function editSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');
		$this->load->model('Boardaccessmodel');
		
		$type = $this->input->post('type', TRUE);

		$this->form_validation->set_rules('board_name', $this->lang->line('boardname'), 'required|max_length[50]|xss_clean');
		if ($type == 2 || $type == 3) {
			$this->form_validation->set_rules('description', $this->lang->line('description'), 'required|max_length[100]|xss_clean');
			$this->form_validation->set_rules('category', $this->lang->line('parentboard'), 'required|xss_clean');
		}
		
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			$editBoardData = array(
			  'Board' => $this->input->post('board_name', TRUE),
			  'Description' => $type == 2 || $type== 3 ? $this->input->post('description', TRUE) : NULL,
			  'Category' => $type == 2 || $type== 3 ? $this->input->post('category', TRUE) : 0,
			  'Smiles' => $type == 2 || $type== 3 ? $this->input->post('smiles', TRUE) : 0,
			  'BBcode' => $type == 2 || $type== 3 ? $this->input->post('bbcode', TRUE) : 0,
			  'Post_Increment' => $type == 2 || $type== 3 ? $this->input->post('increment', TRUE) : 0,
			  'Image' => $type == 2 || $type== 3 ? $this->input->post('img', TRUE) : 0
			  );
			$this->Boardmodel->setId($this->input->post('boardId', TRUE));

			#update board.
			$this->Boardmodel->UpdateBoard($editBoardData);

			$boardAccessData = array(
			  'B_Read' => $this->input->post('readaccess', TRUE),
			  'B_Post' => $type == 2 || $type== 3 ? $this->input->post('writeaccess', TRUE) : 4,
			  'B_Reply' => $type == 2 || $type== 3 ? $this->input->post('replyaccess', TRUE) : 4,
			  'B_Vote' => $type == 2 || $type== 3 ? $this->input->post('voteaccess', TRUE) : 4,
			  'B_Poll' => $type == 2 || $type== 3 ? $this->input->post('pollaccess', TRUE) : 4,
			  'B_Attachment' => $type == 2 || $type== 3 ? $this->input->post('attachaccess', TRUE) : 4
			);
			$this->Boardaccessmodel->setBId($this->input->post('boardId', TRUE));

			#update rules.
			$this->Boardaccessmodel->UpdateAccessRules($boardAccessData);

			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Modified Board: ".$this->input->post('board_name', TRUE));
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('editboardsuccess'),
            		'fields' => array(
                    	'board_name' => '',
                    	'description' => '',
                    	'category' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
                    	'board_name' => form_error('board_name'),
                    	'description' => form_error('description'),
                    	'category' => form_error('category')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * Delete board & associated data.
	 * @param integer $id Board ID
	 * @example index.php/ACP/boards/delete
	*/
	public function delete($id) {
		$this->Boardmodel->setId($id);

		#delete board & associated data.
		$this->Boardmodel->DeleteBoard();
		
		#log this into our audit system.
		$this->Cplogmodel->setUser($this->userID);
		$this->Cplogmodel->setAction("Deleted Board");
		$this->Cplogmodel->setDate(time());
		$this->Cplogmodel->setIp(detectProxy());
		$this->Cplogmodel->logAction();
		
		//display success message.
		$this->notifications('success', $this->lang->line('successdeleteboard'));

		#direct user to login page.
		redirect($this->boardUrl.'/ACP/boards/setup', 'location');
	}
	
}