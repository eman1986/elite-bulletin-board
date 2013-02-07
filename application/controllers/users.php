<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * users.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 01/21/2013
*/
class Users extends EBB_Controller {
	
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
	 * Main user view.
	 * @example index.php/users/5
	*/
	public function index() {
		$this->load->helper(array('boardindex', 'user', 'form', 'common'));
		$this->load->library(array('encrypt', 'breadcrumb'));
		
		//add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("members"), '/users/memberslist');
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'index', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("uoptions"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
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
		  'LANG_PROCESSINGFORM' => $this->lang->line('processingform'),
		  'LANG_POSTCOUNT' => $this->lang->line('postcount'),
		  'LANG_JOINDATE' => $this->lang->line('joindate'),
		  'LANG_BTNPMAUTHOR' => $this->lang->line('btnpmauthor'),
		  'LANG_BTNVIEWPROFILE' => $this->lang->line('viewprofile'),
		  "LANG_ACCTMENU" => $this->lang->line('accountmenu'),
		  "LANG_PROFILEMENU" => $this->lang->line("profilemenu"),
		  "LANG_NOTESMENU" => $this->lang->line("notesmenu"),
		  "LANG_PRIVACYMENU" => $this->lang->line('privacymenu'),
		  "LANG_EDITPROFILE" => $this->lang->line('editinfo'),
		  "LANG_EDITUSERSETTINGS" => $this->lang->line('editusrsettings'),
		  "LANG_EDITMESSENGER" => $this->lang->line('editmsgr'),
		  "LANG_EDITSIG" => $this->lang->line('editsig'),
		  "LANG_UPDATEEMAIL" => $this->lang->line('emailupdate'),
		  "LANG_CHANGEPASSWORD" => $this->lang->line('changepassword'),
		  "LANG_MANAGEATTACHMENTS" => $this->lang->line('manageattach'),
		  "LANG_MANAGESUBSCRIPTIONS" => $this->lang->line('subscriptionsetting'),
		  "LANG_NEWNOTES" => $this->lang->line('newnote'),
		  "LANG_MANAGENOTES" => $this->lang->line('managenotes'),
		  "LANG_NOTESSETTINGS" => $this->lang->line("notessettings"),
		  "LANG_FRIENDSLIST" => $this->lang->line("friendslist"),
		  "PROFILESETTINGS" => $this->lang->line('profilesettings'),
		  "LANG_OPTION" => $this->lang->line('profilemenu'),
		  "LANG_RANK" => $this->lang->line("rank"),
		  "RANK" => $this->Groupmodel->getName(),
		  "LANG_POSTCOUNT" => $this->lang->line("postcount"),
		  "POSTCOUNT" => number_format($this->Usermodel->getPostCount()),
		  "LANG_EMAIL" => $this->lang->line("email"),
		  "EMAIL" => $this->Usermodel->getEmail(),
		  "LANG_MSN" => $this->lang->line("msn"),
		  "MSN" => $this->Usermodel->getMSn(),
		  "LANG_AOL" => $this->lang->line("aol"),
		  "AOL" => $this->Usermodel->getAol(),
		  "LANG_ICQ" => $this->lang->line("icq"),
		  "ICQ" => $this->Usermodel->getIcq(),
		  "LANG_YAHOO" => $this->lang->line('yim'),
		  "YAHOO" => $this->Usermodel->getYahoo(),
		  "LANG_WWW" => $this->lang->line('www'),
		  "WWW" => $this->Usermodel->getWWW(),
		  "LANG_LOCATION" => $this->lang->line('location'),
		  "LOCATION" => $this->Usermodel->getLocation(),
		  "JOINED" => $this->Usermodel->getDateJoined(),
		  "LANG_LATEST_TOPICS" => $this->lang->line('latesttopics'),
		  "LANG_LATEST_POSTS" => $this->lang->line("latestreplies"),
		  "LANG_FINDTOPICS" => $this->lang->line('findtopics'),
		  "LANG_FINDPOSTS" => $this->lang->line("findposts"),
		  "AVATAR" => $this->preference->getPreferenceValue("avatar_type") == "gravatar" ? getGravatar($this->Usermodel->getEmail(), $this->preference->getPreferenceValue("gravatar_size"), $this->preference->getPreferenceValue("gravatar_noresults"), $this->preference->getPreferenceValue("gravatar_rating"), $this->preference->getPreferenceValue("gravatar_secure")): $this->Usermodel->getAvatar()
		));
	}
	
	/**
	 * view user's profile.
	 * @example index.php/users/viewprofile/5
	*/
	public function viewprofile($id) {
		$this->load->helper(array('boardindex', 'user', 'form', 'common'));
		$this->load->library(array('encrypt', 'breadcrumb'));
		$this->load->model('Usermodel', 'usr');
		$this->load->model('Groupmodel', 'ugr');
		$usrData = $this->usr->getUser($id);
		if ($usrData) {
			//get group data.
			$groupData = $this->ugr->GetGroupData($this->usr->getGid());
			if ($groupData) {
				//add breadcrumbs
				$this->breadcrumb->append_crumb($this->title, '/');
				$this->breadcrumb->append_crumb($this->lang->line("viewprofile"), '/users/viewprofile');

				//render to HTML.
				echo $this->twig->render(strtolower(__CLASS__), 'viewprofile', array (
				  'boardName' => $this->title,
				  'pageTitle'=> $this->lang->line("uoptions"),
				  'THEME_NAME' => $this->getStyleName(),
				  'STYLELIST' => $this->styleList,
				  'INDEX_PAGE' => $this->config->item('index_page'),
				  'BOARD_URL' => $this->boardUrl,
				  'APP_URL' => $this->boardUrl.APPPATH,
				  'NOTIFY_TYPE' => $this->notifyType,
				  'NOTIFY_MSG' =>  $this->notifyMsg,
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
				  'LANG_BTNVIEWPROFILE' => $this->lang->line('viewprofile'),
				  'USERNAME' => $this->usr->getUsername(),
				  'LANG_PROCESSINGFORM' => $this->lang->line('processingform'),
				  'LANG_POSTCOUNT' => $this->lang->line('postcount'),
				  'LANG_JOINDATE' => $this->lang->line('joindate'),
				  "LANG_FRIENDSLIST" => $this->lang->line("friendslist"),
				  "USER_ID" => $id,
				  "LANG_RANK" => $this->lang->line("rank"),
				  "RANK" => $this->ugr->getName(),
				  "LANG_POSTCOUNT" => $this->lang->line("postcount"),
				  "POSTCOUNT" => number_format($this->usr->getPostCount()),
				  "LANG_EMAIL" => $this->lang->line("email"),
				  "EMAIL" => $this->usr->getEmail(),
				  "LANG_MSN" => $this->lang->line("msn"),
				  "MSN" => $this->usr->getMSn(),
				  "LANG_AOL" => $this->lang->line("aol"),
				  "AOL" => $this->usr->getAol(),
				  "LANG_ICQ" => $this->lang->line("icq"),
				  "ICQ" => $this->usr->getIcq(),
				  "LANG_YAHOO" => $this->lang->line('yim'),
				  "YAHOO" => $this->usr->getYahoo(),
				  "LANG_WWW" => $this->lang->line('www'),
				  "WWW" => $this->usr->getWWW(),
				  "LANG_LOCATION" => $this->lang->line('location'),
				  "LOCATION" => $this->usr->getLocation(),
				  "JOINED" => $this->usr->getDateJoined(),
				  "LANG_FINDTOPICS" => $this->lang->line('findtopics'),
				  "LANG_FINDPOSTS" => $this->lang->line("findposts"),
				  "LANG_ADDFRIEND" => $this->lang->line("addfriend"),
				  "LANG_BLOCKUSER" => $this->lang->line("blockuser"),
				  "AVATAR" => $this->preference->getPreferenceValue("avatar_type") == "gravatar" ? getGravatar($this->usr->getEmail(), $this->preference->getPreferenceValue("gravatar_size"), $this->preference->getPreferenceValue("gravatar_noresults"), $this->preference->getPreferenceValue("gravatar_rating"), $this->preference->getPreferenceValue("gravatar_secure")): $this->usr->getAvatar()
				));
			} else {
				$this->notifications('error', $this->lang->line('invalidgid'));
				redirect('/', 'location');
			}
		} else {
			$this->notifications('error', $this->lang->line('invaliduser'));
			redirect('/', 'location');
		}
	}

	/**
	 * confirm friendship request.
	 * @example index.php/users/confirmfriendship/4
	*/
	public function confirmfriendship($id) {
		$this->load->model('Relationshipmodel');
		$this->Relationshipmodel->updateRelationship($id, 1);
		$this->notifications("success", $this->lang->line('friendshipreqaccepted'));
		redirect('/', 'location');
	}
	
	/**
	 * ignore friendship request.
	 * @example index.php/users/ignorefriendship/4
	*/
	public function ignorefriendship($id) {
		$this->load->model('Relationshipmodel');
		$this->Relationshipmodel->RemoveRelationship($id);
		$this->notifications("success", $this->lang->line('friendshipreqblocked'));
		redirect('/', 'location');
	}
	
	/**
	 * view user's private messages.
	 * @example index.php/users/messages/inbox
	*/
	public function messages($folder="inbox") {
		if (!$this->Groupmodel->ValidateAccess(1, 27)){
			$this->notifications('error', $this->lang->line('accessdenied'));
			redirect('/', 'location');
			exit();
		}

		$this->load->helper(array('boardindex', 'user', 'form'));
		$this->load->library(array('encrypt', 'breadcrumb'));
		
		//add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("pm"), '/users/messages/');
		
		//make sure the user is looking at valid folders only.
		if (!in_array($folder, Pmmodel::$validFolders)) {
			exit(show_error($this->lang->line('invalidfolder'), 500, $this->lang->line('error')));
		}
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'messages', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("pm"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
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
		  'FOLDER' => $folder,
		  'BREADCRUMB' =>$this->breadcrumb->output(),
		  'PER_PAGE' => $this->preference->getPreferenceValue("per_page"),
		  'LANG_SUBJECT' => $this->lang->line('subject'),
		  'LANG_SENDER' => $this->lang->line('sender'),
		  'LANG_DATE' => $this->lang->line('date'),
		  'LANG_DELETE_PM' => $this->lang->line('delpm'),
		  'LANG_CONFIRM_DELETE' => $this->lang->line('confirmdelete'),
		  'LANG_ARCHIVE_MESSAGE' => $this->lang->line('movemsg'),
		  'LANG_CONFIRM_ARCHIVE' => $this->lang->line('moveconfirm'),
		  'LANG_BTNCLOSE' => $this->lang->line('close')
		));
	}
	
	/**
	 * view private message.
	 * @example index.php/users/viewmessage/5
	*/
	public function viewmessage($id) {
		if (!$this->Groupmodel->ValidateAccess(1, 27)){
			$this->notifications('error', $this->lang->line('accessdenied'));
			redirect('/', 'location');
		}

		//see if the requested message exists.
		if ($this->Pmmodel->getPMMessage($id)) {
			//validate the user viewing the message is the owner of the message.
			if (!$this->Pmmodel->IsPMOwner($this->userID)) {
				$this->notifications('error', $this->lang->line('pmaccessdenied'));
				redirect('/', 'location');
			}
			
			//mark message as read.
			$this->Pmmodel->markAsRead();
			
			//load helpers & libraries.
			$this->load->helper(array('posting', 'boardindex', 'user', 'form'));
			$this->load->library(array('encrypt', 'breadcrumb'));

			//add breadcrumbs
			$this->breadcrumb->append_crumb($this->title, '/');
			$this->breadcrumb->append_crumb($this->lang->line("pm"), '/users/messages/'.$this->Pmmodel->getFolder());
			$this->breadcrumb->append_crumb($this->lang->line("viewpm"), '/users/viewmessage/'.$id);
			
			//render to HTML.
			$this->twig->_twig_env->addFunction('FormatMsg', new Twig_Function_Function('FormatTopicBody'));
			$this->twig->_twig_env->addFunction('Spam_Filter', new Twig_Function_Function('language_filter'));
			echo $this->twig->render(strtolower(__CLASS__), 'viewmessage', array (
			  'boardName' => $this->title,
			  'pageTitle'=> $this->lang->line("pm"),
			  'THEME_NAME' => $this->getStyleName(),
			  'STYLELIST' => $this->styleList,
			  'INDEX_PAGE' => $this->config->item('index_page'),
			  'BOARD_URL' => $this->boardUrl,
			  'APP_URL' => $this->boardUrl.APPPATH,
			  'NOTIFY_TYPE' => $this->notifyType,
			  'NOTIFY_MSG' =>  $this->notifyMsg,
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
			  'FOLDER' => $this->Pmmodel->getFolder(),
			  'PM_ID' => $this->Pmmodel->getId(),
			  'LANG_SUBJECT' => $this->lang->line('subject'),
			  'SUBJECT' => $this->Pmmodel->getSubject(),
			  'LANG_SENDER' => $this->lang->line('from'),
			  'SENDER' => $this->Pmmodel->getSender(),
			  'LANG_RECEIVER' => $this->lang->line('to'),
			  'RECEIVER' => $this->Pmmodel->getReceiver(),
			  'LANG_POSTED_ON' => $this->lang->line('date'),
			  'POSTED_ON' => $this->Pmmodel->getDate(),
			  'PM_BODY' => $this->Pmmodel->getMessage(),
			  'LANG_DELETE_PM' => $this->lang->line('delpm'),
			  'LANG_REPLY_PM' => $this->lang->line('replypm'),
			  'LANG_CONFIRM_DELETE' => $this->lang->line('confirmdelete'),
			  'LANG_ARCHIVE_MESSAGE' => $this->lang->line('movemsg'),
			  'LANG_CONFIRM_ARCHIVE' => $this->lang->line('moveconfirm'),
			  'LANG_BTNCLOSE' => $this->lang->line('close')
			));
		} else {
			$this->notifications('warning', $this->lang->line('pm404'));
			redirect('/users/messages/Inbox', 'location');
		}
	}
	
	/**
	 * Memberlist view.
	 * @example index.php/users/memberslist
	*/
	public function memberslist() {
		$this->load->helper(array('boardindex', 'user', 'form'));
		$this->load->library(array('encrypt', 'breadcrumb'));
		
		//add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("members"), '/users/memberslist');
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'memberlist', array (
		  'boardName' => $this->title,
		  'pageTitle'=> $this->lang->line("members"),
		  'THEME_NAME' => $this->getStyleName(),
		  'STYLELIST' => $this->styleList,
		  'INDEX_PAGE' => $this->config->item('index_page'),
		  'BOARD_URL' => $this->boardUrl,
		  'APP_URL' => $this->boardUrl.APPPATH,
		  'NOTIFY_TYPE' => $this->notifyType,
		  'NOTIFY_MSG' =>  $this->notifyMsg,
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
		  'PER_PAGE' => $this->preference->getPreferenceValue("per_page"),
		  'LANG_POSTCOUNT' => $this->lang->line('postcount'),
		  'LANG_CUSTOMTITLE' => $this->lang->line('customtitle'),
		  'LANG_BTNPMAUTHOR' => $this->lang->line('btnpmauthor'),
		  'LANG_BTNVIEWPROFILE' => $this->lang->line('viewprofile'),
		  'LANG_BTNCLOSE' => $this->lang->line('close')
		));
	}
	
	/**
	 * Change the theme to a different one.
	 * @param integer $id The styleId to of the new theme.
	 */
	public function changetheme($id) {
		//validate style ID.
		$loadStyle = $this->Stylemodel->GetStyleData($id);
		
		if ($loadStyle) {
			//update user's profile with new theme.
			$data = array(
			  'Style' => $id
			  );
			
			#set id field.
			$this->Usermodel->setId($this->userID);
			
			#update user.
			$this->Usermodel->UpdateUser($data);
			
			//display message to user.
			$this->notifications('success', $this->lang->line('applythemesuccess'));
		} else {
			//display message to user.
			$this->notifications('error', $this->lang->line('applythemefail'));
		}
		
		//@todo try to figure out how to validate the HTTP_REFERER value from spoofing.
		if (isset($_SERVER['HTTP_REFERER'])) {
			 redirect($_SERVER['HTTP_REFERER'], 'location'); //redirect user to index page.
		} else {
			redirect('/', 'location'); //redirect user to index page.
		}
	}
	
	/**
	 * load hovercard profile.
	 * @example index.php/users/viewvcard/5
	 */
	public function viewvcard($id) {
		//see if user is calling directly or by AJAX.
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$userData = $this->Usermodel->getUser($id);
		$groupData = $this->Groupmodel->GetGroupData($this->Usermodel->gid);
		
		if ($userData && $groupData) {
			//render to HTML.
			echo $this->twig->render(strtolower(__CLASS__), 'hovercard', array (
			  'BOARD_URL' => $this->boardUrl,
			  'USERNAME' => $this->Usermodel->getUserName(),
			  "AVATAR" => $this->preference->getPreferenceValue("avatar_type") == "gravatar" ? getGravatar($this->Usermodel->getEmail(), $this->preference->getPreferenceValue("gravatar_size"), $this->preference->getPreferenceValue("gravatar_noresults"), $this->preference->getPreferenceValue("gravatar_rating"), $this->preference->getPreferenceValue("gravatar_secure")): $this->Usermodel->getAvatar(),
			  'GROUPLEVEL' => $this->Groupmodel->getLevel(),
			  'GROUPNAME' => $this->Groupmodel->getName(),
			  'CTITLE' => $this->Usermodel->getCustomTitle(),
			  'LANG_POSTCOUNT' => $this->lang->line('postcount'),
			  'POSTCOUNT' => $this->Usermodel->getPostCount()
			));
		} else {
			exit($this->lang->line('invaliduser'));
		}
	}
}

