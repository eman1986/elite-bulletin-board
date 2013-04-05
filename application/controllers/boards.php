<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * boards.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 02/18/2013
*/

/**
 * Board Controller.
 * @abstract EBB_Controller 
*/
class Boards extends EBB_Controller {

	function __construct() {
 		parent::__construct();
		$this->load->model(array('Boardmodel', 'Boardaccessmodel'));
		$this->load->helper(array('common', 'posting'));
	}
	
	/**
	 * Index Page for this controller.
	 * @example index.php
	 * @example index.php/
	 * @example index.php/boards
	 * @example index.php/boards/index
	 */
	public function index() {

		$this->load->helper(array('boardindex', 'user', 'form'));
		$this->load->library('encrypt');
		
		$bInx = $this->Boardmodel->loadBoardIndex();

		#setup filters.
		$this->twig->_twig_env->addFilter('counter', new Twig_Filter_Function('GetCount'));
		$this->twig->_twig_env->addFilter('TopicReadStat', new Twig_Filter_Function('readTopicStat'));
		$this->twig->_twig_env->addFilter('SubBoards', new Twig_Filter_Function('getSubBoard'));
		$this->twig->_twig_env->addFunction('boardInfo', new Twig_Function_Function('boardStats'));

		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'index', array (
            'boardName' => $this->title,
			'THEME_NAME' => $this->getStyleName(),
			'STYLELIST' => $this->styleList,
            'pageTitle'=> $this->lang->line('index'),
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
            'LANG_POSTEDBY' => $this->lang->line('Postedby'),
            'groupAccess' => $this->groupAccess,
            'showInfoBox' => $this->preference->getPreferenceValue("infobox_status"),
            'LANG_TICKER' => $this->lang->line('ticker_txt'),
            'ANNOUNCEMENT' => informationPanel(),
            'LANG_BOARD' => $this->lang->line('boards'),
            'LANG_TOPIC' => $this->lang->line('topics'),
            'LANG_POST' => $this->lang->line('posts'),
            'LANG_LASTPOSTDATE' => $this->lang->line('lastposteddate'),
            "LANG_RSS" => $this->lang->line('viewfeed'),
			'LANG_NOPOSTS' => $this->lang->line('noposts'),
            'Category' => $bInx['Parent_Boards'],
            'Boards' => $bInx['Child_Boards'],
			'LAST30DAYS' => time()-3600*24*30,
            "LANG_BOARDSTAT" => $this->lang->line('boardstatus'),
            "LANG_ICONGUIDE" => $this->lang->line('iconguide'),
            "LANG_NEWESTMEMBER" => $this->lang->line('newestmember'),
            "LANG_TOTALTOPIC" => $this->lang->line('topics'),
            "LANG_TOTALPOST" => $this->lang->line('posts'),
            "LANG_TOTALUSER" => $this->lang->line('membernum'),
            "LANG_NEWPOST" => $this->lang->line('newpost'),
            "LANG_OLDPOST" => $this->lang->line('oldpost'),
            "LANG_WHOSONLINE" => $this->lang->line('whosonline'),
            "LANG_ONLINEKEY" => $this->lang->line('onlinekey'),
            "LANG_LOGGED_ONLINE" => $this->lang->line('membernum'),
            "LANG_GUEST_ONLINE" => $this->lang->line('guestonline'),
            "WHOSONLINE"=> whosonline()
		));
	}

	/**
	 * shows list of topics.
	 * @example index.php/boards/viewboard/5
	*/
	public function viewboard($id) {

		//make sure Board ID is defined.
		if (!isset($id) OR (empty($id)) OR (!is_numeric($id))) {
			show_error($this->lang->line('nobid'),500,$this->lang->line('error'));
		} else {

			//if user is trying to access a category board, redirect them, category boards are locked.
			if ($this->Boardmodel->getType() == 1) {
				redirect('/', 'location');
			}

			//load entity.
			$boardOpt = $this->Boardmodel->GetBoardSettings($id);
			$boardAcc = $this->Boardaccessmodel->GetBoardAccess($id);
			
			//see if all board data loaded correctly.
			if ($boardOpt && $boardAcc) {
				/**
				 *  Permission Validation
				*/

				#see if user can view this topic.
				if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBRead())) {
					$CanRead = TRUE;
				} else {
					$CanRead = FALSE;
				}

				#see if user can post a topic or not.
				if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBPost())) {
					//see if we got any group-based permission overwritting the board-level permission.
					if($this->Groupmodel->validateAccess(1, 37)){
						$CanPost = TRUE;
					} else {
						$CanPost = FALSE;
					}
				} else {
					$CanPost = FALSE;
				}

				#see if user can post a topic poll or not.
				if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBPoll())) {
					//see if we got any group-based permission overwritting the board-level permission.
					if($this->Groupmodel->validateAccess(1, 35)) {
						$CanPostPoll = TRUE;
					} else {
						$CanPostPoll = FALSE;
					}
				} else {
					$CanPostPoll = FALSE;
				}
				
				//load pagination library
				$this->load->helper(array('boardindex', 'user', 'form'));
				$this->load->library(array('encrypt', 'pagination', 'breadcrumb'));

				//setup pagination.
				$config = array();
				$config['base_url'] = $this->boardUrl.$this->config->item('index_page').'/viewboard/'.$id;
				$config['total_rows'] = GetCount($id, 'TopicCount');
				$config['per_page'] = $this->preference->getPreferenceValue("per_page");
				$config['uri_segment'] = 3;
				$config['full_tag_open'] = '<div class="pagination">';
				$config['full_tag_close'] = '</div>';
				//$config['next_tag_open'] = '<span class="next_page">';
				//$config['next_tag_close'] = '</span>';
				//$config['prev_tag_open'] = '<span class="prev_page">';
				//$config['prev_tag_close'] = '</span>';
				$config['cur_tag_open'] = '<span class="current">';
				$config['cur_tag_close'] = '</span>';
				$config['next_link'] = '&raquo;';
				$config['prev_link'] = '&laquo;';
				$config['first_link'] = $this->lang->line('pagination_first');
				$config['last_link'] = $this->lang->line('pagination_last');
				$this->pagination->initialize($config);

				//add breadcrumbs
				$this->breadcrumb->append_crumb($this->title, '/');
				$this->breadcrumb->append_crumb($this->Boardmodel->getBoard(), '/viewboard');

				#setup filters.
				$this->twig->_twig_env->addFilter('counter', new Twig_Filter_Function('GetCount'));
				$this->twig->_twig_env->addFilter('TopicReadStat', new Twig_Filter_Function('readTopicStat'));
				$this->twig->_twig_env->addFunction('Attachment', new Twig_Function_Function('HasAttachment'));
				$this->twig->_twig_env->addFunction('SubBoardCount', new Twig_Function_Function('GetSubBoardCount'));
				$this->twig->_twig_env->addFunction('ReadStat', new Twig_Function_Function('readBoardStat'));
				$this->twig->_twig_env->addFilter('SubBoards', new Twig_Filter_Function('getSubBoard'));

				//render to HTML.
				echo $this->twig->render(strtolower(__CLASS__), 'viewboard', array (
                    'boardName' => $this->title,
					'THEME_NAME' => $this->getStyleName(),
					'STYLELIST' => $this->styleList,
                    'pageTitle'=> $this->lang->line('viewboard').' - '.$this->Boardmodel->getBoard(),
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
                    'LANG_POSTEDBY' => $this->lang->line('Postedby'),
                    'LANG_BTNLOCKED' => $this->lang->line('btnlocked'),
                    'LANG_BTNNEWTOPIC' => $this->lang->line('newtopic'),
                    'LANG_BTNNEWPOLL' => $this->lang->line('newpoll'),
                    'groupAccess' => $this->groupAccess,
                    'BOARDID' => $id,
					'BOARD_NAME' => $this->Boardmodel->getBoard(),
                    'BOARDCOUNT' => GetCount($id, 'TopicCount'),
                    'BOARDDATA' => $this->Boardmodel->GetTopics($id, $config['per_page'], $this->uri->segment(3)),
                    'SUBBOARDDATA' => $this->Boardmodel->GetSubBoards($id),
					'LAST30DAYS' => time()-3600*24*30,
                    'PAGINATION' => $this->pagination->create_links(),
                    'BREADCRUMB' => $this->breadcrumb->output(),
                    'LANG_NOREAD' => $this->lang->line('noread'),
                    'LANG_NOPOST' => $this->lang->line('nopost'),
                    'CANREAD_TOPIC' => $CanRead,
                    'CANPOST_TOPIC' => $CanPost,
                    'CANPOST_POLL' => $CanPostPoll,
                    'LANG_NEWPOST' => $this->lang->line('newpost'),
                    'LANG_OLDPOST' => $this->lang->line('oldpost'),
                    'LANG_POLLTOPIC' => $this->lang->line('polltopic'),
                    'LANG_LOCKTOPIC' => $this->lang->line('lockedtopic'),
                    'LANG_IMPORTANTTOPIC' => $this->lang->line('importanttopic'),
                    'LANG_HOTTOPIC' => $this->lang->line('hottopic'),
                    'LANG_BOARD' => $this->lang->line('boards'),
                    'LANG_TOPIC' => $this->lang->line('topics'),
                    'LANG_POSTEDBY' => $this->lang->line('Postedby'),
                    'LANG_REPLIES' => $this->lang->line('replies'),
                    'LANG_POSTVIEWS' => $this->lang->line('views'),
                    'LANG_POST' => $this->lang->line('posts'),
                    'LANG_LASTPOSTDATE' => $this->lang->line('lastposteddate'),
                    'LANG_LASTPOSTEDBY' => $this->lang->line('lastpost'),
                    'LANG_POSTEDBY' => $this->lang->line('Postedby'),
				    'LANG_NOPOSTS' => $this->lang->line('noposts')
				));
			} else {
				show_error($this->lang->line('doesntexist'),404,$this->lang->line('error'));
			}
		}
	}

	/**
	 * shows topics & all replies tied to it.
	 * @example index.php/boards/viewtopic/5
	*/
	public function viewtopic($id) {

		//make sure Topic ID is defined.
		if (!isset($id) OR (empty($id)) OR (!is_numeric($id))) {
			show_error($this->lang->line('notid'),500,$this->lang->line('error'));
		} else {
			//load library & helpers
			$this->load->helper(array('boardindex', 'user', 'form', 'posting', 'group', 'attachment', 'form_select'));
			$this->load->library(array('encrypt', 'pagination', 'breadcrumb'));

			//load topic model.
			$this->load->model('Topicmodel');

			//load entities
			$tData = $this->Topicmodel->GetTopicData($id);
			$boardOpt = $this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
			$boardAcc = $this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());

			//ensure everything loaded correctly.
			if ($tData && $boardOpt && $boardAcc) {
				/**
				* Permission Validation
				*/

				#see if user can view this topic.
				if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBRead()) == false) {
					show_error($this->lang->line('noread'), 403, $this->lang->line('error'));
				}

				#see if user can post a reply or not.
				if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBReply())) {
					//see if we got any group-based permission overwritting the board-level permission.
					if($this->Groupmodel->validateAccess(1, 38)){
						$CanReply = TRUE;
					} else {
						$CanReply = FALSE;
					}
				} else {
					$CanReply = FALSE;
				}

				#see if user can vote.
				if ($this->Groupmodel->ValidateAccess(0, $this->Boardaccessmodel->getBVote())) {
					//see if we got any group-based permission overwritting the board-level permission.
					if($this->Groupmodel->ValidateAccess(1, 36)) {
						$CanVote = TRUE;
					} else {
						$CanVote = FALSE;
					}
				} else {
					$CanVote = TRUE;
				}

				#see if this user can delete a topic as a moderator/admin.
				if($this->Groupmodel->ValidateAccess(1, 20)){
					$CanEditACL = TRUE;
				} else {
					$CanEditACL = FALSE;
				}		

				#see if this user can delete a topic as a moderator/admin.
				if($this->Groupmodel->ValidateAccess(1, 21)){
					$CanDeleteACL = TRUE;
				} else {
					$CanDeleteACL = FALSE;
				}

				#see if user can move topic.
				if($this->Groupmodel->ValidateAccess(1, 23)){
					$CanMoveACL = TRUE;
				} else {
					$CanMoveACL = FALSE;
				}

				#see if user can lock/unlock a topic.
				if($this->Groupmodel->ValidateAccess(1, 22)){
					$CanToggleLockACL = TRUE;
				} else {
					$CanToggleLockACL = FALSE;
				}

				#see if user can alter warning levels.
				if($this->Groupmodel->validateAccess(1, 25)){
					$canAlterWarn = TRUE;
				} else {
					$canAlterWarn = FALSE;
				}

				#see if user can view poster's IP.
				if($this->Groupmodel->ValidateAccess(1, 24)){
					$canSeeIP = TRUE;
				} else {
					$canSeeIP = FALSE;
				}

				//see if we got any group-based permission overwritting the board-level permission.
				if($this->Groupmodel->ValidateAccess(1, 40)){
					$CanEdit = TRUE;
				} else {
					$CanEdit = FALSE;
				}

				#see if user can delete topic.
				if($this->Groupmodel->ValidateAccess(1, 41)){
					$CanDelete = TRUE;
				} else {
					$CanDelete = FALSE;
				}

				#see if user can upload
				if ($this->Groupmodel->ValidateAccess(1, 26)) {
					$uploadLimit = $this->preference->getPreferenceValue("upload_limit");	
				} else {
					$uploadLimit = 0;
				}

				//perform some tasks only for logged in users.
				if ($this->logged_user != "guest") {
					//mark this topic as read.
					if (readTopicStat($id, $this->userID) == 0 && $this->Topicmodel->getLastUpdate() >= time()-3600*24*30) {
						$data = array(
						  'Topic' => $id,
						  'User' => $this->userID
						);
						$this->db->insert('ebb_read_topic', $data);
					}
					
					//increase post view.
					if ($this->logged_user != $this->Topicmodel->getAuthor()) {
						$this->Topicmodel->increaseTopicView();
					}
				}
				
				/**
				* Setup Pagination.
				*/
				$config = array();
				$config['base_url'] = $this->boardUrl.$this->config->item('index_page').'/viewtopic/'.$id;
				$config['total_rows'] = GetCount($id, 'TopicReplies');
				$config['per_page'] = $this->preference->getPreferenceValue("per_page");
				$config['uri_segment'] = 3;
				$config['full_tag_open'] = '<div class="pagination">';
				$config['full_tag_close'] = '</div>';
				//$config['next_tag_open'] = '<span class="next_page">';
				//$config['next_tag_close'] = '</span>';
				//$config['prev_tag_open'] = '<span class="prev_page">';
				//$config['prev_tag_close'] = '</span>';
				$config['cur_tag_open'] = '<span class="current">';
				$config['cur_tag_close'] = '</span>';
				$config['next_link'] = '&raquo;';
				$config['prev_link'] = '&laquo;';
				$config['first_link'] = $this->lang->line('pagination_first');
				$config['last_link'] = $this->lang->line('pagination_last');
				$this->pagination->initialize($config);

				//add breadcrumbs
				$this->breadcrumb->append_crumb($this->title, '/');
				$this->breadcrumb->append_crumb($this->Boardmodel->getBoard(), '/viewboard/'.$this->Topicmodel->getBid());
				$this->breadcrumb->append_crumb($this->Topicmodel->getTopic(), '/viewtopic');

				#setup filters.
				$this->twig->_twig_env->addFilter('counter', new Twig_Filter_Function('GetCount'));
				$this->twig->_twig_env->addFilter('TopicReadStat', new Twig_Filter_Function('readTopicStat'));
				$this->twig->_twig_env->addFunction('FormatMsg', new Twig_Function_Function('FormatTopicBody'));
				$this->twig->_twig_env->addFunction('Spam_Filter', new Twig_Function_Function('language_filter'));
				$this->twig->_twig_env->addFunction('ATTACH_BAR', new Twig_Function_Function('GetAttachments'));
				$this->twig->_twig_env->addFunction('MATH_ROUND', new Twig_Function_Function('Round'));
				$this->twig->_twig_env->addFunction('CALC_VOTE', new Twig_Function_Function('CalcVotes'));
				$this->twig->_twig_env->addFunction('VOTECHECK', new Twig_Function_Function('CheckVoteStatus'));
				$this->twig->_twig_env->addFunction('GRAVATAR', new Twig_Function_Function('getGravatar'));

				//Grab some settings.
				$disable_bbcode = $this->Topicmodel->getDisableBbCode();
				$disable_smiles = $this->Topicmodel->getDisableSmiles();
				$boardpref_bbcode = $this->Boardmodel->getBbCode();
				$boardpref_smiles = $this->Boardmodel->getSmiles();
				$boardpref_image = $this->Boardmodel->getImage();

				//render to HTML.
				echo $this->twig->render(strtolower(__CLASS__), 'viewtopic', array (
				'boardName' => $this->title,
				'THEME_NAME' => $this->getStyleName(),
				'STYLELIST' => $this->styleList,
				'pageTitle'=> $this->lang->line('viewtopic').' - '.$this->Topicmodel->getTopic(),
				'INDEX_PAGE' => $this->config->item('index_page'),
				'BOARD_URL' => $this->boardUrl,
				'APP_URL' => $this->boardUrl.APPPATH,
				'NOTIFY_TYPE' => $this->notifyType,
				'NOTIFY_MSG' =>  $this->notifyMsg,
				'LANG' => $this->lng,
				'TimeFormat' => $this->dateTimeFormat,
				'TimeZone' => $this->timeZone,
				'LANG_ERROR' => $this->lang->line('error'),
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
				'LANG_PROFILE' => $this->lang->line('viewprofile'),
				'LANG_CHANGETHEME' => $this->lang->line('changetheme'),
				'LANG_POWERED' => $this->lang->line('poweredby'),
				'LANG_POSTEDBY' => $this->lang->line('Postedby'),
				'LANG_BTNLOCKED' => $this->lang->line('btnlocked'),
				'LANG_BTNREPLY' => $this->lang->line('postreply'),
				'LANG_BTNMOVETOPIC' => $this->lang->line('movetopic'),
				'LANG_BTNDELETETOPIC' => $this->lang->line('btndeletetopic'),
				'LANG_BTNLOCKTOPIC' => $this->lang->line('btnlocktopic'),
				'LANG_BTNUNLOCKTOPIC' => $this->lang->line('btnunlocktopic'),
				'LANG_BTNEDITPOST' => $this->lang->line('editpost'),
				'LANG_BTNDELETEPOST' => $this->lang->line('btndeletemessage'),
				'LANG_BTNQUOTEAUTHOR' => $this->lang->line('btnquoteauthor'),
				'LANG_BTNPMAUTHOR' => $this->lang->line('btnpmauthor'),
				'LANG_BTNREPORTPOST' => $this->lang->line('reporttomod'),
				'groupAccess' => $this->groupAccess,
				'LANG_PRINT' =>  $this->lang->line('ptitle'),
				'LANG_POSTCOUNT' => $this->lang->line('postcount'),
				'LANG_DELPROMPT' => $this->lang->line('topiccon'),
				'LANG_DELPROMPT2' => $this->lang->line('postcon'),
				'TOPICID' => $id,
				'MOVETOPICFORM' => form_open('maintenance/move/'),
				'MOVETOPIC_SELECT' => boardListSelect($this->Topicmodel->getBid()),
				'BOARDID' => $this->Topicmodel->getBid(),
				'LANG_MOVETOPIC' => $this->lang->line('movetopic'),
				'DISABLE_SMILES' => $disable_smiles,
				'BOARDPREF_SMILES' => $boardpref_smiles,
				'DISABLE_BBCODE' => $disable_bbcode,
				'BOARDPREF_BBCODE' => $boardpref_bbcode,
				'BOARDPREF_IMAGE' => $boardpref_image,
				'PREF_AVATARTYPE' => $this->preference->getPreferenceValue("avatar_type"),
				'PREF_GRAVATAR_SIZE' => $this->preference->getPreferenceValue("gravatar_size"),
				'PREF_GRAVATAR_NOAVATAR' => $this->preference->getPreferenceValue("gravatar_noresults"),
				'PREF_GRAVATAR_RATING' => $this->preference->getPreferenceValue("gravatar_rating"),
				'PREF_GRAVATAR_SECURE' => $this->preference->getPreferenceValue("gravatar_secure"),
				'TOPIC_LOCKED' =>$this->Topicmodel->getLocked(),
				'LANG_WARNLEVEL' => $this->lang->line('warnlevel'),
				'TOPIC_TYPE' => $this->Topicmodel->getTopicType(),
				'TOPIC_SUBJECT' => $this->Topicmodel->getTopic(),
				'TOPIC_BODY' => $this->Topicmodel->getBody(),
				'TOPIC_AUTHOR' => $this->Topicmodel->getAuthor(),
				'TOPIC_USERID' => $this->Topicmodel->getUId(),
				'TOPIC_IP' => $this->Topicmodel->getIp(),
				'TOPIC_POSTEDON' => $this->Topicmodel->getOriginalDate(),
				'AUTHOR_GROUPNAME' => $this->Topicmodel->getGroupProfile(),
				'AUTHOR_GROUPLEVEL' => $this->Topicmodel->getGroupAccess(),
				'AUTHOR_EMAIL' => $this->Topicmodel->getEmail(),
				'AUTHOR_AVATAR' => $this->Topicmodel->getAvatar(),
				'AUTHOR_GID' => $this->Topicmodel->getGid(),
				'AUTHOR_POSTCOUNT' => $this->Topicmodel->getPostCount(),
				'AUTHOR_SIG' => $this->Topicmodel->getSig(),
				'AUTHOR_WARNLEVEL' => $this->Topicmodel->getWarningLevel(),
				'AUTHOR_CTITLE' => $this->Topicmodel->getCustomTitle(),
				'LANG_DOWNLOADS' => $this->lang->line('downloadct'),
				'LANG_ATTACHMENTS' => $this->lang->line('attachments'),
				'LANG_POSTED' => $this->lang->line('postedon'),
				'LANG_IP'  => $this->lang->line('ipmod'),
				'LANG_IPLOGGED' => $this->lang->line('iplogged'),
				'POLLFORM' => form_open('boards/vote/'.$id, array("class" => "form")),
				'POLL_QUESTION' => $this->Topicmodel->getQuestion(),
				'POLLDATA' => $this->Topicmodel->GetPoll($id),
				'LANG_VOTE' => $this->lang->line('castvote'),
				'LANG_TOTAL' => $this->lang->line('total'),
				'TOTAL_VOTES' => GetCount($id, 'PollCount'),
				'REPLYDATA' => $this->Topicmodel->GetReplies($id, $config['per_page'], $this->uri->segment(3)),
				'PAGINATION' => $this->pagination->create_links(),
				'FORM_PAGE' => $this->uri->segment(4),
				'BREADCRUMB' => $this->breadcrumb->output(),
				'CANPOST_REPLY' => $CanReply,
				'CANVOTE' => $CanVote,
				'GAC_WARNINGLEVELS' => $canAlterWarn,
				'GAC_SEEIP' => $canSeeIP,
				'GAC_EDITTOPIC' => $CanEdit,
				'GAC_EDITTOPICACL' => $CanEditACL,
				'GAC_DELETETOPIC' => $CanDelete,
				'GAC_DELETEACL' => $CanDeleteACL,
				'GAC_MOVEACL' => $CanMoveACL,
				'GAC_TOGGLELOCKACL' => $CanToggleLockACL,
				'QREPLYFORM' => form_open('boards/reply/'.$id, array('name' => 'frmQReply', 'class' => 'form')),
				'UPLOADFORM' => form_open_multipart('upload/do_upload/'),
				'SMILES' => form_smiles(),
				'LANG_REPLY' => $this->lang->line('btnreply'),
				'LANG_OPTIONS' => $this->lang->line('options'),
				'LANG_DISABLERTF' => $this->lang->line('disablertf'),
				'LANG_SMILES' => $this->lang->line('moresmiles'),
				'LANG_NOTIFY' => $this->lang->line('notify'),
				'LANG_DISABLESMILES' => $this->lang->line('disablesmiles'),
				'LANG_DISABLEBBCODE' => $this->lang->line('disablebbcode'),
				"LANG_UPLOAD" => $this->lang->line("uploadfile"),
				"LANG_CLEAR" => $this->lang->line("clearfile"),
				"LANG_ADDMOREFILES" => $this->lang->line("addMoreFiles"),
				"ATTACHMENTLIMIT" => $uploadLimit
				));
			} else {
				show_error($this->lang->line('doesntexist'), 403, $this->lang->line('error'));
			}
		}
	}
	
	/**
	 * download attachment from topic/post.
	 * @example index.php/boards/download/5
	*/
	public function download($id) {
		//guests are not allowed to access this page.
		if ($this->groupAccess == 0) {
			//alert user.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		} else {
			//load topic & attachments model.
			$this->load->model(array('Topicmodel', 'Attachmentsmodel'));
			
			//load helpers
			$this->load->helper('download');
			
			//get file.
			$attachData = $this->Attachmentsmodel->GetAttachment($id);
			
			//did the attachment data load correctly?
			if ($attachData) {
				
				//increment download count.
				$this->Attachmentsmodel->setDownloadCount($this->Attachmentsmodel->getDownloadCount() + 1);
				$this->Attachmentsmodel->IncrementDownloadCounter();
				
				$dwnloadPath = $this->config->item('upload_path').$this->Attachmentsmodel->getEncryptedFileName();
				
				#Send file for download.
				$stream = fopen($dwnloadPath, 'rb');
				if ($stream){
					if (!feof($stream) && connection_status() == 0) {
						#reset time limit for big files.
						set_time_limit(0);
						$fileData = fread($stream,filesize($dwnloadPath));
					}
					fclose($stream);

					//force file to download.
					force_download($this->Attachmentsmodel->getFileName(), $fileData);
				}
			} else {
				show_error($this->lang->line('noattachments'),500,$this->lang->line('error'));
			}
		}
	}
    
    /**
	 * post new topic on board.
	 * @example index.php/boards/printable/5
	*/
    public function printable($id) {
        //make sure Topic ID is defined.
		if (!isset($id) OR (empty($id)) OR (!is_numeric($id))) {
			show_error($this->lang->line('notid'),500,$this->lang->line('error'));
		} else {
			//load library & helpers
			$this->load->helper(array('boardindex', 'user', 'form', 'posting', 'group', 'attachment'));
			$this->load->library(array('encrypt', 'pagination', 'breadcrumb'));

			//load topic model.
			$this->load->model('Topicmodel');

			//load entities
			$tData = $this->Topicmodel->GetTopicData($id);
			$boardOpt = $this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
			$boardAcc = $this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());

			//ensure everything loaded correctly.
			if ($tData && $boardOpt && $boardAcc) {
				/**
				* Permission Validation
				*/

				#see if user can view this topic.
				if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBRead()) == false){
					show_error($this->lang->line('noread'), 403, $this->lang->line('error'));
				}

				#setup filters.
				$this->twig->_twig_env->addFunction('FormatMsg', new Twig_Function_Function('FormatTopicBody'));
				$this->twig->_twig_env->addFunction('Spam_Filter', new Twig_Function_Function('language_filter'));

				//Grab some settings.
				$disable_bbcode = $this->Topicmodel->getDisableBbCode();
				$disable_smiles = $this->Topicmodel->getDisableSmiles();
				$boardpref_bbcode = $this->Boardmodel->getBbCode();
				$boardpref_smiles = $this->Boardmodel->getSmiles();
				$boardpref_image = $this->Boardmodel->getImage();

				//render to HTML.
				echo $this->twig->render(strtolower(__CLASS__), 'printable', array (
                    'boardName' => $this->title,
					'THEME_NAME' => $this->getStyleName(),
                    'pageTitle'=> $this->lang->line('viewtopic').' - '.$this->Topicmodel->getTopic(),
                    'TimeFormat' => $this->dateTimeFormat,
                    'TimeZone' => $this->timeZone,
                    'TOPICID' => $id,
                    'DISABLE_SMILES' => $disable_smiles,
                    'BOARDPREF_SMILES' => $boardpref_smiles,
                    'DISABLE_BBCODE' => $disable_bbcode,
                    'BOARDPREF_BBCODE' => $boardpref_bbcode,
                    'BOARDPREF_IMAGE' => $boardpref_image,
                    'LANG_VIEWORIGTOPIC' => $this->lang->line('vieworiginal'),
                    'TOPIC_SUBJECT' => $this->Topicmodel->getTopic(),
                    'TOPIC_BODY' => $this->Topicmodel->getBody(),
                    'TOPIC_AUTHOR' => $this->Topicmodel->getAuthor(),
                    'TOPIC_POSTEDON' => $this->Topicmodel->getOriginalDate(),
                    'LANG_POSTED' => $this->lang->line('postedon'),
                    'REPLYDATA' => $this->Topicmodel->GetReplies($id, null, null),
                    'LANG_BOARD' => $this->lang->line('boards'),
                    'LANG_TOPIC' => $this->lang->line('topics'),
                    'LANG_POSTEDBY' => $this->lang->line('Postedby'),
                    'LANG_POWERED' => $this->lang->line('poweredby'),
				));
			} else {
				show_error($this->lang->line('doesntexist'), 403, $this->lang->line('error'));
			}
		}
    }
	
	/**
	 * post new topic on board.
	 * @example index.php/boards/newtopic/5
	*/
	public function newtopic($bid) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'email', 'form_validation'));
        $this->load->helper(array('form', 'user', 'posting'));
		
		//get board settings.
		$this->Boardmodel->GetBoardSettings($bid);
		
		//see if user can post on this board.
		if (!$this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBPost())){
			show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
		} else {
			if (!$this->Groupmodel->ValidateAccess(1, 37)){
				show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
			}
		}
		
		#see if user can mark topics as important.
		if ($this->Groupmodel->ValidateAccess(1, 39)){
			$CanImportant = TRUE;
		} else {
			$CanImportant = FALSE;
		}
		
		//setup validation rules.
        $this->form_validation->set_rules('topic', $this->lang->line('topic'), 'required|min_length[5]|max_length[50]|callback_SpamFilter|xss_clean');
        $this->form_validation->set_rules('post', $this->lang->line('topicbody'), 'required|min_length[10]|callback_SpamFilter|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="flash"><div class="message error">', '</div></div>');

		//see if any validation rules failed.
		if ($this->form_validation->run() == FALSE) {
			$this->FORM_newtopic($bid, false);
		} else {
			
			//flood check,
			if ($this->groupAccess == 3 && flood_check("posting", $this->Usermodel->getLastPost())) {
				#setup error session.
				$this->notifications('warning', $this->lang->line('flood'));

				#direct user.
				redirect('/viewboard/'.$bid, 'location');
				exit();
			}
			
			//load topic & attachments model.
			$this->load->model(array('Topicmodel', 'Attachmentsmodel'));
			
			//CREATE NEW TOPIC.
			$no_smile = ($this->input->post('no_smile', TRUE) == FALSE) ? FALSE : TRUE;
			$no_bbcode = ($this->input->post('no_bbcode', TRUE) == FALSE) ? FALSE : TRUE;
			$subscribe = ($this->input->post('subscribe', TRUE) == FALSE) ? FALSE : TRUE;
			//$page = ($this->input->post('page', TRUE) == FALSE) ? null : $this->input->post('page', TRUE);
			$time = time();
			
			//create new topic.
			$this->Topicmodel->setAuthor($this->userID);
			$this->Topicmodel->setBid($bid);
			$this->Topicmodel->setTopic($this->input->post('topic', TRUE));
			$this->Topicmodel->setBody($this->input->post('post', TRUE));
			$this->Topicmodel->setDisableBbCode($no_bbcode);
			$this->Topicmodel->setDisableSmiles($no_smile);
			$this->Topicmodel->setImportant($this->input->post('post_type', TRUE));
			$this->Topicmodel->setIp(detectProxy());
			$this->Topicmodel->setLastUpdate($time);
			$this->Topicmodel->setOriginalDate($time);
			$this->Topicmodel->setLocked(0);
			$this->Topicmodel->setPostedUser($this->userID);
			$this->Topicmodel->setQuestion(null);
			$this->Topicmodel->setTopicType(0);
			$this->Topicmodel->setViews(0);
			
			$newTopicId = $this->Topicmodel->CreateTopic(); //create topic, get Topic ID.
			
			//see if user wants to subscribe to this topic.
			if ($subscribe) {
				subscriptionManager($this->userID, $newTopicId, "subscribe");
			}

			#update board & topic details.
			update_board($bid, $newTopicId, $time, $this->userID);
			update_topic($newTopicId, $time, $this->userID);
			
			//update user's last post.
			update_user($this->userID);
			
			#see if this board can allow post count increase.
			if($this->Boardmodel->getPostIncrement() == 1){
				//get current post count then add on to it.
				post_count($this->userID);
			}
			
			//validate user can attach files.
			if($this->Groupmodel->ValidateAccess(1, 26)){ 
				#see if user uploaded a file, if so lets assign the file to the topic.
				$this->db->select('id')
					->from('ebb_attachments')
					->where('Username', $this->userID)
					->where('tid', 0)
					->where('pid', 0);
				$query = $this->db->get();
				
				//see if we have anything to assign first.
				if($query->num_rows() > 0) {
					foreach ($query->result() as $row) {
						#add attachment to db for listing purpose.
						$this->Attachmentsmodel->AssignAttachment($newTopicId, 0, $row->id);
					}
				}
			}
			
			//direct user to topic.
			redirect('/viewtopic/'.$newTopicId, 'location');
		}
	}
	
	/**
	 * post new topic poll on board.
	 * @example index.php/boards/newpoll/5
	*/
	public function newpoll($bid) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'email', 'form_validation'));
        $this->load->helper(array('form', 'user', 'posting'));
		
		//get board settings.
		$this->Boardmodel->GetBoardSettings($bid);
		
		//see if user can post on this board.
		if (!$this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBPoll())){
			show_error($this->lang->line('nopoll'),403,$this->lang->line('error'));
		} else {
			if (!$this->Groupmodel->ValidateAccess(1, 35)){
				show_error($this->lang->line('nopoll'),403,$this->lang->line('error'));
			}
		}
		
		#see if user can mark topics as important.
		if ($this->Groupmodel->ValidateAccess(1, 39)){
			$CanImportant = TRUE;
		} else {
			$CanImportant = FALSE;
		}
		
		//setup validation rules.
        $this->form_validation->set_rules('topic', $this->lang->line('topic'), 'required|min_length[5]|max_length[50]|callback_SpamFilter|xss_clean');
        $this->form_validation->set_rules('post', $this->lang->line('topicbody'), 'required|min_length[10]|callback_SpamFilter|xss_clean');
		$this->form_validation->set_rules('question', $this->lang->line('question'), 'required|min_length[5]|max_length[50]|xss_clean');
		$this->form_validation->set_rules('pollOpts', $this->lang->line('polloptionfield'), 'required|callback_PollOptionValidation|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="flash"><div class="message error">', '</div></div>');

		//see if any validation rules failed.
		if ($this->form_validation->run() == FALSE) {
			$this->FORM_newtopic($bid, true);
		} else {
			
			//flood check,
			if ($this->groupAccess == 3 && flood_check("posting", $this->Usermodel->getLastPost())) {
				#setup error session.
				$this->notifications('warning', $this->lang->line('flood'));

				#direct user.
				redirect('/viewboard/'.$bid, 'location');
				exit();
			}
			
			//load topic & attachments model.
			$this->load->model(array('Topicmodel', 'Attachmentsmodel'));
			
			//CREATE NEW TOPIC.
			$no_smile = ($this->input->post('no_smile', TRUE) == FALSE) ? FALSE : TRUE;
			$no_bbcode = ($this->input->post('no_bbcode', TRUE) == FALSE) ? FALSE : TRUE;
			$subscribe = ($this->input->post('subscribe', TRUE) == FALSE) ? FALSE : TRUE;
			$pollOptions = explode(PHP_EOL, $this->input->post('pollOpts', TRUE));
			$time = time();
			
			//create new topic.
			$this->Topicmodel->setAuthor($this->userID);
			$this->Topicmodel->setBid($bid);
			$this->Topicmodel->setTopic($this->input->post('topic', TRUE));
			$this->Topicmodel->setBody($this->input->post('post', TRUE));
			$this->Topicmodel->setDisableBbCode($no_bbcode);
			$this->Topicmodel->setDisableSmiles($no_smile);
			$this->Topicmodel->setImportant($this->input->post('post_type', TRUE));
			$this->Topicmodel->setIp(detectProxy());
			$this->Topicmodel->setLastUpdate($time);
			$this->Topicmodel->setOriginalDate($time);
			$this->Topicmodel->setLocked(0);
			$this->Topicmodel->setPostedUser($this->userID);
			$this->Topicmodel->setQuestion($this->input->post('question', TRUE));
			$this->Topicmodel->setTopicType(1);
			$this->Topicmodel->setViews(0);
			
			$newTopicId = $this->Topicmodel->CreateTopic(); //create topic, get Topic ID.
			
			//loop through our poll options.
			for ($i = 0; $i <= count($pollOptions)-1; $i++) {
				//ensure no blank values get through.
				if (strlen($pollOptions[$i]) > 1) {
					$this->Topicmodel->CreatePoll($pollOptions[$i], $newTopicId); //create poll options
				}
			}
			
			//see if user wants to subscribe to this topic.
			if ($subscribe) {
				subscriptionManager($this->userID, $newTopicId, "subscribe");
			}

			#update board & topic details.
			update_board($bid, $newTopicId, $time, $this->userID);
			update_topic($newTopicId, $time, $this->userID);
			
			//update user's last post.
			update_user($this->userID);
			
			#see if this board can allow post count increase.
			if($this->Boardmodel->getPostIncrement() == 1){
				//get current post count then add on to it.
				post_count($this->userID);
			}
			
			//validate user can attach files.
			if($this->Groupmodel->ValidateAccess(1, 26)){ 
				#see if user uploaded a file, if so lets assign the file to the topic.
				$this->db->select('id')
					->from('ebb_attachments')
					->where('Username', $this->userID)
					->where('tid', 0)
					->where('pid', 0);
				$query = $this->db->get();
				
				//see if we have anything to assign first.
				if($query->num_rows() > 0) {
					foreach ($query->result() as $row) {
						#add attachment to db for listing purpose.
						$this->Attachmentsmodel->AssignAttachment($newTopicId, 0, $row->id);
					}
				}
			}
			
			//direct user to topic.
			redirect('/viewtopic/'.$newTopicId, 'location');
			
		}
	}
	
	/**
	 * New Topics form.
	 * @params integer $bid Board ID
	 * @params boolean $pollTopic Is this a poll topic?
	 * @access private
	*/
	private function FORM_newtopic($bid, $pollTopic) {

		//load breadcrumb library
		$this->load->library('breadcrumb');
		
		//get board settings.
		$this->Boardmodel->GetBoardSettings($bid);
		
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->Boardmodel->getBoard(), '/viewboard/'.$bid);
		$this->breadcrumb->append_crumb($this->lang->line("posttopic"), '/newtopic');

		//grab board preferences.
		$boardpref_bbcode = $this->Boardmodel->getBbCode();
		$boardpref_smiles = $this->Boardmodel->getSmiles();
		$boardpref_image = $this->Boardmodel->getImage();
		
		#see if user can mark topics as important.
		if ($this->Groupmodel->ValidateAccess(1, 39)){
			$CanImportant = TRUE;
		} else {
			$CanImportant = FALSE;
		}
		
		#see if poll topic is enabled and user can post a poll topic.
		if (($pollTopic) AND ($this->Groupmodel->ValidateAccess(1, 35))){
			$CanPoll = TRUE;
		} else {
			$CanPoll = FALSE;
		}
		
		#see if user can upload
		if ($this->Groupmodel->ValidateAccess(1, 26)) {
			$uploadLimit = $this->preference->getPreferenceValue("upload_limit");	
		} else {
			$uploadLimit = 0;
		}
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'newtopic', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("newtopic"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG' => $this->lng,
		  'TimeFormat' => $this->dateTimeFormat,
		  'TimeZone' => $this->timeZone,
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
		  'LOGGEDUSERID' => $this->userID,
		  'LANG_JSDISABLED' => $this->lang->line('jsdisabled'),
		  'LANG_INFO' => $this->lang->line('info'),
		  'LANG_LOGIN' => $this->lang->line('login'),
		  'LANG_LOGOUT' => $this->lang->line('logout'),
		  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin')),
		  'NEWTOPICFORM' => form_open('boards/newtopic/'.$bid, array('name' => 'frmNewTopic', 'id' => 'frmNewTopic', 'class' => 'form')),
		  'NEWPOLLFORM' => form_open('boards/newpoll/'.$bid, array('name' => 'frmNewTopic', 'id' => 'frmNewTopic', 'class' => 'form')),
		  'UPLOADFORM' => form_open_multipart('upload/do_upload/'),
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
		  'LANG_POSTEDBY' => $this->lang->line('Postedby'),
		  'BREADCRUMB' =>$this->breadcrumb->output(),
		  "LANG_POSTINGRULES" => $this->lang->line("postingrules"),
		  "LANG_ALLOWSMILES" => $this->lang->line("smiles"),
		  "ALLOWSMILES" => $boardpref_smiles,
		  "LANG_ALLOWBBCODE" => $this->lang->line("bbcode"),
		  "ALLOWBBCODE" => $boardpref_bbcode,
		  "LANG_ALLOWIMG" => $this->lang->line("img"),
		  "ALLOWIMG" => $boardpref_image,
		  "BID" => $bid,
		  "POLLOPTION" => $pollTopic,
		  "LANG_SMILES" => $this->lang->line("moresmiles"),
		  "SMILES" => form_smiles(),
		  "LANG_TOPIC" => $this->lang->line("topic"),
		  "LANG_TOPICBODY" => $this->lang->line('topicbody'),
		  "LANG_UPLOAD" => $this->lang->line("uploadfile"),
		  "LANG_CLEAR" => $this->lang->line("clearfile"),
		  "LANG_ADDMOREFILES" => $this->lang->line("addMoreFiles"),
		  "ATTACHMENTLIMIT" => $uploadLimit,
		  "LANG_DISABLERTF" => $this->lang->line("disablertf"),
		  "LANG_OPTIONS" => $this->lang->line("options"),
		  "LANG_POSTTYPE" => $this->lang->line("type"),
		  "GAC_IMPORTANT" => $CanImportant,
		  "LANG_IMPORTANT" => $this->lang->line("important"),
		  "LANG_NORMAL" => $this->lang->line("normal"),
		  "LANG_NOTIFY" => $this->lang->line("notify"),
		  "LANG_DISABLESMILES" => $this->lang->line("disablesmiles"),
		  "LANG_DISABLEBBCODE" => $this->lang->line("disablebbcode"),
		  "LANG_POLL" => $this->lang->line("polltext"),
		  "LANG_POLLOPTIONS" => $this->lang->line('polloptionfield'),
		  "LANG_QUESTION" => $this->lang->line("question"),
		  "LANG_POSTTOPIC" => $this->lang->line("posttopic")
		));
	}
	
	/**
	 * used to quote a topic or post.
	 * @param integer $tid Topic.
	 * @param integer $pid Post ID.
	 * @param integer $type topic=1;post=2.
	 * @example index.php/boards/quote/5/5/2
	 */
	public function quote($tid, $pid, $type) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'email', 'form_validation'));
        $this->load->helper(array('form', 'user', 'posting'));
		
		$this->FORM_reply($tid, $pid, $type);
	}
	
	/**
	 * reply to a topic.
	 * @example index.php/boards/reply/5/20
	*/
	public function reply($tid) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'email', 'form_validation'));
        $this->load->helper(array('form', 'user', 'posting'));
		
		//load topic model.
		$this->load->model('Topicmodel');
		
		//load entities
		$this->Topicmodel->GetTopicData($tid);
		$this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
		$this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());
		
		//see if user can post on this board.
		if (!$this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBReply())){
			show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
		} else {
			if (!$this->Groupmodel->ValidateAccess(1, 38)){
				show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
			}
		}

		//setup validation rules.
        $this->form_validation->set_rules('reply_post', $this->lang->line('topicbody'), 'required|min_length[10]|callback_SpamFilter|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="flash"><div class="message error">', '</div></div>');

		//see if any validation rules failed.
		if ($this->form_validation->run() == FALSE) {
			$this->FORM_reply($tid);
		} else {
			
			//flood check,
			if ($this->groupAccess == 3 && flood_check("posting", $this->Usermodel->getLastPost())) {
				#setup error session.
				$this->notifications('warning', $this->lang->line('flood'));

				#direct user.
				redirect('/viewtopic/'.$tid, 'location');
				exit();
			}
			
			//load topic & attachments model.
			$this->load->model(array('Topicmodel', 'Attachmentsmodel'));
			
			//CREATE NEW TOPIC.
			$no_smile = ($this->input->post('no_smile', TRUE) == FALSE) ? FALSE : TRUE;
			$no_bbcode = ($this->input->post('no_bbcode', TRUE) == FALSE) ? FALSE : TRUE;
			$subscribe = ($this->input->post('subscribe', TRUE) == FALSE) ? FALSE : TRUE;
			//$page = ($this->input->post('page', TRUE) == FALSE) ? null : $this->input->post('page', TRUE);
			$time = time();
			
			//create new topic.
			$this->Topicmodel->setAuthor($this->userID);
			$this->Topicmodel->setBid($this->Topicmodel->getBid());
			$this->Topicmodel->setTiD($tid);
			$this->Topicmodel->setTopic(null);
			$this->Topicmodel->setBody($this->input->post('reply_post', TRUE));
			$this->Topicmodel->setDisableBbCode($no_bbcode);
			$this->Topicmodel->setDisableSmiles($no_smile);
			$this->Topicmodel->setImportant(null);
			$this->Topicmodel->setIp(detectProxy());
			$this->Topicmodel->setLastUpdate($time);
			$this->Topicmodel->setOriginalDate($time);
			$this->Topicmodel->setLocked(0);
			$this->Topicmodel->setPostedUser($this->userID);
			$this->Topicmodel->setQuestion(null);
			$this->Topicmodel->setTopicType(null);
			
			$newPostId = $this->Topicmodel->CreateReply(); //create post, get Post ID.
			
			//see if user wants to subscribe to this topic.
			if ($subscribe) {
				subscriptionManager($this->userID, $tid, "subscribe");
			}

			#update board & topic details.
			update_board($this->Topicmodel->getBid(), $tid, $time, $this->userID);
			update_topic($tid, $time, $this->userID);
			
			//update user's last post.
			update_user($this->userID);
			
			#see if this board can allow post count increase.
			if($this->Boardmodel->getPostIncrement() == 1){
				//get current post count then add on to it.
				post_count($this->userID);
			}
			
			//validate user can attach files.
			if($this->Groupmodel->ValidateAccess(1, 26)){ 
				#see if user uploaded a file, if so lets assign the file to the topic.
				$this->db->select('id')
					->from('ebb_attachments')
					->where('Username', $this->userID)
					->where('tid', 0)
					->where('pid', 0);
				$query = $this->db->get();
				
				//see if we have anything to assign first.
				if($query->num_rows() > 0) {
					foreach ($query->result() as $row) {
						#add attachment to db for listing purpose.
						$this->Attachmentsmodel->AssignAttachment(0, $newPostId, $row->id);
					}
				}
			}
			
			//new topic notification.
			$this->db->select('u.Email, u.Language, u.Username')
			  ->from('ebb_topic_watch tw')
			  ->join('ebb_users u', 'tw.username=u.id', 'LEFT')
			  ->where('tw.username !=', $this->userID)
			  ->where('tw.tid', $tid)
			  ->where('tw.read_status', 0);
			$notificationQ = $this->db->get();
			
			//see if we have any subscribers.
			if($notificationQ->num_rows() > 0) {
				#email user.
				$config = array();
				if ($this->preference->getPreferenceValue("mail_type") == 2) {
					$config['protocol'] = 'sendmail';
					$config['mailpath'] = $this->preference->getPreferenceValue("sendmail_path");
					$this->email->initialize($config);
				} elseif ($this->preference->getPreferenceValue("mail_type") == 0) {
					$config['protocol'] = 'smtp';
					$config['smtp_host'] = $this->preference->getPreferenceValue("smtp_host");
					$config['smtp_user'] = $this->preference->getPreferenceValue("smtp_user");
					$config['smtp_pass'] = $this->preference->getPreferenceValue("smtp_pwd");
					$config['smtp_port'] = $this->preference->getPreferenceValue("smtp_port");
					$config['smtp_timeout'] = $this->preference->getPreferenceValue("smtp_timeout");
					$this->email->initialize($config);
				}

				//loop through data and bind to an array.
				foreach ($notificationQ->result() as $notify) {
					$this->email->clear(); //reset email setting
					
					//send out email.        	
					$this->email->to($notify->Email);
					$this->email->from($this->preference->getPreferenceValue("board_email"), $this->title);
					$this->email->subject('RE: '.$this->Topicmodel->getTopic());
					$this->email->message($this->twig->renderNoStyle('/emails/'.$notify->Language.'/eml_new_reply.twig', array(
						'USERNAME' => $notify->Username,
						'AUTHOR' => $this->logged_user,						  
						'TITLE' => $this->title,
						'BOARDADDR' => $this->boardUrl,
						'INDEX_PAGE' => $this->config->item('index_page'),
						'TOPIC_SUMMARY' => substr_replace(nl2br($this->input->post('reply_post', TRUE)),'[...]',100),
						'TID' => $tid
					)));

					//send out email.
					$this->email->send();
				}
			}
			
			//direct user to topic.
			redirect('/viewtopic/'.$tid, 'location');
		}
	}
	
	/**
	 * Reply Form.
	 * @param integer $tid TopicID.
	 * @param integer $quoteID Topic/Post ID.
	 * @param integer $quoteType topic=1;post=2.
	 */
	private function FORM_reply($tid, $quoteID=null, $quoteType=null) {
		//load breadcrumb library
		$this->load->library('breadcrumb');
		
		//load topic model.
		$this->load->model('Topicmodel');

		//load entities
		$this->Topicmodel->GetTopicData($tid);
		$this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
		$this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());
		
		//see if a user is quoting someone.
		if (!is_null($quoteID) || !is_null($quoteType)) {
			//see if its the original topic or not.
			if ($quoteType == 1) {
				$quoteMsg = '[quote='.$this->Topicmodel->getAuthor().']'.$this->Topicmodel->getBody().'[/quote]';
			} else {
				$this->db->select('u.Username, p.Body')
				->from('ebb_posts p')
				->join('ebb_users u', 'p.author=u.id', 'LEFT')
				->where('p.pid', $quoteID);
				$query = $this->db->get();
				
				//see if the post exists.
				if($query->num_rows() > 0) {
					$row = $query->row();
					$quoteMsg = '[quote='.$row->Username.']'.$row->Body.'[/quote]';
				} else {
					$quoteMsg = '';
				}
			}
		} else {
			$quoteMsg = '';
		}
		
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->Boardmodel->getBoard(), '/viewboard/'.$this->Topicmodel->getBid());
		$this->breadcrumb->append_crumb($this->lang->line("postreply"), '/reply');

		//grab board preferences.
		$boardpref_bbcode = $this->Boardmodel->getBbCode();
		$boardpref_smiles = $this->Boardmodel->getSmiles();
		$boardpref_image = $this->Boardmodel->getImage();
		
		#see if user can upload
		if ($this->Groupmodel->ValidateAccess(1, 26)) {
			$uploadLimit = $this->preference->getPreferenceValue("upload_limit");	
		} else {
			$uploadLimit = 0;
		}
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'reply', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("newreply"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG' => $this->lng,
		  'TimeFormat' => $this->dateTimeFormat,
		  'TimeZone' => $this->timeZone,
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
		  'LOGGEDUSERID' => $this->userID,
		  'LANG_JSDISABLED' => $this->lang->line('jsdisabled'),
		  'LANG_INFO' => $this->lang->line('info'),
		  'LANG_LOGIN' => $this->lang->line('login'),
		  'LANG_LOGOUT' => $this->lang->line('logout'),
		  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin')),
		  'REPLYFORM' => form_open('boards/reply/'.$tid, array('name' => 'frmReply', 'class' => 'form')),
		  'UPLOADFORM' => form_open_multipart('upload/do_upload/'),
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
		  'LANG_POSTEDBY' => $this->lang->line('Postedby'),
		  'BREADCRUMB' =>$this->breadcrumb->output(),
		  "LANG_POSTINGRULES" => $this->lang->line("postingrules"),
		  "LANG_ALLOWSMILES" => $this->lang->line("smiles"),
		  "ALLOWSMILES" => $boardpref_smiles,
		  "LANG_ALLOWBBCODE" => $this->lang->line("bbcode"),
		  "ALLOWBBCODE" => $boardpref_bbcode,
		  "LANG_ALLOWIMG" => $this->lang->line("img"),
		  "ALLOWIMG" => $boardpref_image,
		  "LANG_SMILES" => $this->lang->line("moresmiles"),
		  "SMILES" => form_smiles(),
		  "TID" => $tid,
		  "LANG_TOPIC" => $this->lang->line("topic"),
		  "LANG_TOPICBODY" => $this->lang->line('topicbody'),
		  "QUOTE_BODY" => $quoteMsg,
		  "LANG_UPLOAD" => $this->lang->line("uploadfile"),
		  "LANG_CLEAR" => $this->lang->line("clearfile"),
		  "LANG_ADDMOREFILES" => $this->lang->line("addMoreFiles"),
		  "ATTACHMENTLIMIT" => $uploadLimit,
		  "LANG_DISABLERTF" => $this->lang->line("disablertf"),
		  "LANG_OPTIONS" => $this->lang->line("options"),
		  "LANG_NOTIFY" => $this->lang->line("notify"),
		  "LANG_DISABLESMILES" => $this->lang->line("disablesmiles"),
		  "LANG_DISABLEBBCODE" => $this->lang->line("disablebbcode"),
		  "LANG_TOPICREVIEW" => $this->lang->line('topicreview'),
		  "LANG_RELOAD" => $this->lang->line('reload'),
		  "LANG_REPLY" => $this->lang->line("btnreply")
		));
	}
	
	/**
	 * vote on a poll.
	 * @example index.php/boards/vote/5
	*/
	public function vote($id) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		$canVote = TRUE; //see if user can vote.
		
		//LOAD LIBRARIES
		$this->load->library(array('encrypt'));
        $this->load->helper(array('form'));
		
		//load topic model.
		$this->load->model('Topicmodel');
		
		//load entities
		$this->Topicmodel->GetTopicData($id);
		$this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
		$this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());
		
		//see if user can vote.
		if (!$this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBVote())){
			$this->notifications('error', $this->lang->line('cantvote'));
			$canVote = FALSE;
		} else {
			if (!$this->Groupmodel->ValidateAccess(1, 36)){
				$this->notifications('error', $this->lang->line('cantvote'));
				$canVote = FALSE;
			}
		}
		
		//process vote only if allowed to.
		if ($canVote) {
			//get vote from form.
			$vote = $this->input->post('vote', TRUE);

			//ensure the user entered something.
			if (!$vote) {
				//something went wrong.
				$this->notifications('error', $this->lang->line('novote'));
			} else {
				//load topic model.
				$this->load->model('Topicmodel');

				//cast vote.
				$this->Topicmodel->CastVote($this->userID, $id, $vote);

				//vote has been recorded.
				$this->notifications('success', $this->lang->line('votecasted'));
			}
		}
		
		//direct user to topic.
		redirect('/viewtopic/'.$id, 'location');
	}
	
	/**
	 * report a topic/reply.
	 * @example index.php/boards/reporttopic/5
	*/
	public function reporttopic($id) {
		
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//alert user.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email', 'form_validation', 'breadcrumb'));
        $this->load->helper(array('form', 'user'));
		
		//load topic model.
		$this->load->model('Topicmodel');

		//load topic entity.
		$tData = $this->Topicmodel->GetTopicData($id);
		
		if ($tData) {
			// add breadcrumbs
			$this->breadcrumb->append_crumb($this->title, '/boards/');
			$this->breadcrumb->append_crumb($this->Topicmodel->getTopic(), '/viewtopic/'.$this->Topicmodel->getTiD());
			$this->breadcrumb->append_crumb($this->lang->line("reporttomod"), '/reporttopic/'.$id);

			//setup validation rules.
			$this->form_validation->set_rules('reason', $this->lang->line('reason'), 'required|xss_clean');
			$this->form_validation->set_rules('msg', $this->lang->line('message'), 'required|min_length[10]|max_length[255]|xss_clean');
			$this->form_validation->set_error_delimiters('<div class="flash"><div class="message error">', '</div></div>');

			//see if any validation rules failed.
			if ($this->form_validation->run() == FALSE) {
				//render to HTML.
				echo $this->twig->render(strtolower(__CLASS__), 'reporttopic', array (
				'boardName' => $this->title,
				'pageTitle'=> $this->lang->line("reporttomod"),
				'THEME_NAME' => $this->getStyleName(),
				'STYLELIST' => $this->styleList,
				'INDEX_PAGE' => $this->config->item('index_page'),
				'BOARD_URL' => $this->boardUrl,
				'APP_URL' => $this->boardUrl.APPPATH,
				'NOTIFY_TYPE' => $this->notifyType,
				'NOTIFY_MSG' =>  $this->notifyMsg,
				'LANG_ERROR' => $this->lang->line('error'),
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
				'LOGGEDUSERID' => $this->userID,
				'LANG_JSDISABLED' => $this->lang->line('jsdisabled'),
				'LANG_INFO' => $this->lang->line('info'),
				'LANG_LOGIN' => $this->lang->line('login'),
				'LANG_LOGOUT' => $this->lang->line('logout'),
				'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin')),
				'REPORTTOPICFORM' => form_open('boards/reporttopic/'.$id, array('name' => 'frmReportTopics', 'class' => 'form')),
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
				'LANG_POSTEDBY' => $this->lang->line('Postedby'),
				'BREADCRUMB' =>$this->breadcrumb->output(),
				'LANG_TEXT' => $this->lang->line('topicreporttxt'),
				'LANG_REPORTEDBY' => $this->lang->line('Reportedby'),
				'LANG_REASON' => $this->lang->line('reason'),
				'LANG_SPAMPOST' => $this->lang->line('spampost'),
				'LANG_FIGHTPOST' => $this->lang->line('fightpost'),
				'LANG_ADVERT' => $this->lang->line('advert'),
				'LANG_USERPROBLEMS' => $this->lang->line('userproblems'),
				'LANG_OTHER' => $this->lang->line('other'),
				'LANG_MESSAGE' => $this->lang->line('message'),
				'LANG_SUBMITREPORT' => $this->lang->line('submitreport')
				));
			} else {
				//new topic notification.
				$this->db->select('u.Email, u.Language')
				->from('ebb_users u')
				->join('ebb_groups g', 'u.gid=g.id', 'LEFT')
				->where('g.Level', 1)
				->or_where('g.Level', 2);
				$notificationQ = $this->db->get();

				//see if we have any subscribers.
				if($notificationQ->num_rows() > 0) {
					#email user.
					$config = array();
					if ($this->preference->getPreferenceValue("mail_type") == 2) {
						$config['protocol'] = 'sendmail';
						$config['mailpath'] = $this->preference->getPreferenceValue("sendmail_path");
						$this->email->initialize($config);
					} elseif ($this->preference->getPreferenceValue("mail_type") == 0) {
						$config['protocol'] = 'smtp';
						$config['smtp_host'] = $this->preference->getPreferenceValue("smtp_host");
						$config['smtp_user'] = $this->preference->getPreferenceValue("smtp_user");
						$config['smtp_pass'] = $this->preference->getPreferenceValue("smtp_pwd");
						$config['smtp_port'] = $this->preference->getPreferenceValue("smtp_port");
						$config['smtp_timeout'] = $this->preference->getPreferenceValue("smtp_timeout");
						$this->email->initialize($config);
					}

					//loop through data and bind to an array.
					foreach ($notificationQ->result() as $notify) {
						$this->email->clear(); //reset email setting

						//send out email.        	
						$this->email->to($notify->Email);
						$this->email->from($this->preference->getPreferenceValue("board_email"), $this->title);
						$this->email->subject($this->lang->line('reportsubject'));
						$this->email->message($this->twig->renderNoStyle('/emails/'.$notify->Language.'/eml_report_topic.twig', array(
						'REPORTED_BY' => $this->logged_user,
						'REASON' => $this->input->post('reason', TRUE),
						'MSG' => $this->input->post('msg', TRUE),
						'BOARDADDR' => $this->boardUrl,
						'TID' => $id
						)));

						//send out email.
						$this->email->send();
					}
				}

				//show success message.
				$this->notifications('success', $this->lang->line('reportsent'));

				//direct user to topic.
				redirect('/viewtopic/'.$id, 'location');

			}
		} else {
			show_error($this->lang->line('doesntexist'), 403, $this->lang->line('error'));
		}
		
	}
	
	/**
	 * edit a topic.
	 * @example index.php/boards/edittopic/5
	*/
	public function edittopic($id) {

		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation', 'breadcrumb'));
        $this->load->helper(array('form', 'user', 'posting'));
		
		//load topic & attachments model.
		$this->load->model(array('Topicmodel', 'Attachmentsmodel'));
		
		//load entities
		$this->Topicmodel->GetTopicData($id);
		$this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
		$this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());
		
		//see if user can post on this board.
		if (!$this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBPost())){
			show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
		} else {
			if (!$this->Groupmodel->ValidateAccess(1, 37)){
				show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
			}
		}
		
		#see if user can mark topics as important.
		if ($this->Groupmodel->ValidateAccess(1, 39)){
			$CanImportant = TRUE;
		} else {
			$CanImportant = FALSE;
		}
		
		//setup validation rules.
        $this->form_validation->set_rules('topic', $this->lang->line('topic'), 'required|min_length[5]|max_length[50]|callback_SpamFilter|xss_clean');
        $this->form_validation->set_rules('post', $this->lang->line('topicbody'), 'required|min_length[10]|callback_SpamFilter|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="flash"><div class="message error">', '</div></div>');

		//see if any validation rules failed.
		if ($this->form_validation->run() == FALSE) {
			// add breadcrumbs
			$this->breadcrumb->append_crumb($this->title, '/');
			$this->breadcrumb->append_crumb($this->Boardmodel->getBoard(), '/viewboard/'.$this->Topicmodel->getBid());
			$this->breadcrumb->append_crumb($this->Topicmodel->getTopic(), '/viewtopic/'.$id);
			$this->breadcrumb->append_crumb($this->lang->line("edittopic"), '/edittopic/'.$id);

			//grab board preferences.
			$boardpref_bbcode = $this->Boardmodel->getBbCode();
			$boardpref_smiles = $this->Boardmodel->getSmiles();
			$boardpref_image = $this->Boardmodel->getImage();

			#see if user can mark topics as important.
			if ($this->Groupmodel->ValidateAccess(1, 39)){
				$CanImportant = TRUE;
			} else {
				$CanImportant = FALSE;
			}

			#see if user can upload
			if ($this->Groupmodel->ValidateAccess(1, 26)) {
				$uploadLimit = $this->preference->getPreferenceValue("upload_limit");	
			} else {
				$uploadLimit = 0;
			}

			//render to HTML.
			echo $this->twig->render(strtolower(__CLASS__), 'edit_topic', array (
			  'boardName' => $this->title,
			  'pageTitle'=> $this->lang->line("edittopic"),
			  'THEME_NAME' => $this->getStyleName(),
			  'STYLELIST' => $this->styleList,
			  'INDEX_PAGE' => $this->config->item('index_page'),
			  'BOARD_URL' => $this->boardUrl,
			  'APP_URL' => $this->boardUrl.APPPATH,
			  'NOTIFY_TYPE' => $this->notifyType,
			  'NOTIFY_MSG' =>  $this->notifyMsg,
			  'LANG_ERROR' => $this->lang->line('error'),
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
			  'LOGGEDUSERID' => $this->userID,
			  'LANG_JSDISABLED' => $this->lang->line('jsdisabled'),
			  'LANG_INFO' => $this->lang->line('info'),
			  'LANG_LOGIN' => $this->lang->line('login'),
			  'LANG_LOGOUT' => $this->lang->line('logout'),
			  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin')),
			  'EDITTOPICFORM' => form_open('boards/edittopic/'.$id, array('name' => 'frmEditTopic', 'class' => 'form')),
			  'UPLOADFORM' => form_open_multipart('upload/do_upload/'),
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
			  'LANG_POSTEDBY' => $this->lang->line('Postedby'),
			  'BREADCRUMB' =>$this->breadcrumb->output(),
			  "LANG_POSTINGRULES" => $this->lang->line("postingrules"),
			  "LANG_ALLOWSMILES" => $this->lang->line("smiles"),
			  "ALLOWSMILES" => $boardpref_smiles,
			  "LANG_ALLOWBBCODE" => $this->lang->line("bbcode"),
			  "ALLOWBBCODE" => $boardpref_bbcode,
			  "LANG_ALLOWIMG" => $this->lang->line("img"),
			  "ALLOWIMG" => $boardpref_image,
			  "LANG_SMILES" => $this->lang->line("moresmiles"),
			  "SMILES" => form_smiles(),
			  "LANG_TOPIC" => $this->lang->line("topic"),
			  'TOPIC' => $this->Topicmodel->getTopic(),
			  "LANG_TOPICBODY" => $this->lang->line('topicbody'),
			  'POSTTOPIC' => $this->Topicmodel->getBody(),
			  "LANG_UPLOAD" => $this->lang->line("uploadfile"),
			  "LANG_CLEAR" => $this->lang->line("clearfile"),
			  "LANG_VIEWFILES" => $this->lang->line("viewfiles"),
			  "ATTACHMENTLIMIT" => $uploadLimit,
			  "LANG_DISABLERTF" => $this->lang->line("disablertf"),
			  "LANG_OPTIONS" => $this->lang->line("options"),
			  "LANG_POSTTYPE" => $this->lang->line("type"),
			  "POSTTYPE" => $this->Topicmodel->getImportant(),
			  "GAC_IMPORTANT" => $CanImportant,
			  "LANG_IMPORTANT" => $this->lang->line("important"),
			  "LANG_NORMAL" => $this->lang->line("normal"),
			  "LANG_NOTIFY" => $this->lang->line("notify"),
			  "LANG_DISABLESMILES" => $this->lang->line("disablesmiles"),
			  "DISABLESMILES" => $this->Topicmodel->getDisableSmiles(),
			  "LANG_DISABLEBBCODE" => $this->lang->line("disablebbcode"),
			  "DISABLEBBCODE" => $this->Topicmodel->getDisableBbCode(),
			  "LANG_EDITTOPIC" => $this->lang->line("edittopic")
			));
		} else {
			$no_smile = ($this->input->post('no_smile', TRUE) == FALSE) ? FALSE : TRUE;
			$no_bbcode = ($this->input->post('no_bbcode', TRUE) == FALSE) ? FALSE : TRUE;
			$subscribe = ($this->input->post('subscribe', TRUE) == FALSE) ? FALSE : TRUE;
			
			//create new topic.
			$this->Topicmodel->setTopic($this->input->post('topic', TRUE));
			$this->Topicmodel->setBody($this->input->post('post', TRUE));
			$this->Topicmodel->setDisableBbCode($no_bbcode);
			$this->Topicmodel->setDisableSmiles($no_smile);
			$this->Topicmodel->setImportant($this->input->post('post_type', TRUE));
			
			$this->Topicmodel->ModifyTopic(); //update topic.
			
			//see if user wants to subscribe to this topic.
			if ($subscribe) {
				subscriptionManager($this->userID, $id, "subscribe");
			}
		
			//validate user can attach files.
			if($this->Groupmodel->ValidateAccess(1, 26)){ 
				#see if user uploaded a file, if so lets assign the file to the topic.
				$this->db->select('id')
					->from('ebb_attachments')
					->where('Username', $this->userID)
					->where('tid', 0)
					->where('pid', 0);
				$query = $this->db->get();
				
				//see if we have anything to assign first.
				if($query->num_rows() > 0) {
					foreach ($query->result() as $row) {
						#add attachment to db for listing purpose.
						$this->Attachmentsmodel->AssignAttachment($id, 0, $row->id);
					}
				}
			}
			
			//direct user to topic.
			redirect('/viewtopic/'.$id, 'location');
		}
		
	}
	
	/**
	 * deletes topic and all things assocated with it.
	 * @example index.php/boards/deletetopic/5
	*/
	public function deletetopic($id) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//load topic model.
		$this->load->model(array('Topicmodel'));
		
		//load entities
		$tData = $this->Topicmodel->GetTopicData($id);
		$boardOpt = $this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
		$boardAcc = $this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());
		
		//ensure everything loaded correctly.
		if ($tData && $boardOpt && $boardAcc) {
			
			//see if admin or moderator, they got different access control to validate from.
			if ($this->groupAccess == 1 || $this->groupAccess == 2) {
				if($this->Groupmodel->ValidateAccess(1, 21)){
					$CanDelete = TRUE;
				} else {
					$CanDelete = FALSE;
				}
			} else {
				//see if user can delete this topic.
				if($this->Groupmodel->ValidateAccess(1, 41)){
					$CanDelete = TRUE;
				} else {
					$CanDelete = FALSE;
				}
			}
			
			//if user can delete, delete all data, if not, let them know this.
			if ($CanDelete) {
				$this->Topicmodel->DeleteTopic();
				$this->Topicmodel->DeleteReply(TRUE);
				$this->Topicmodel->DeletePoll();
				
				//display success message.
				$this->notifications('success', $this->lang->line('deletetopicsuccess'));
				
				#direct user to login page.
				redirect('/viewboard/'.$this->Topicmodel->getBid(), 'location');
			} else {
				show_error($this->lang->line('accessdenied'),403,$this->lang->line('error'));
			}

		} else {
			exit(show_error($this->lang->line('doesntexist'), 500, $this->lang->line('error')));
		}
	}
	
	/**
	 * edit a reply.
	 * @example index.php/boards/editpost/5
	*/
	public function editpost($id) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation', 'breadcrumb'));
        $this->load->helper(array('form', 'user', 'posting'));
		
		//load topic & attachments model.
		$this->load->model(array('Topicmodel', 'Attachmentsmodel'));
		
		//load entities
		$this->Topicmodel->GetReplyData($id);
		$this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
		$this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());
		
		//see if user can post on this board.
		if (!$this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBReply())){
			show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
		} else {
			if (!$this->Groupmodel->ValidateAccess(1, 38)){
				show_error($this->lang->line('nowrite'),403,$this->lang->line('error'));
			}
		}

		//setup validation rules.
        $this->form_validation->set_rules('reply_post', $this->lang->line('topicbody'), 'required|min_length[10]|callback_SpamFilter|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="flash"><div class="message error">', '</div></div>');

		//see if any validation rules failed.
		if ($this->form_validation->run() == FALSE) {
			// add breadcrumbs
			$this->breadcrumb->append_crumb($this->title, '/');
			$this->breadcrumb->append_crumb($this->Boardmodel->getBoard(), '/viewboard/'.$this->Topicmodel->getBid());
			$this->breadcrumb->append_crumb($this->Topicmodel->getTopic(), '/viewtopic/'.$this->Topicmodel->getTiD());
			$this->breadcrumb->append_crumb($this->lang->line("editpost"), '/editpost/'.$id);

			//grab board preferences.
			$boardpref_bbcode = $this->Boardmodel->getBbCode();
			$boardpref_smiles = $this->Boardmodel->getSmiles();
			$boardpref_image = $this->Boardmodel->getImage();

			#see if user can upload
			if ($this->Groupmodel->ValidateAccess(1, 26)) {
				$uploadLimit = $this->preference->getPreferenceValue("upload_limit");	
			} else {
				$uploadLimit = 0;
			}

			//render to HTML.
			echo $this->twig->render(strtolower(__CLASS__), 'edit_post', array (
			  'boardName' => $this->title,
			  'pageTitle'=> $this->lang->line("editpost"),
			  'THEME_NAME' => $this->getStyleName(),
			  'STYLELIST' => $this->styleList,
			  'INDEX_PAGE' => $this->config->item('index_page'),
			  'BOARD_URL' => $this->boardUrl,
			  'APP_URL' => $this->boardUrl.APPPATH,
			  'NOTIFY_TYPE' => $this->notifyType,
			  'NOTIFY_MSG' =>  $this->notifyMsg,
			  'LANG_ERROR' => $this->lang->line('error'),
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
			  'LOGGEDUSERID' => $this->userID,
			  'LANG_JSDISABLED' => $this->lang->line('jsdisabled'),
			  'LANG_INFO' => $this->lang->line('info'),
			  'LANG_LOGIN' => $this->lang->line('login'),
			  'LANG_LOGOUT' => $this->lang->line('logout'),
			  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin')),
			  'EDITPOSTFORM' => form_open('boards/editpost/'.$id, array('name' => 'frmEditPost', 'class' => 'form')),
			  'UPLOADFORM' => form_open_multipart('upload/do_upload/'),
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
			  'LANG_POSTEDBY' => $this->lang->line('Postedby'),
			  'BREADCRUMB' =>$this->breadcrumb->output(),
			  "LANG_POSTINGRULES" => $this->lang->line("postingrules"),
			  "LANG_ALLOWSMILES" => $this->lang->line("smiles"),
			  "ALLOWSMILES" => $boardpref_smiles,
			  "LANG_ALLOWBBCODE" => $this->lang->line("bbcode"),
			  "ALLOWBBCODE" => $boardpref_bbcode,
			  "LANG_ALLOWIMG" => $this->lang->line("img"),
			  "ALLOWIMG" => $boardpref_image,
			  "LANG_SMILES" => $this->lang->line("moresmiles"),
			  "SMILES" => form_smiles(),
			  "LANG_TOPICBODY" => $this->lang->line('topicbody'),
			  'POSTBODY' => $this->Topicmodel->getBody(),
			  "LANG_UPLOAD" => $this->lang->line("uploadfile"),
			  "LANG_CLEAR" => $this->lang->line("clearfile"),
			  "LANG_VIEWFILES" => $this->lang->line("viewfiles"),
			  "ATTACHMENTLIMIT" => $uploadLimit,
			  "LANG_DISABLERTF" => $this->lang->line("disablertf"),
			  "LANG_OPTIONS" => $this->lang->line("options"),
			  "LANG_NOTIFY" => $this->lang->line("notify"),
			  "LANG_DISABLESMILES" => $this->lang->line("disablesmiles"),
			  "DISABLESMILES" => $this->Topicmodel->getDisableSmiles(),
			  "LANG_DISABLEBBCODE" => $this->lang->line("disablebbcode"),
			  "DISABLEBBCODE" => $this->Topicmodel->getDisableBbCode(),
			  "LANG_EDITPOST" => $this->lang->line("editpost")
			));
		} else {
			//CREATE NEW TOPIC.
			$no_smile = ($this->input->post('no_smile', TRUE) == FALSE) ? FALSE : TRUE;
			$no_bbcode = ($this->input->post('no_bbcode', TRUE) == FALSE) ? FALSE : TRUE;
			$subscribe = ($this->input->post('subscribe', TRUE) == FALSE) ? FALSE : TRUE;
			
			//update topic.
			$this->Topicmodel->setBody($this->input->post('reply_post', TRUE));
			$this->Topicmodel->setDisableBbCode($no_bbcode);
			$this->Topicmodel->setDisableSmiles($no_smile);
			
			$this->Topicmodel->ModifyReply(); //update post.
			
			//see if user wants to subscribe to this topic.
			if ($subscribe) {
				subscriptionManager($this->userID, $this->Topicmodel->getTiD(), "subscribe");
			}
			
			//validate user can attach files.
			if($this->Groupmodel->ValidateAccess(1, 26)){ 
				#see if user uploaded a file, if so lets assign the file to the topic.
				$this->db->select('id')
					->from('ebb_attachments')
					->where('Username', $this->userID)
					->where('tid', 0)
					->where('pid', 0);
				$query = $this->db->get();
				
				//see if we have anything to assign first.
				if($query->num_rows() > 0) {
					foreach ($query->result() as $row) {
						#add attachment to db for listing purpose.
						$this->Attachmentsmodel->AssignAttachment(0, $id, $row->id);
					}
				}
			}
			
			//direct user to topic.
			redirect('/viewtopic/'.$this->Topicmodel->getTiD(), 'location');
			
		}
	}
	
	/**
	 * delete a reply and everything assoicated with it.
	 * @example index.php/boards/deletepost/5
	*/
	public function deletepost($id) {
		#if Group Access property is 0, redirect user.
		if ($this->groupAccess == 0) {
			//show success message.
			$this->notifications('warning', $this->lang->line('notloggedin'));

			#direct user to login page.
			redirect('/login', 'location');
		}
		
		//load topic model.
		$this->load->model(array('Topicmodel'));
		
		//load entities
		$pData = $this->Topicmodel->GetReplyData($id);
		$boardOpt = $this->Boardmodel->GetBoardSettings($this->Topicmodel->getBid());
		$boardAcc = $this->Boardaccessmodel->GetBoardAccess($this->Topicmodel->getBid());
		
		//ensure everything loaded correctly.
		if ($pData && $boardOpt && $boardAcc) {
			
			//see if admin or moderator, they got different access control to validate from.
			if ($this->groupAccess == 1 || $this->groupAccess == 2) {
				if($this->Groupmodel->ValidateAccess(1, 21)){
					$CanDelete = TRUE;
				} else {
					$CanDelete = FALSE;
				}
			} else {
				//see if user can delete this topic.
				if($this->Groupmodel->ValidateAccess(1, 41)){
					$CanDelete = TRUE;
				} else {
					$CanDelete = FALSE;
				}
			}
			
			//if user can delete, delete all data, if not, let them know this.
			if ($CanDelete) {
				$this->Topicmodel->DeleteReply();
				
				//display success message.
				$this->notifications('success', $this->lang->line('deletereplysuccess'));
				
				#direct user to login page.
				redirect('/viewtopic/'.$this->Topicmodel->getTiD(), 'location');
			} else {
				show_error($this->lang->line('accessdenied'),403,$this->lang->line('error'));
			}

		} else {
			exit(show_error($this->lang->line('doesntexist'), 500, $this->lang->line('error')));
		}
	}
	
	/**
	 * Get RSS Feed for the selected Board.
	 * @example index.php/boards/boardFeed/5
	*/
	public function boardFeed($id) {
		
		//load libraries/helpers/etc
		$this->load->helper('text');
		
		if (!$this->Boardmodel->GetBoardSettings($id)) {
			show_error($this->lang->line('doesntexist'),404,$this->lang->line('error'));
		}elseif ($this->Boardmodel->getType() == 1) {
			redirect('/', 'location');
		}

		$this->Boardaccessmodel->GetBoardAccess($id);
		
		//fetch topic data.
		$this->db->select('t.tid, t.bid, p.pid, t.Topic, t.Body AS TBODY, p.Body AS PBODY, t.Original_Date AS TDATE, p.Original_Date AS PDATE')
		  ->from('ebb_topics t')
		  ->join('ebb_posts p', 't.tid=p.tid', 'LEFT')
		  ->where('t.bid', $id)
		  ->order_by('TDATE desc, PDATE desc')
		  ->limit(20);
		$query = $this->db->get();
		
		#set headers to make it an xml file.
		header("Content-type: text/xml");
		
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
		echo '<channel>
		<title>'.$this->title.' - '.$this->Boardmodel->getBoard().'</title>
		<description>'.$this->Boardmodel->getDescription().'</description>
		<link>'.$this->boardUrl.'</link>';
		
		foreach ($query->result() as $row) {
			
			#see if user can view this topic.
			if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBRead())){
								
				//see if pid is blank(would be for topcis only)
				if ($row->pid <> "") {
					#if body is over 100 characters, cut it off.
					if(strlen($row->PBODY) > 100){
						$rss_desc = character_limiter($row->PBODY, 100);
					}else{
						$rss_desc = $row->PBODY;
					}

					//setup date
					$gmttime = gmdate ("r", $row->PDATE);				
					
					echo '<item>
					<link>'.$this->boardUrl.$this->config->item('index_page').'/viewtopic/'.$row->tid.'</link>
					<date>'. $gmttime .'</date>
					<title>'.$row->Topic.'</title>
					<description>'.$rss_desc.'</description>
					<pubDate>'. $gmttime .'</pubDate>
					</item>';
				} else {
					#if body is over 100 characters, cut it off.
					if(strlen($row->TBODY) > 100){
						$rss_desc = character_limiter($row->TBODY, 100);
					}else{
						$rss_desc = $row->TBODY;
					}

					//setup date
					$gmttime = gmdate ("r", $row->TDATE);	

					echo '<item>
					<link>'.$this->boardUrl.$this->config->item('index_page').'/viewtopic/'.$row->tid.'</link>
					<date>'. $gmttime .'</date>
					<title>'.$row->Topic.'</title>
					<description>'.$rss_desc.'</description>
					<pubDate>'. $gmttime .'</pubDate>
					</item>';
				}
			}
		}
		echo '</channel></rss>';
	}
	
	/**
	 * Get RSS Feed for the latest posts on the board.
	 * @example index.php/boards/latestpost/5
	*/
	public function latestPost() {

		//load libraries/helpers/etc
		$this->load->helper('text');
		
		//fetch topic data.
		$this->db->select('t.tid, t.bid, p.pid, t.Topic, t.Body AS TBODY, p.Body AS PBODY, t.Original_Date AS TDATE, p.Original_Date AS PDATE')
		  ->from('ebb_topics t')
		  ->join('ebb_posts p', 't.tid=p.tid', 'LEFT')
		  ->order_by('TDATE desc, PDATE desc')
		  ->limit(20);
		$query = $this->db->get();
		
		#set headers to make it an xml file.
		header("Content-type: text/xml");
		
		echo '<?xml version="1.0" encoding="UTF-8" ?>';
		echo '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
		echo '<channel>
		<title>'.$this->title.'</title>
		<description>'.$this->lang->line('latestposts').'</description>
		<link>'.$this->boardUrl.'</link>';
		
		foreach ($query->result() as $row) {
			
			$this->Boardmodel->GetBoardSettings($row->bid);
			$this->Boardaccessmodel->GetBoardAccess($row->bid);
			
			#see if user can view this topic.
			if ($this->Groupmodel->validateAccess(0, $this->Boardaccessmodel->getBRead())){
								
				//see if pid is blank(would be for topcis only)
				if ($row->pid <> "") {
					#if body is over 100 characters, cut it off.
					if(strlen($row->PBODY) > 100){
						$rss_desc = character_limiter($row->PBODY, 100);
					}else{
						$rss_desc = $row->PBODY;
					}

					//setup date
					$gmttime = gmdate ("r", $row->PDATE);				
					
					echo '<item>
					<link>'.$this->boardUrl.$this->config->item('index_page').'/viewtopic/'.$row->tid.'</link>
					<date>'. $gmttime .'</date>
					<title>'.$row->Topic.'</title>
					<description>'.$rss_desc.'</description>
					<pubDate>'. $gmttime .'</pubDate>
					</item>';
				} else {
					#if body is over 100 characters, cut it off.
					if(strlen($row->TBODY) > 100){
						$rss_desc = character_limiter($row->TBODY, 100);
					}else{
						$rss_desc = $row->TBODY;
					}

					//setup date
					$gmttime = gmdate ("r", $row->TDATE);	

					echo '<item>
					<link>'.$this->boardUrl.$this->config->item('index_page').'/viewtopic/'.$row->tid.'</link>
					<date>'. $gmttime .'</date>
					<title>'.$row->Topic.'</title>
					<description>'.$rss_desc.'</description>
					<pubDate>'. $gmttime .'</pubDate>
					</item>';
				}
			}
		}
		echo '</channel></rss>';
	}	
}
/* Location: ./application/controllers/boards.php */