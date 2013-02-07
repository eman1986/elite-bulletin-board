<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * search.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 12/24/2012
*/

class Search extends EBB_Controller {

	function __construct() {
 		parent::__construct();
		
		//see if user is logged in.
		if ($this->groupAccess == 0) {
			//see if user is calling directly or by AJAX.
			if (IS_AJAX) {
				exit($this->lang->line('guesterror'));
			} else {
				//show success message.
				$this->notifications('warning', $this->lang->line('notloggedin'));

				#direct user to login page.
				redirect('/login', 'location');
			}
		}
		
		//load breadcrumb library
		$this->load->library('breadcrumb');
	}
	
	/**
	 * Main search action.
	 * @example index.php/search/
	*/
	public function index() {
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'pagination', 'form_validation'));
		$this->load->model(array('Topicmodel', 'Boardaccessmodel'));
        $this->load->helper(array('boardindex','form', 'user', 'posting', 'form_select'));
		
		//setup validation rules.
        $this->form_validation->set_rules('keyword', $this->lang->line('keyword'), 'required|min_length[5]|xss_clean');
		$this->form_validation->set_error_delimiters('<div class="flash"><div class="message error">', '</div></div>');
		
		//see if any validation rules failed.
		if ($this->form_validation->run() == FALSE) {
			// add breadcrumbs
			$this->breadcrumb->append_crumb($this->title, '/');
			$this->breadcrumb->append_crumb($this->lang->line("search"), '/search/');
			
			//render to HTML.
			echo $this->twig->render(strtolower(__CLASS__), 'index', array (
			  'boardName' => $this->title,
			  'pageTitle'=> $this->lang->line("search"),
			  'THEME_NAME' => $this->getStyleName(),
			  'STYLELIST' => $this->styleList,
			  'INDEX_PAGE' => $this->config->item('index_page'),
			  'BOARD_URL' => $this->boardUrl,
			  'APP_URL' => $this->boardUrl.APPPATH,
			  'NOTIFY_TYPE' => $this->notifyType,
			  'NOTIFY_MSG' =>  $this->notifyMsg,
			  'LANG_ERROR' => $this->lang->line('error'),
			  'LANG' => $this->lng,
			  'TimeFormat' => $this->timeFormat,
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
			  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin', 'id' => 'loginForm')),
			  'SEARCHFORM' => form_open('search/', array('name' => 'frmSearch', 'class' => 'form')),
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
			  "LANG_KEYWORD" => $this->lang->line('keyword'),
			  "LANG_SELBOARD" => $this->lang->line('selboard'),
			  "LANG_SEARCHTYPE" => $this->lang->line('selectsearchtype'),
			  "LANG_TOPIC" => $this->lang->line('topics'),
			  "LANG_POST" => $this->lang->line('posts'),
			  "BOARDLIST" => boardListSelect()
			));
		} else {
			$boardID = ($this->input->post('boardIDs', TRUE != FALSE) ? $this->input->post('boardIDs', TRUE) : NULL);
			$author = ($this->input->post('author', TRUE != FALSE) ? $this->input->post('author', TRUE) : NULL);
			
			if ($this->input->post('search_type', TRUE)  == "post") {
				$searchDataCount = $this->Topicmodel->SearchPostCount($this->input->post('keyword', TRUE), $boardID);
			} else {
				$searchDataCount = $this->Topicmodel->SearchTopicCount($this->input->post('keyword', TRUE), $boardID);
			}
			
			/**
			 * Setup Pagination.
			*/
			$config = array();
			$config['base_url'] = $this->boardUrl.$this->config->item('index_page').'/search/';
			$config['total_rows'] = $searchDataCount;
			$config['per_page'] = $this->preference->getPreferenceValue("per_page");
			$config['uri_segment'] = 4;
			$config['full_tag_open'] = '<div class="pagination">';
			$config['full_tag_close'] = '</div>';
			$config['next_tag_open'] = '<span class="nextpage">';
			$config['next_tag_close'] = '</span>';
			$config['prev_tag_open'] = '<span class="prevpage">';
			$config['prev_tag_close'] = '</span>';
			$config['cur_tag_open'] = '<span class="currentpage">';
			$config['cur_tag_close'] = '</span>';
			$config['next_link'] = '&raquo;';
			$config['prev_link'] = '&laquo;';
			$config['first_link'] = $this->lang->line('pagination_first');
			$config['last_link'] = $this->lang->line('pagination_last');
			$this->pagination->initialize($config);

			// add breadcrumbs
			$this->breadcrumb->append_crumb($this->title, '/');
			$this->breadcrumb->append_crumb($this->lang->line("search"), '/search/');
			$this->breadcrumb->append_crumb($this->lang->line("searchresults"), '/search/');

			if ($this->input->post('search_type', TRUE)  == "post") {
				$searchData = $this->Topicmodel->SearchPost($this->input->post('keyword', TRUE), $config['per_page'], $this->uri->segment(4), $author, $boardID);				
			} else {
				$searchData = $this->Topicmodel->SearchTopic($this->input->post('keyword', TRUE), $config['per_page'], $this->uri->segment(4), $author, $boardID);
			}
			
			#setup filters.
			$this->twig->_twig_env->addFilter('counter', new Twig_Filter_Function('GetCount'));
			$this->twig->_twig_env->addFilter('TopicReadStat', new Twig_Filter_Function('readTopicStat'));
			$this->twig->_twig_env->addFunction('Attachment', new Twig_Function_Function('HasAttachment'));
			$this->twig->_twig_env->addFunction('SubBoardCount', new Twig_Function_Function('GetSubBoardCount'));
			$this->twig->_twig_env->addFilter('ReadStat', new Twig_Filter_Function('CheckReadStatus'));
			
			//render to HTML.
			echo $this->twig->render(strtolower(__CLASS__), 'results', array (
			  'boardName' => $this->title,
			  'pageTitle'=> $this->lang->line("searchresults"),
			  'THEME_NAME' => $this->getStyleName(),
			  'STYLELIST' => $this->styleList,
			  'INDEX_PAGE' => $this->config->item('index_page'),
			  'BOARD_URL' => $this->boardUrl,
			  'APP_URL' => $this->boardUrl.APPPATH,
			  'NOTIFY_TYPE' => $this->notifyType,
			  'NOTIFY_MSG' =>  $this->notifyMsg,
			  'LANG' => $this->lng,
			  'TimeFormat' => $this->timeFormat,
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
			  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin', 'id' => 'loginForm')),
			  'SEARCHFORM' => form_open('search/', array('name' => 'frmSearch', 'class' => 'form')),
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
			  'LANG_POSTEDIN' => $this->lang->line('postedin'),
			  'BREADCRUMB' =>$this->breadcrumb->output(),
			  'LANG_TOPIC' => $this->lang->line('topics'),
			  'LANG_RESULTS' => $this->lang->line('result'),
			  "SEARCH_RESULTS" => $searchData,
			  "SEARCH_RESULTS_COUNT" => $searchDataCount,
			  "LANG_NORESULTS" => $this->lang->line('noresults')
			));
		}
	}
	
	
	/**
	 * Main search action.
	 * @example index.php/search/newposts
	*/
	public function newposts() {
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'pagination', 'form_validation'));
		$this->load->model(array('Topicmodel', 'Boardaccessmodel'));
        $this->load->helper(array('boardindex','form', 'user', 'posting', 'form_select'));

		$searchData = $this->Topicmodel->getTopicsSinceLastActive($this->Usermodel->getLastVisit(), $this->userID);

		/**
		 * Setup Pagination.
		*/
		$config = array();
		$config['base_url'] = $this->boardUrl.$this->config->item('index_page').'/search/';
		$config['total_rows'] = count($searchData);
		$config['per_page'] = $this->preference->getPreferenceValue("per_page");
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';
		$config['next_tag_open'] = '<span class="nextpage">';
		$config['next_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span class="prevpage">';
		$config['prev_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="currentpage">';
		$config['cur_tag_close'] = '</span>';
		$config['next_link'] = '&raquo;';
		$config['prev_link'] = '&laquo;';
		$config['first_link'] = $this->lang->line('pagination_first');
		$config['last_link'] = $this->lang->line('pagination_last');
		$this->pagination->initialize($config);

		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("search"), '/search/');
		$this->breadcrumb->append_crumb($this->lang->line("searchresults"), '/search/');

		#setup filters.
		$this->twig->_twig_env->addFilter('counter', new Twig_Filter_Function('GetCount'));
		$this->twig->_twig_env->addFilter('TopicReadStat', new Twig_Filter_Function('readTopicStat'));
		$this->twig->_twig_env->addFunction('Attachment', new Twig_Function_Function('HasAttachment'));
		$this->twig->_twig_env->addFunction('SubBoardCount', new Twig_Function_Function('GetSubBoardCount'));
		$this->twig->_twig_env->addFilter('ReadStat', new Twig_Filter_Function('CheckReadStatus'));

		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'results', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("searchresults"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
		  'LANG' => $this->lng,
		  'TimeFormat' => $this->timeFormat,
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
		  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin', 'id' => 'loginForm')),
		  'SEARCHFORM' => form_open('search/', array('name' => 'frmSearch', 'class' => 'form')),
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
		  'LANG_POSTEDIN' => $this->lang->line('postedin'),
		  'BREADCRUMB' =>$this->breadcrumb->output(),
		  'LANG_TOPIC' => $this->lang->line('topics'),
		  'LANG_RESULTS' => $this->lang->line('result'),
		  "SEARCH_RESULTS" => $searchData,
		  "SEARCH_RESULTS_COUNT" => count($searchData),
		  "LANG_NORESULTS" => $this->lang->line('noresults')
		));
	}

	/**
	 * search by user (posts).
	 * @example index.php/search/searchpostbyuser/foo
	*/
	public function searchpostbyuser($user) {
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'pagination', 'form_validation'));
		$this->load->model(array('Topicmodel', 'Boardaccessmodel'));
        $this->load->helper(array('boardindex','form', 'user', 'posting', 'form_select'));

		$searchDataCount = $this->Topicmodel->SearchPostCount(null, $user);

		/**
		 * Setup Pagination.
		*/
		$config = array();
		$config['base_url'] = $this->boardUrl.$this->config->item('index_page').'/search/';
		$config['total_rows'] = $searchDataCount;
		$config['per_page'] = $this->preference->getPreferenceValue("per_page");
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';
		$config['next_tag_open'] = '<span class="nextpage">';
		$config['next_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span class="prevpage">';
		$config['prev_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="currentpage">';
		$config['cur_tag_close'] = '</span>';
		$config['next_link'] = '&raquo;';
		$config['prev_link'] = '&laquo;';
		$config['first_link'] = $this->lang->line('pagination_first');
		$config['last_link'] = $this->lang->line('pagination_last');
		$this->pagination->initialize($config);

		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("search"), '/search/');
		$this->breadcrumb->append_crumb($this->lang->line("searchresults"), '/search/');

		$searchData = $this->Topicmodel->SearchPost(null, $config['per_page'], $this->uri->segment(4), $user, null);

		#setup filters.
		$this->twig->_twig_env->addFilter('counter', new Twig_Filter_Function('GetCount'));
		$this->twig->_twig_env->addFilter('TopicReadStat', new Twig_Filter_Function('readTopicStat'));
		$this->twig->_twig_env->addFunction('Attachment', new Twig_Function_Function('HasAttachment'));
		$this->twig->_twig_env->addFunction('SubBoardCount', new Twig_Function_Function('GetSubBoardCount'));
		$this->twig->_twig_env->addFilter('ReadStat', new Twig_Filter_Function('CheckReadStatus'));

		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'results', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("searchresults"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
		  'LANG' => $this->lng,
		  'TimeFormat' => $this->timeFormat,
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
		  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin', 'id' => 'loginForm')),
		  'SEARCHFORM' => form_open('search/', array('name' => 'frmSearch', 'class' => 'form')),
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
		  'LANG_POSTEDIN' => $this->lang->line('postedin'),
		  'BREADCRUMB' =>$this->breadcrumb->output(),
		  'LANG_TOPIC' => $this->lang->line('topics'),
		  'LANG_RESULTS' => $this->lang->line('result'),
		  "SEARCH_RESULTS" => $searchData,
		  "SEARCH_RESULTS_COUNT" => $searchDataCount,
		  "LANG_NORESULTS" => $this->lang->line('noresults')
		));
	}

	/**
	 * search by user (topics).
	 * @example index.php/search/searchtopicbyuser/foo
	*/
	public function searchtopicbyuser($user) {
		//LOAD LIBRARIES
		$this->load->library(array('encrypt', 'pagination', 'form_validation'));
		$this->load->model(array('Topicmodel', 'Boardaccessmodel'));
        $this->load->helper(array('boardindex','form', 'user', 'posting', 'form_select'));

		$searchDataCount = $this->Topicmodel->SearchTopicCount(null, $user);

		/**
		 * Setup Pagination.
		*/
		$config = array();
		$config['base_url'] = $this->boardUrl.$this->config->item('index_page').'/search/';
		$config['total_rows'] = $searchDataCount;
		$config['per_page'] = $this->preference->getPreferenceValue("per_page");
		$config['uri_segment'] = 4;
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = '</div>';
		$config['next_tag_open'] = '<span class="nextpage">';
		$config['next_tag_close'] = '</span>';
		$config['prev_tag_open'] = '<span class="prevpage">';
		$config['prev_tag_close'] = '</span>';
		$config['cur_tag_open'] = '<span class="currentpage">';
		$config['cur_tag_close'] = '</span>';
		$config['next_link'] = '&raquo;';
		$config['prev_link'] = '&laquo;';
		$config['first_link'] = $this->lang->line('pagination_first');
		$config['last_link'] = $this->lang->line('pagination_last');
		$this->pagination->initialize($config);

		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("search"), '/search/');
		$this->breadcrumb->append_crumb($this->lang->line("searchresults"), '/search/');

		$searchData = $this->Topicmodel->SearchTopic(null, $config['per_page'], $this->uri->segment(4), $user, null);

		#setup filters.
		$this->twig->_twig_env->addFilter('counter', new Twig_Filter_Function('GetCount'));
		$this->twig->_twig_env->addFilter('TopicReadStat', new Twig_Filter_Function('readTopicStat'));
		$this->twig->_twig_env->addFunction('Attachment', new Twig_Function_Function('HasAttachment'));
		$this->twig->_twig_env->addFunction('SubBoardCount', new Twig_Function_Function('GetSubBoardCount'));
		$this->twig->_twig_env->addFilter('ReadStat', new Twig_Filter_Function('CheckReadStatus'));

		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'results', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("searchresults"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
		  'LANG' => $this->lng,
		  'TimeFormat' => $this->timeFormat,
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
		  'LOGINFORM' => form_open('login/LogIn', array('name' => 'frmQLogin', 'id' => 'loginForm')),
		  'SEARCHFORM' => form_open('search/', array('name' => 'frmSearch', 'class' => 'form')),
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
		  'LANG_POSTEDIN' => $this->lang->line('postedin'),
		  'BREADCRUMB' =>$this->breadcrumb->output(),
		  'LANG_TOPIC' => $this->lang->line('topics'),
		  'LANG_RESULTS' => $this->lang->line('result'),
		  "SEARCH_RESULTS" => $searchData,
		  "SEARCH_RESULTS_COUNT" => $searchDataCount,
		  "LANG_NORESULTS" => $this->lang->line('noresults')
		));
	}
	
	/**
	 * search for topics/posts.
	 * @example index.php/search/LiveSearch
	*/
	public function LiveSearch($keyword) {
		//see if user is calling directly or by AJAX.
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		$searchResults = array();
		
		//fetch topic data.
		$this->db->select('t.tid, t.Topic')
		  ->from('ebb_topics t')
		  ->order_by('t.Original_Date desc')
		  ->like('t.Topic', xss_clean($keyword))
		  ->or_like('t.Body', xss_clean($keyword))
		  ->limit(20);
		$query = $this->db->get();
		
		foreach ($query->result() as $row) {
			$searchResults[] = $row;
		}
		
		echo json_encode($searchResults);
		
		/**
SELECT t.tid, t.bid, p.pid, t.Topic, t.Body AS TBODY, p.Body AS PBODY, t.Original_Date AS TDATE, p.Original_Date AS PDATE
FROM ebb_topics t
LEFT JOIN ebb_posts p ON t.tid=p.tid
WHERE t.Topic LIKE '%test%'
OR t.Body LIKE '%test%'
or p.Body LIKE '%test%'
ORDER BY TDATE desc, PDATE desc
		 */
		
		
	}
	
}
