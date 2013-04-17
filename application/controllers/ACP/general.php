<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * general.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/17/2013
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
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		//load Stylemodel
		$this->load->model('Stylemodel');
		
		//load file helper
		$this->load->helper('file');
		
		$this->Stylemodel->setId($id);
		$styleData = $this->Stylemodel->GetStyleData();
		
		if ($styleData && $this->Stylemodel->deleteStyle()) {
			// delete template files as well upon success.
			delete_files(FCPATH.'themes/'.$this->Stylemodel->getTempPath(), TRUE);
			
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Uninstalled Style");
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

			// validation ok
			$data = array(
			  'status' => 'success', 
			  'msg' =>  $this->lang->line('reordersuccess')
			);
		} else {
			// validation failed
			$data = array(
			  'status' => 'error', 
			  'msg' =>  $this->lang->line('uninstallstylefailure')
			);
		}

		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * newsletter form.
	 * @example index.php/ACP/general/newsletter
	*/
	public function newsletter() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'newsletter', array (
		  'NEWSLETTERFORM' => form_open('ACP/general/newsletterSubmit/', array('name' => 'frmNewsletter', 'id' => 'frmNewsletter', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_SUBJECT' => $this->lang->line('subject'),
		  "LANG_SENDNEWSLETTER" => $this->lang->line('sendnewsletter'),
		  "LANG_MSG" => $this->lang->line('message'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * send out newsletter.
	 * @example index.php/ACP/general/newsletterSubmit
	*/
	public function newsletterSubmit() {
		$data = $success = array();
		
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email', 'form_validation'));
        $this->load->helper('form');

		$this->form_validation->set_rules('subject', $this->lang->line('subject'), 'required|max_length[50]|xss_clean');
        $this->form_validation->set_rules('msg', $this->lang->line('message'), 'required|max_length[500]|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			$this->load->model('Usermodel');
			$emailList = $this->Usermodel->getEmailList();
			
			if (!$emailList) {
				$data = array(
				  'status' => 'error',
				  'msg' => $this->lang->line('failedloadingemaillist'),
				  'fields' => array(
					'subject' => form_error('subject'),
					'msg' => form_error('msg')
					)
				);
			} else {
				foreach ($emailList as $emailAddr) {
					//send out email.
					$this->email->to($emailAddr->Email);
					$this->email->from($this->preference->getPreferenceValue("board_email"), $this->title);
					$this->email->subject($this->input->post('subject', TRUE));
					$this->email->message($this->input->post('msg', TRUE));

					//send out email.
					if ($this->email->send()) {
						// mark as success email delivery.
						$success[] = $emailAddr->Username;
					} else {
						if ($this->config->item('debug_mode')) {
							log_message('error', $this->email->print_debugger()); //log error for debugging.
						}
						log_message('error', $emailAddr->Username.'<'.$emailAddr->Email.'> failed to receive message.');
					}
					$this->email->clear(); //reset email values for next record in loop.
				}
				
				//see if we were able to successfully send out the emails.
				if (count($success) > 0) {
					#log this into our audit system.
					$this->Cplogmodel->setUser($this->userID);
					$this->Cplogmodel->setAction("Sent out newsletter");
					$this->Cplogmodel->setDate(time());
					$this->Cplogmodel->setIp(detectProxy());
					$this->Cplogmodel->logAction();
					
					// validation ok
					$data = array(
					  'status' => 'success', 
					  'msg' =>  $this->lang->line('newslettersentsuccess'),
					  'fields' => array(
						'subject' => '',
						'msg' => ''
						)
					);
				} else {
					$data = array(
					  'status' => 'error',
					  'msg' => $this->lang->line('newslettersentfailure'),
					  'fields' => array(
						'subject' => form_error('subject'),
						'msg' => form_error('msg')
						)
					);
				}
			}
        } else {
            $data = array(
			  'status' => 'error',
			  'msg' => $this->lang->line('formfail'),
			  'fields' => array(
				'subject' => form_error('subject'),
				'msg' => form_error('msg')
				)
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * list spam words.
	 * @example index.php/ACP/general/spamlist
	*/
	public function spamlist() {
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("admincp"), '/ACP/');
		$this->breadcrumb->append_crumb($this->lang->line("spamlist"), '/ACP/general/spamlist');
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'spamlist', array (
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
			'LANG_SPAM_WORD' => $this->lang->line('spamword'),
			'LANG_ADD_SPAM_WORD' => $this->lang->line('addspamword'),
			'LANG_EDIT' => $this->lang->line('editspamword'),
			'LANG_DELETE' => $this->lang->line('deletespamword'),
			'LANG_CONFIRM_DELETE' => $this->lang->line('confirmdeletespamword')
		));
	}
	
	/**
	 * edit spam word.
	 * @example index.php/ACP/general/editspamword/5
	*/
	public function editspamword($id) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		//load Spamlistmodel
		$this->load->model('Spamlistmodel');
		
		$this->Spamlistmodel->setId($id);
		$spamData = $this->Spamlistmodel->getSpamData($id);
		
		if ($spamData) {
			// LOAD LIBRARIES
			$this->load->library(array('encrypt', 'form_validation'));
			$this->load->helper(array('form', 'form_select'));

			//render to HTML.
			echo $this->twig->render("acp/".strtolower(__CLASS__), 'editspamword', array (
			  'ACPEDITSPAMWORDFORM' => form_open('ACP/general/editspamwordSubmit/', array('name' => 'frmEditSpamWord', 'id' => 'frmEditSpamWord', 'class' => 'form')),
			  'ID' => $id,
			  'LANG_ERROR' => $this->lang->line('error'),
			  'LANG_SPAM_WORD' => $this->lang->line('spamword'),
			  'SPAM_WORD' => $this->Spamlistmodel->getSpamWord(),
			  'LANG_EDIT_SPAM_WORD' => $this->lang->line('editspamword'),
			  'LANG_FORMFAILURE' => $this->lang->line('formfail')
			));
		} else {
			show_error($this->lang->line('doesntexist'),404,$this->lang->line('error'));
		}		
	}
	
	/**
	 * process spam word edit form.
	 * @example index.php/ACP/general/editspamwordSubmit
	*/
	public function editspamwordSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$data = array();
		
		//load Spamlistmodel
		$this->load->model('Spamlistmodel');
		
		$this->Spamlistmodel->setId($this->input->post('id', TRUE));
		$spamData = $this->Spamlistmodel->getSpamData($this->input->post('id', TRUE));
		
		if ($spamData) {
			// LOAD LIBRARIES
			$this->load->library(array('encrypt', 'form_validation'));
			$this->load->helper('form');

			$this->form_validation->set_rules('spam_word', $this->lang->line('spamword'), 'required|max_length[255]|xss_clean');		
			$this->form_validation->set_error_delimiters('', '');

			//see if the form processed correctly without problems.
			if ($this->form_validation->run()) {
				$this->Spamlistmodel->setId($this->input->post('id', TRUE));
				$this->Spamlistmodel->setSpamWord($this->input->post('spam_word', TRUE));

				#update spam word.
				$this->Spamlistmodel->updateSpamEntry();

				#log this into our audit system.
				$this->Cplogmodel->setUser($this->userID);
				$this->Cplogmodel->setAction("Modified spam word: ".$this->input->post('spam_word', TRUE));
				$this->Cplogmodel->setDate(time());
				$this->Cplogmodel->setIp(detectProxy());
				$this->Cplogmodel->logAction();

				// validation ok
				$data = array(
						'status' => 'success', 
						'msg' =>  $this->lang->line('spamwordupdatesuccess'),
						'fields' => array(
							'spam_word' => ''
						)
				);
			} else {
				$data = array(
						'status' => 'error',
						'msg' => $this->lang->line('formfail'),
						'fields' => array(
							'spam_word' => form_error('spam_word')
						)
				);
			}
		} else {
            $data = array(
				'status' => 'error',
				'msg' => $this->lang->line('doesntexist'),
				'fields' => array(
					'spam_word' => form_error('spam_word')
				)
            );
		}
		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * add a new spam word.
	 * @example index.php/ACP/general/addspamword
	*/
	public function addspamword() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render("acp/".strtolower(__CLASS__), 'addspamword', array (
		  'ACPADDSPAMWORDFORM' => form_open('ACP/general/addspamwordSubmit/', array('name' => 'frmAddSpamWord', 'id' => 'frmAddSpamWord', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_SPAM_WORD' => $this->lang->line('spamword'),
		  'LANG_ADD_SPAM_WORD' => $this->lang->line('addspamword'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * process add new spam word.
	 * @example index.php/ACP/general/addspamwordSubmit
	*/
	public function addspamwordSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		$data = array();
		
		//load Spamlistmodel
		$this->load->model('Spamlistmodel');
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');

		$this->form_validation->set_rules('spam_word', $this->lang->line('spamword'), 'required|max_length[255]|xss_clean');		
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			$this->Spamlistmodel->setSpamWord($this->input->post('spam_word', TRUE));

			#add new spam word
			$this->Spamlistmodel->createSpamEntry();
			
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Added new spam word to list: ".$this->input->post('spam_word', TRUE));
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('spamwordaddsuccess'),
            		'fields' => array(
                    	'spam_word' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
                    	'spam_word' => form_error('spam_word')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * remove a spam word from list.
	 * @example index.php/ACP/general/deletespamword/5
	*/
	public function deletespamword($id) {
		$data = array();
		
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		//load Spamlistmodel
		$this->load->model('Spamlistmodel');
		
		$this->Spamlistmodel->setId($id);
		$spamData = $this->Spamlistmodel->getSpamData($id);
		
		if ($spamData && $this->Spamlistmodel->deleteSpamEntry()) {
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Deleted spam word from list: ".$this->Spamlistmodel->getSpamWord());
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();
			
			$data = array(
				'status' => 'success', 
				'msg' =>  $this->lang->line('spamworddeletesuccess')
			);
		} else {
			$data = array(
				'status' => 'error',
				'msg' => $this->lang->line('spamworddeletefailed')
			  );
		}
		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * censored word list.
	 * @example index.php/ACP/general/censor
	*/
	public function censor() {
		// add breadcrumbs
		$this->breadcrumb->append_crumb($this->title, '/');
		$this->breadcrumb->append_crumb($this->lang->line("admincp"), '/ACP/');
		$this->breadcrumb->append_crumb($this->lang->line("censor"), '/ACP/general/censor');
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'censor', array (
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
			'LANG_ORIGINAL_WORD' => $this->lang->line('originalword'),
			'LANG_ADD_CENSOR' => $this->lang->line('addcensor'),
			'LANG_EDIT' => $this->lang->line('editcensor'),
			'LANG_DELETE' => $this->lang->line('deletecensor'),
			'LANG_CONFIRM_DELETE' => $this->lang->line('confirmdeletecensorword')
		));
	}
	
	/**
	 * add new censored word.
	 * @example index.php/ACP/general/addcensor
	*/
	public function addcensor() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render("acp/".strtolower(__CLASS__), 'addcensor', array (
		  'ACPADDCENSORFORM' => form_open('ACP/general/addcensorSubmit/', array('name' => 'frmAddCensor', 'id' => 'frmAddCensor', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_ORIGINAL_WORD' => $this->lang->line('originalword'),
		  'LANG_ADD_CENSOR' => $this->lang->line('addcensor'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * process add censor form.
	 * @example index.php/ACP/general/addcensorSubmit
	*/
	public function addcensorSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		$data = array();
		
		//load Censormodel
		$this->load->model('Censormodel');
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');

		$this->form_validation->set_rules('original_word', $this->lang->line('originalword'), 'required|max_length[50]|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			$this->Censormodel->setOriginalWord($this->input->post('original_word', TRUE));

			#add new censored word.
			$this->Censormodel->createCensorEntry();
			
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Added new censored word to list: ".$this->input->post('original_word', TRUE));
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('censoraddsuccess'),
            		'fields' => array(
                    	'original_word' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
                    	'original_word' => form_error('original_word')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * edit an existing censored word.
	 * @example index.php/ACP/general/editcensor/5
	*/
	public function editcensor($id) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		//load Censormodel
		$this->load->model('Censormodel');
		
		$this->Censormodel->setId($id);
		$censorData = $this->Censormodel->getCensorData($id);
		
		if ($censorData) {
			// LOAD LIBRARIES
			$this->load->library(array('encrypt', 'form_validation'));
			$this->load->helper(array('form', 'form_select'));

			//render to HTML.
			echo $this->twig->render("acp/".strtolower(__CLASS__), 'editcensor', array (
			  'ACPEDITCENSORFORM' => form_open('ACP/general/editcensorSubmit/', array('name' => 'frmEditCensor', 'id' => 'frmEditCensor', 'class' => 'form')),
			  'ID' => $id,
			  'LANG_ERROR' => $this->lang->line('error'),
			  'LANG_ORIGINAL_WORD' => $this->lang->line('originalword'),
			  'ORIGINAL_WORD' => $this->Censormodel->getOriginalWord(),
			  'LANG_EDIT_CENSOR' => $this->lang->line('editcensor'),
			  'LANG_FORMFAILURE' => $this->lang->line('formfail')
			));
		} else {
			show_error($this->lang->line('doesntexist'),404,$this->lang->line('error'));
		}
	}
	
	/**
	 * process edit censor form.
	 * @example index.php/ACP/general/editcensorSubmit
	*/
	public function editcensorSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$data = array();
		
		//load Censormodel
		$this->load->model('Censormodel');
		
		$this->Censormodel->setId($this->input->post('id', TRUE));
		$censorData = $this->Censormodel->getCensorData($this->input->post('id', TRUE));
		
		if ($censorData) {
			// LOAD LIBRARIES
			$this->load->library(array('encrypt', 'form_validation'));
			$this->load->helper('form');

			$this->form_validation->set_rules('original_word', $this->lang->line('originalword'), 'required|max_length[50]|xss_clean');
			$this->form_validation->set_error_delimiters('', '');

			//see if the form processed correctly without problems.
			if ($this->form_validation->run()) {
				$this->Censormodel->setId($this->input->post('id', TRUE));
				$this->Censormodel->setOriginalWord($this->input->post('original_word', TRUE));

				#modify censored word.
				$this->Censormodel->updateCensorEntry();

				#log this into our audit system.
				$this->Cplogmodel->setUser($this->userID);
				$this->Cplogmodel->setAction("Modified censored word: ".$this->input->post('original_word', TRUE));
				$this->Cplogmodel->setDate(time());
				$this->Cplogmodel->setIp(detectProxy());
				$this->Cplogmodel->logAction();

				// validation ok
				$data = array(
						'status' => 'success', 
						'msg' =>  $this->lang->line('censorupdatesuccess'),
						'fields' => array(
							'original_word' => ''
						)
				);
			} else {
				$data = array(
						'status' => 'error',
						'msg' => $this->lang->line('formfail'),
						'fields' => array(
							'original_word' => form_error('original_word')
						)
				);
			}
		} else {
            $data = array(
				'status' => 'error',
				'msg' => $this->lang->line('doesntexist'),
				'fields' => array(
					'original_word' => form_error('original_word')
				)
            );
		}
		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * remove a censored word from list.
	 * @example index.php/ACP/general/deletecensor/5
	*/
	public function deletecensor($id) {
		$data = array();
		
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		//load Censormodel
		$this->load->model('Censormodel');
		
		$this->Censormodel->setId($id);
		$censorData = $this->Censormodel->getCensorData($id);
		
		if ($censorData && $this->Censormodel->deleteCensorEntry()) {
			#log this into our audit system.
			$this->Cplogmodel->setUser($this->userID);
			$this->Cplogmodel->setAction("Deleted censored word from list: ".$this->Censormodel->getOriginalWord());
			$this->Cplogmodel->setDate(time());
			$this->Cplogmodel->setIp(detectProxy());
			$this->Cplogmodel->logAction();
			
			$data = array(
				'status' => 'success', 
				'msg' =>  $this->lang->line('censordeletesuccess')
			);
		} else {
			$data = array(
				'status' => 'error',
				'msg' => $this->lang->line('censordeletefailed')
			  );
		}
		echo json_encode($data); //return results in JSON format.
	}
}