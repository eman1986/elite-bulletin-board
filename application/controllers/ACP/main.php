<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * main.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 01/09/2013
*/

class Main extends EBB_Controller {

	function __construct() {
 		parent::__construct();
		
		//see if user is an administrator.
		if ($this->groupAccess != 1) {
			//see if user is calling directly or by AJAX.
			if (IS_AJAX) {
				exit($this->lang->line('accessdenied'));
			} else {
				//show success message.
				$this->notifications('warning', $this->lang->line('accessdenied'));

				#direct user to login page.
				redirect('/', 'location');
			}
		}
		
		//load breadcrumb library
		$this->load->helper(array('admin', 'form'));
		
		//load breadcrumb library
		$this->load->library('breadcrumb');
		
		//load audit model.
		$this->load->model('Cplogmodel');
	}
	
	/**
	 * main menu for ACP.
	 * @example index.php/ACP/main/index
	*/
	public function index() {
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("admincp"), '/ACP');
		
		if ($this->Groupmodel->ValidateAccess(1, 10)) {
			$verData = versionCheck();
		} else {
			$verData = array(
			  'status' => 'error',
			  'msg' => $this->lang->line('updateerr'),
			  'notes' => NULL,
			  'patch_link' => NULL,
			  'severity' => NULL
			  );
		}
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'index', array (
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
            'LANG_POSTEDBY' => $this->lang->line('Postedby'),
            'groupAccess' => $this->groupAccess,
			'BREADCRUMB' => $this->breadcrumb->output(),
			'LANG_ACPINFO' => $this->lang->line('acpinfo'),
			'GAC_VERSIONINFO' => $this->Groupmodel->ValidateAccess(1, 10),
			'GAC_SERVERINFO' => $this->Groupmodel->ValidateAccess(1, 9),
			'GAC_AUDITLOG' => $this->Groupmodel->ValidateAccess(1, 11),
			'LANG_VERSIONINFO' => $this->lang->line('verdetails'),
			'VERSIONDATA_MSG' => $verData['msg'],
			'LANG_INSTALLEDVERSION' => $this->lang->line('ebb_version'),
			'VERSION_MAJOR' => $this->config->item("version_major"),
			'VERSION_MINOR' => $this->config->item("version_minor"),
			'VERSION_PATCH' => $this->config->item("version_patch"),
			'VERSION_BUILD' => $this->config->item("version_build"),
			'VERSIONDATA_NOTES' => (!is_null($verData['notes'])) ? nl2br($verData['notes']) : '',
			'LANG_VERSIONDATA_PATCH_LINK' => $this->lang->line('update_patchurl'),
			'VERSIONDATA_PATCH_LINK' => $verData['patch_link'],
			'LANG_VERSIONDATA_SEVERITY' => $this->lang->line('update_severity'),
			'VERSIONDATA_SEVERITY' => $verData['severity'],
			'LANG_SERVERINFO' => $this->lang->line('server_info'),
			'LANG_DATABASE_PLATFORM' => $this->lang->line('db_driver'),
			'DATABASE_PLATFORM' => $this->db->platform(),
			'LANG_DATABASE_VERSION' => $this->lang->line('db_version'),
			'DATABASE_VERSION' => $this->db->version(),
			'LANG_PHP_VERSION' => $this->lang->line('php_ver'),
			'PHP_VERSION' => phpversion(),
			'LANG_PHPINFO' => $this->lang->line('php_info'),
			'LANG_AUDITLOG' => $this->lang->line('acp_auditlog'),
			'PER_PAGE' => $this->preference->getPreferenceValue("per_page"),
			'LANG_PERFORMEDBY' => $this->lang->line('acp_performedby'),
			'LANG_AUDITIP' => $this->lang->line('iplogged'),
			'LANG_ACTION' => $this->lang->line('acp_actionperformed'),
			'LANG_DATE' => $this->lang->line('acp_performedon'),
			'LANG_CLEARAUDITLOG' => $this->lang->line('acp_lclear'),
			'LANG_CFMCLEARLOG' => $this->lang->line('confirmacpclear'),
			'LANG_BOARDMENU' => $this->lang->line('boardmenu'),
			'LANG_BOARDSETUP' => $this->lang->line('boardsetup'),
			'LANG_NEWBOARD' => $this->lang->line('newboard'),
			'LANG_NEWPARENTBOARD' => $this->lang->line('newparentboard'),
			'LANG_NEWSUBBOARD' => $this->lang->line('newsubboard'),
			'LANG_GENERALMENU' => $this->lang->line('generalmenu'),
			'LANG_MANAGESTYLES' => $this->lang->line('managestyles'),
			'LANG_NEWSLETTER' => $this->lang->line('newsletter'),
			'LANG_SMILES' => $this->lang->line('smiles'),
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
		  
		  
//			'LANG_STYLEMENU' => $this->lang->line('stylemenu'),
			'LANG_SETTINGS' => $this->lang->line('settings'),
		));
	}

	/**
	 * View your PHP Configuation.
	 * @example index.php/ACP/main/clearlog
	*/
	public function clearlog() {
		//load Pmmodel
		$this->load->model('Cplogmodel');
		$this->Cplogmodel->clearAll();
		
		#log this into our audit system.
		$this->Cplogmodel->setUser($this->userID);
		$this->Cplogmodel->setAction("Cleared Audit Log");
		$this->Cplogmodel->setDate(time());
		$this->Cplogmodel->setIp(detectProxy());
		$this->Cplogmodel->logAction();
		
		#direct user to login page.
		redirect('/ACP', 'location');
	}

	/**
	 * View your PHP Configuation.
	 * @example index.php/ACP/main/phpinfo
	*/
	public function phpinfo() {
		ob_start();
		phpinfo();
		$string = ob_get_contents();
		$string = strchr($string, '</style>');
		$string = str_replace('</style>','',$string);
		$string = str_replace('class="p"','',$string);
		$string = str_replace('class="e"','class="td2"',$string);
		$string = str_replace('class="v"','class="td1"',$string);
		$string = str_replace('class="h"','class="td1"',$string);
		$string = str_replace('class="center"','',$string);
		ob_end_clean();

		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("admincp"), '/ACP/');
		$this->breadcrumb->append_crumb($this->lang->line("php_info"), '/ACP/phpinfo');
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'phpinfo', array (
            'boardName' => $this->title,
			'THEME_NAME' => $this->getStyleName(),
			'STYLELIST' => $this->styleList,
            'pageTitle'=> $this->lang->line('admincp'),
			'subpageTitle'=> $this->lang->line('php_info'),
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
			'BREADCRUMB' => $this->breadcrumb->output(),
			'PHPINFO' => $string
		));

		#log this into our audit system.
		$this->Cplogmodel->setUser($this->userID);
		$this->Cplogmodel->setAction("Viewed PHP Info Page");
		$this->Cplogmodel->setDate(time());
		$this->Cplogmodel->setIp(detectProxy());
		$this->Cplogmodel->logAction();
		ob_end_flush();
	}

}