<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * settings.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 07/06/2013
*/

class Settings extends EBB_Controller {

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
		
		//load audit model.
		$this->load->model('Cplogmodel');
	}
	
	/**
	 * user settings form.
	 * @example index.php/ACP/settings/user
	*/
	public function user() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'user', array (
		  'USERSSETTINGSFORM' => form_open('ACP/settings/userSubmit/', array('name' => 'frmUserSettings', 'id' => 'frmUserSettings', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_YES' => $this->lang->line('yes'),
		  'LANG_NO' => $this->lang->line('no'),
		  'LANG_ON' => $this->lang->line('on'),
		  'LANG_OFF' => $this->lang->line('off'),
		  'LANG_ENABLE_REGISTER_RULES' => $this->lang->line('tosstat'),
		  'ENABLE_REGISTER_RULES' => $this->preference->getPreferenceValue("rules_status"),
		  'LANG_REGISTER_RULES' => $this->lang->line('tos'),
		  'REGISTER_RULES' => $this->preference->getPreferenceValue("rules"),
		  'LANG_REGISTER_STATUS' => $this->lang->line('registerstat'),
		  'REGISTER_STATUS' => $this->preference->getPreferenceValue("allow_newusers"),
		  'LANG_ACTIVATION_TYPE' => $this->lang->line('activation'),
		  'ACTIVATION_TYPE' => $this->preference->getPreferenceValue("activation"),
		  'ACTIVATION_TYPE_NONE' => $this->lang->line('none'),
		  'ACTIVATION_TYPE_USER' => $this->lang->line('activeusers'),
		  'ACTIVATION_TYPE_ADMIN' => $this->lang->line('activeadmin'),
		  'LANG_NEWUSER_STATUS' => $this->lang->line('autogroupsel'),
		  'NEWUSER_GROUP_SELECT' => newUserGroupSelect($this->preference->getPreferenceValue("userstat")),
		  'LANG_COPPA_STATUS' => $this->lang->line('copparule'),
		  'COPPA_SELECT' => coppaSelect($this->preference->getPreferenceValue("coppa")),
		  'LANG_CAPTCHA_STATUS' => $this->lang->line('cpcaptcha'),
		  'CAPTCHA_STATUS' => $this->preference->getPreferenceValue("captcha"),
		  'LANG_MX_VALIDATION' => $this->lang->line('mxcheck'),
		  'MX_VALIDATION' => $this->preference->getPreferenceValue("mx_check"),
		  'LANG_MX_HINT' => $this->lang->line('mxcheckhint'),
		  'LANG_WARNING_THRESHOLD' => $this->lang->line('warnthreshold'),
		  'WARNING_THRESHOLD_SELECT' => warningThresholdSelect($this->preference->getPreferenceValue("warning_threshold")),
		  'LANG_WARNING_HINT' => $this->lang->line('warnthresholdhint'),
		  'LANG_PM_INBOX_QUOTA' => $this->lang->line('pminboxquota'),
		  'PM_INBOX_QUOTA' => $this->preference->getPreferenceValue("pm_quota"),
		  'LANG_PM_ARCHIVE_QUOTA' => $this->lang->line('pmarchivequota'),
		  'PM_ARCHIVE_QUOTA' => $this->preference->getPreferenceValue("archive_quota"),
		  'LANG_SAVESETTINGS' => $this->lang->line('savesettings'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * Submit user settings to server.
	 * @example index.php/ACP/settings/userSubmit
	*/
	public function userSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$data = array();
	
		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper('form');

		$this->form_validation->set_rules('pm_inbox_quota', $this->lang->line('pminboxquota'), 'required|numeric|max_length[3]|xss_clean');
		$this->form_validation->set_rules('pm_archive_quota', $this->lang->line('pmarchivequota'), 'required|numeric|max_length[3]|xss_clean');
		
		if ($this->input->post('enable_register_rules', TRUE) == 1) {
			$this->form_validation->set_rules('register_rules', $this->lang->line('tos'), 'required|xss_clean');
		}
		
		$this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
		if ($this->form_validation->run()) {
			//save our settings.
			$this->preference->savePreferences("rules_status",Preference::$boolean, $this->input->post('enable_register_rules', TRUE));
			$this->preference->savePreferences("rules", Preference::$string, $this->input->post('register_rules', TRUE));
			$this->preference->savePreferences("allow_newusers", Preference::$boolean, $this->input->post('register_status', TRUE));
			$this->preference->savePreferences("activation", Preference::$string, $this->input->post('activation_type', TRUE));
			$this->preference->savePreferences("userstat", Preference::$numeric, $this->input->post('newuser_status', TRUE));
			$this->preference->savePreferences("coppa", Preference::$numeric, $this->input->post('coppa', TRUE));
			$this->preference->savePreferences("captcha", Preference::$boolean, $this->input->post('capcha_status', TRUE));
			$this->preference->savePreferences("mx_check", Preference::$boolean, $this->input->post('mx_validation', TRUE));
			$this->preference->savePreferences("warning_threshold", Preference::$numeric, $this->input->post('warning_threshold', TRUE));
			$this->preference->savePreferences("pm_quota", Preference::$numeric, $this->input->post('pm_inbox_quota', TRUE));
			$this->preference->savePreferences("archive_quota", Preference::$numeric, $this->input->post('pm_archive_quota', TRUE));
			
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Updated User Settings");
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

			// validation ok
			$data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('usrsettingssuccess'),
            		'fields' => array(
                    	'enable_register_rules' => '',
                    	'register_rules' => '',
                    	'register_status' => '',
					    'activation_type' => '',
                    	'newuser_status' => '',
                    	'coppa' => '',
					    'capcha_status' => '',
                    	'mx_validation' => '',
                    	'warning_threshold' => '',
					    'pm_inbox_quota' => '',
                    	'pm_archive_quota' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
					    'enable_register_rules' => form_error('enable_register_rules'),
                    	'register_rules' => form_error('register_rules'),
                    	'register_status' => form_error('register_status'),
					    'activation_type' => form_error('activation_type'),
                    	'newuser_status' => form_error('newuser_status'),
                    	'coppa' => form_error('coppa'),
					    'capcha_status' => form_error('capcha_status'),
                    	'mx_validation' => form_error('mx_validation'),
                    	'warning_threshold' =>form_error('warning_threshold'),
					    'pm_inbox_quota' => form_error('pm_inbox_quota'),
                    	'pm_archive_quota' => form_error('pm_archive_quota')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * board settings form.
	 * @example index.php/ACP/settings/board
	*/
	public function board() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'board', array (
		  'BOARDSETTINGSFORM' => form_open('ACP/settings/boardSubmit/', array('name' => 'frmBoardSettings', 'id' => 'frmBoardSettings', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_ON' => $this->lang->line('on'),
		  'LANG_OFF' => $this->lang->line('off'),
		  'LANG_BOARD_NAME' => $this->lang->line('boardname'),
		  'BOARD_NAME' => $this->preference->getPreferenceValue("board_name"),
		  'LANG_BOARD_EMAIL' => $this->lang->line('boardemail'),
		  'BOARD_EMAIL' => $this->preference->getPreferenceValue("board_email"),
		  'LANG_BOARD_STATUS' => $this->lang->line('boardstatus'), //@TODO seriously consider calling this 'Maintenance Mode' instead.
		  'BOARD_STATUS' => $this->preference->getPreferenceValue("board_status"),
		  'LANG_PER_PAGE' => $this->lang->line('perpg'),
		  'PER_PAGE_SELECT' => perPageDropDown($this->preference->getPreferenceValue("per_page")),
		  'LANG_STYLE' => $this->lang->line('defaultstyle'),
		  'STYLE_SELECT' => ThemeList($this->preference->getPreferenceValue("default_style")),
		  'LANG_LANGUAGE' => $this->lang->line('defaultlangacp'),
		  'LANGUAGE_SELECT' => LanguageList($this->preference->getPreferenceValue("default_language")),
		  'LANG_TIMEZONE' => $this->lang->line('timezone'),
		  'TIMEZONE_SELECT' => TimeZoneList($this->preference->getPreferenceValue("timezone")),
		  'LANG_DATE_FORMAT' => $this->lang->line('dateformat'),
		  'DATE_FORMAT_SELECT' => dateFormatSelect($this->preference->getPreferenceValue("dateformat")),
		  'LANG_TIME_FORMAT' => $this->lang->line('timeformat'),
		  'TIME_FORMAT_SELECT' => timeFormatSelect($this->preference->getPreferenceValue("timeformat")),
		  'LANG_SAVESETTINGS' => $this->lang->line('savesettings'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * submit board settings to server.
	 * @example index.php/ACP/settings/boardSubmit
	*/
	public function boardSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$data = array();
	
		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper('form');

		$this->form_validation->set_rules('board_name', $this->lang->line('pminboxquota'), 'required|max_length[50]|xss_clean');
		$this->form_validation->set_rules('board_email', $this->lang->line('pmarchivequota'), 'required|valid_email|max_length[255]|xss_clean');
		
		$this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
		if ($this->form_validation->run()) {
			//save our settings.
			$this->preference->savePreferences("board_name",Preference::$string, $this->input->post('board_name', TRUE));
			$this->preference->savePreferences("board_email", Preference::$string, $this->input->post('board_email', TRUE));
			$this->preference->savePreferences("board_status", Preference::$boolean, $this->input->post('board_status', TRUE));
			$this->preference->savePreferences("per_page", Preference::$numeric, $this->input->post('per_page', TRUE));
			$this->preference->savePreferences("default_style", Preference::$numeric, $this->input->post('style', TRUE));
			$this->preference->savePreferences("default_language", Preference::$string, $this->input->post('language', TRUE));
			$this->preference->savePreferences("timezone", Preference::$string, $this->input->post('time_zone', TRUE));
			$this->preference->savePreferences("dateformat", Preference::$numeric, $this->input->post('date_format', TRUE));
			$this->preference->savePreferences("timeformat", Preference::$numeric, $this->input->post('time_format', TRUE));
			
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Updated Board Settings");
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

		// validation ok
		$data = array(
		  'status' => 'success', 
		  'msg' =>  $this->lang->line('boardsettingssuccess'),
		  'fields' => array(
			'board_name' => '',
			'board_email' => '',
			'board_status' => '',
			'per_page' => '',
			'style' => '',
			'language' => '',
			'time_zone' => '',
			'date_format' => '',
			'time_format' => ''
			)
		  );
        } else {
		$data = array(
		  'status' => 'error',
		  'msg' => $this->lang->line('formfail'),
		  'fields' => array(
			'board_name' => form_error('board_name'),
			'board_email' => form_error('board_email'),
			'board_status' => form_error('board_status'),
			'per_page' => form_error('per_page'),
			'style' => form_error('style'),
			'language' => form_error('language'),
			'time_zone' => form_error('time_zone'),
			'date_format' => form_error('date_format'),
			'time_format' => form_error('time_format')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * announcement settings.
	 * @example index.php/ACP/settings/announcement
	*/
	public function announcement() {
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("admincp"), '/ACP/');
		$this->breadcrumb->append_crumb($this->lang->line("announcementsettings"), '/ACP/settings/announcement');
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'announcement', array (
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
			'LANG_NEWGROUP' => $this->lang->line('addgroup'),
		    'LANG_USERSETTINGS' => $this->lang->line('usersettings'),
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
			'PER_PAGE' => $this->preference->getPreferenceValue("per_page"),
			'LANG_ANNOUNCEMENTLIST' => $this->lang->line('announcementlist'),
			'LANG_ANNOUNCEMENT_MESSAGE' => $this->lang->line('announce'),
			'LANG_ADD_ANNOUNCEMENT' => $this->lang->line('addannouncement'),
			'LANG_DELETE' => $this->lang->line('deleteannouncement'),
			'LANG_CONFIRM_DELETE' => $this->lang->line('confirmdeleteannouncement')
		));
	}
	
	/**
	 * add announcement form.
	 * @example index.php/ACP/settings/addAnnouncement
	*/
	public function addAnnouncement() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'addAnnouncement', array (
		  'ANNOUNCMENTFORM' => form_open('ACP/settings/addAnnouncementSubmit/', array('name' => 'frmAddAnnouncment', 'id' => 'frmAddAnnouncment', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_ANNOUNCMENT_MESSAGE' => $this->lang->line('announce'),
		  'LANG_ADDANNOUNCEMENT' => $this->lang->line('addannouncement'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * add announcement item to server.
	 * @example index.php/ACP/settings/addAnnouncementSubmit
	*/
	public function addAnnouncementSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$data = array();
	
		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper('form');
		$this->load->model('Informationtickermodel');

		$this->form_validation->set_rules('announcement_message', $this->lang->line('announce'), 'required|max_length[50]|xss_clean');
		
		$this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
		if ($this->form_validation->run()) {
			//insert new record.
			$this->Informationtickermodel->setInformation($this->input->post('announcement_message', TRUE));
			$this->Informationtickermodel->create();

			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Added an announcement item");
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

		// validation ok
		$data = array(
		  'status' => 'success', 
		  'msg' =>  $this->lang->line('createannouncementsuccess'),
		  'fields' => array(
			'announcement_message' => ''
			)
		  );
        } else {
		$data = array(
		  'status' => 'error',
		  'msg' => $this->lang->line('formfail'),
		  'fields' => array(
			'announcement_message' => form_error('announcement_message')
			)
		  );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * delete announcement.
	 * @example index.php/ACP/settings/deleteAnnouncement
	*/
	public function deleteAnnouncement($id) {
		$data = array();
		
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$this->load->model('Informationtickermodel');
		$this->Informationtickermodel->setId($id);

		#delete board & associated data.
		$this->Informationtickermodel->delete();
		
		#log this into our audit system.
		$this->Cplogmodel->setUser($this->userID);
		$this->Cplogmodel->setAction("Deleted Announcement Item");
		$this->Cplogmodel->setDate(time());
		$this->Cplogmodel->setIp(detectProxy());
		$this->Cplogmodel->logAction();
		
		$data = array(
			'status' => 'success', 
			'msg' =>  $this->lang->line('deleteannouncementsuccess')
		);
		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * attachment settings form.
	 * @example index.php/ACP/settings/attachment
	*/
	public function attachment() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'attachment', array (
		  'ATTACHMENTFORM' => form_open('ACP/settings/attachmentSubmit/', array('name' => 'frmAttachmentSettings', 'id' => 'frmAttachmentSettings', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_YES' => $this->lang->line('yes'),
		  'LANG_NO' => $this->lang->line('no'),
		  'LANG_MAX_UPLOAD' => $this->lang->line('attachmentquota'),
		  'LANG_SIZE_HINT' => $this->lang->line('attachmentquotahint'),
		  'MAX_UPLOAD' => $this->preference->getPreferenceValue("attachment_quota"),
		  'LANG_GUEST_DOWNLOADS' => $this->lang->line('guestdownload'),
		  'GUEST_DOWNLOADS' => $this->preference->getPreferenceValue('allow_guest_downloads'),
		  'LANG_SAVESETTINGS' => $this->lang->line('savesettings'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * save attachment settings to server.
	 * @example index.php/ACP/settings/attachmentSubmit
	*/
	public function attachmentSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$data = array();
	
		// LOAD LIBRARIES
		$this->load->library(array('encrypt', 'form_validation'));
		$this->load->helper('form');

		$this->form_validation->set_rules('max_upload', $this->lang->line('attachmentquota'), 'required|numeric|xss_clean');
		$this->form_validation->set_rules('guest_downloads', $this->lang->line('guestdownload'), 'required|xss_clean');
		
		$this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
		if ($this->form_validation->run()) {
			//save our settings.
			$this->preference->savePreferences("attachment_quota", Preference::$numeric, $this->input->post('max_upload', TRUE));
			$this->preference->savePreferences("allow_guest_downloads", Preference::$boolean, $this->input->post('guest_downloads', TRUE));
			
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Updated Attachment Settings");
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

		// validation ok
		$data = array(
		  'status' => 'success', 
		  'msg' =>  $this->lang->line('attachmentsettingssuccess'),
		  'fields' => array(
			'max_upload' => '',
			'guest_downloads' => ''
			)
		  );
        } else {
		$data = array(
		  'status' => 'error',
		  'msg' => $this->lang->line('formfail'),
		  'fields' => array(
			'max_upload' => form_error('max_upload'),
			'guest_downloads' => form_error('guest_downloads')
			)
		  );
        }        
        echo json_encode($data); //return results in JSON format.
	}
}