<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * general.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/12/2013
*/

class General extends EBB_Controller {
	
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
	 * list all installed styles.
	 * @example index.php/ACP/general/styles
	*/
	public function styles() {
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("admincp"), '/ACP/');
		$this->breadcrumb->append_crumb($this->lang->line("managestyles"), '/ACP/general/styles');
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'styles', array (
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
			'PER_PAGE' => $this->preference->getPreferenceValue("per_page"),
			'LANG_STYLE_NAME' => $this->lang->line('stylename'),
			'LANG_INSTALL_STYLE' => $this->lang->line('installstyle'),
			'LANG_UNINSTALL_STYLE' => $this->lang->line('uninstallstyle'),
			'LANG_CONFIRM_STYLE_UNINSTALL' => $this->lang->line('uninstallstyleconfirm')
		));
	}

	/**
	 * uninstall style.
	 * @example index.php/ACP/general/uninstallstyle/5
	*/
	public function uninstallstyle($id) {
		
	}
	
	/**
	 * newsletter form.
	 * @example index.php/ACP/general/newsletter
	*/
	public function newsletter() {
		
	}
	
}