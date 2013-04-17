<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * ucpform.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/17/2013
*/
class Ucpform extends EBB_Controller {

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
	 * update email form.
	 * @example index.php/ucpform/email
	*/
	public function email() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'email', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'UCPEMAILFORM' => form_open('ucpform/emailSubmit/', array('name' => 'frmUpdateEmail', 'id' => 'frmUpdateEmail', 'class' => 'form')),
		  'LANG_CURREMAIL' => $this->lang->line('currentemail'),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_NEWEMAIL' => $this->lang->line('newemail'),
		  'LANG_CONFIRMEMAIL' => $this->lang->line('confirmemail'),
		  'LANG_UPDATEEMAIL' => $this->lang->line('updateemail'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * submits update email form data to server.
	 * @example index.php/ucpform/emailSubmit
	*/
	public function emailSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
		$this->load->model('Usermodel');
        $this->load->helper('form');

		$this->form_validation->set_rules('curemail', $this->lang->line('currentemail'), 'required|valid_email|callback_validateCurrentEmail|xss_clean');
        $this->form_validation->set_rules('newemail', $this->lang->line('newemail'), 'required|valid_email|xss_clean');
        $this->form_validation->set_rules('conemail', $this->lang->line('confirmemail'), 'required|valid_email|matches[newemail]|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			#set username field.
			$this->Usermodel->setId($this->userID);

			$Updatedata = array(
			  'Email' => $this->input->post('newemail', TRUE)
			  );

			#update user.
			$this->Usermodel->UpdateUser($Updatedata);

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('updatedemailsuccess'),
            		'fields' => array(
                    	'curemail' => '',
                    	'newemail' => '',
                    	'conemail' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
                    	'curemail' => form_error('curemail'),
                    	'newemail' => form_error('newemail'),
                    	'conemail' => form_error('conemail')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * change password form.
	 * @example index.php/ucpform/password
	*/
	public function password() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'password', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'UCPPASSWORDFORM' => form_open('ucpform/passwordSubmit/', array('name' => 'frmUpdatePassword', 'id' => 'frmUpdatePassword', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_CURRPASS' => $this->lang->line('currentpass'),
		  'LANG_NEWPASS' => $this->lang->line('newpass'),
		  'LANG_CONFIRMPASS' => $this->lang->line('connewpass'),
		  'LANG_UPDATEPASS' => $this->lang->line('updatepass'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * submits update password form data to server.
	 * @example index.php/ucpform/passwordSubmit
	*/
	public function passwordSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');

		$this->form_validation->set_rules('curpass', $this->lang->line('currentemail'), 'required|xss_clean');
        $this->form_validation->set_rules('newpass', $this->lang->line('newemail'), 'required|xss_clean');
        $this->form_validation->set_rules('confirmpass', $this->lang->line('confirmemail'), 'required|matches[newpass]|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			#set username field.
			$this->Usermodel->setId($this->userID);
			
			//create blowfish hash.
			$newPwdHash = makeHash($this->input->post('newpass', TRUE));

			$Updatedata = array(
			  'Password' => $newPwdHash
			  );

			#update user.
			$this->Usermodel->UpdateUser($Updatedata);

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('updatedpasswdsuccess'),
            		'fields' => array(
                    	'curpass' => '',
                    	'newpass' => '',
                    	'confirmpass' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
                    	'curpass' => form_error('curpass'),
                    	'newpass' => form_error('newpass'),
                    	'confirmpass' => form_error('confirmpass')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}

	/**
	 * get a list of attachments to the logged in user..
	 * @example index.php/ucpform/attachments
	*/
	public function attachments() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'attachments', array (
		  'BOARD_URL' => $this->boardUrl,
		  'LOGGEDUSERID' => $this->userID,
		  'THEME_NAME' => $this->getStyleName(),
		  'PER_PAGE' => $this->preference->getPreferenceValue("per_page"),
		  "LANG_MANAGEATTACHMENTS" => $this->lang->line('manageattach'),
		  'LANG_FILENAME' => $this->lang->line('filename'),
		  'LANG_FILESIZE' => $this->lang->line('filesize'),
		  'LANG_TOPIC' => $this->lang->line('postedin'),
		  'LANG_DELETEFILE' => $this->lang->line('delattach'),
		  'LANG_YES' => $this->lang->line('yes'),
		  'LANG_NO' => $this->lang->line('no')
		));
	}
	
	/**
	 * Deletes the selected attachment record.
	 * @example index.php/ucpform/deleteattachment
	*/
	public function deleteattachment($id) {
		//Attachmentsmodel
		$this->load->model('Attachmentsmodel');
		$deleted = $this->Attachmentsmodel->DeleteAttachment((int)$id);
		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = ($deleted) ? "OK" : "ERROR";
		
		if (!$deleted) {
			$jTableResult['Message'] = $this->lang->line('cantdelete');
		}
		
		print json_encode($jTableResult);
	}
	
	/**
	 * List subscriptions.
	 * @example index.php/ucpform/subscriptions
	*/
	public function subscriptions() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper('form');
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'subscriptions', array (
		  'BOARD_URL' => $this->boardUrl,
		  'LOGGEDUSERID' => $this->userID,
		  'THEME_NAME' => $this->getStyleName(),
		  'PER_PAGE' => $this->preference->getPreferenceValue("per_page"),
		  "LANG_MANAGESUBSCRIPTION" => $this->lang->line('subscriptionsetting'),
		  'LANG_FILENAME' => $this->lang->line('filename'),
		  'LANG_FILESIZE' => $this->lang->line('filesize'),
		  'LANG_TOPIC' => $this->lang->line('scription'),
		  'LANG_BOARD' => $this->lang->line('postedin'),
		  'LANG_UNSUBSCRIBE' => $this->lang->line('delsubscription'),
		  'LANG_CONFIRMUNSUBSCRIBE' => $this->lang->line('confirmUnsubscribe'),
		  'LANG_YES' => $this->lang->line('yes'),
		  'LANG_NO' => $this->lang->line('no')
		));
	}
	
	/**
	 * Deletes the selected subscription.
	 * @example index.php/ucpform/deletesubscription/5
	*/
	public function deletesubscription($id) {
		// LOAD LIBRARIES
		$this->load->helper('user');
		
		subscriptionManager($this->userID, $id, "unsubscribe");
		$unsubscribed = TRUE; //todo this will get rebuild later

		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = ($unsubscribed) ? "OK" : "ERROR";
		
		if (!$deleted) {
			$jTableResult['Message'] = $this->lang->line('cantdelete');
		}
		
		print json_encode($jTableResult);
	}
	
	/**
	 * Form to modify user's profile.
	 * @example index.php/ucpform/profile
	*/
	public function profile() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'profile', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'UCPPROFILEFORM' => form_open('ucpform/profileSubmit/', array('name' => 'frmUpdateProfile', 'id' => 'frmUpdateProfile', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_CUSTOMTITLE' => $this->lang->line('customtitle'),
		  'CUSTOMTITLE' => $this->Usermodel->getCustomTitle(),
		  'ALLOWCTITLE' => $this->Groupmodel->validateAccess(1, 30),
		  "LANG_WWW" => $this->lang->line('www'),
		  "WWW" => $this->Usermodel->getWWW(),
		  "LANG_LOCATION" => $this->lang->line('location'),
		  "LOCATION" => $this->Usermodel->getLocation(),
		  'LANG_EDITPROFILE' => $this->lang->line('editprofile'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail'),
		  "LANG_INVALIDURL" => $this->lang->line('invalidurl')
		));
	}

	/**
	 * submits profile form data to server.
	 * @example index.php/ucpform/profileSubmit
	*/
	public function profileSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
		$this->load->model('Usermodel');
        $this->load->helper('form');

		$this->form_validation->set_rules('www', $this->lang->line('www'), 'max_length[200]|callback_validateUrl|xss_clean');
		$this->form_validation->set_rules('ctitle', $this->lang->line('customtitle'), 'max_length[20]|xss_clean');
        $this->form_validation->set_rules('loc', $this->lang->line('location'), 'max_length[70]|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			#set username field.
			$this->Usermodel->setId($this->userID);

			$Updatedata = array(
			  'WWW' => $this->input->post('www', TRUE),
			  'Custom_Title' => $this->input->post('ctitle', TRUE),
			  'Location' => $this->input->post('loc', TRUE)
			  );

			#update user.
			$this->Usermodel->UpdateUser($Updatedata);

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('usrprofilesuccess'),
            		'fields' => array(
                    	'www' => '',
                    	'ctitle' => '',
                    	'loc' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
                    	'www' => form_error('www'),
                    	'ctitle' => form_error('ctitle'),
                    	'loc' => form_error('loc')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * Form to modify User Settings.
	 * @example index.php/ucpform/settings
	*/
	public function settings() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('form', 'form_select'));

		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'settings', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'UCPUSETTINGSFORM' => form_open('ucpform/settingsSubmit/', array('name' => 'frmUpdateUSettings', 'id' => 'frmUpdateUSettings', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_PMNOTIFY' => $this->lang->line('pm_notify'),
		  'PMNOTIFY' => $this->Usermodel->getPmNotify(),
		  'LANG_SHOWEMAIL' => $this->lang->line('showemail'),
		  'SHOWEMAIL' => $this->Usermodel->getHideEmail(),
		  'LANG_TIMEFORMAT' => $this->lang->line('timeformat'),
		  'TIMEFORMAT' => timeFormatSelect($this->timeFormat),
		  'LANG_DATEFORMAT' => $this->lang->line('dateformat'),
		  'DATEFORMAT' => dateFormatSelect($this->dateFormat),
		  'LANG_YES' => $this->lang->line('yes'),
		  'LANG_NO' => $this->lang->line('no'),
		  'LANG_SAVESETTINGS' => $this->lang->line('savesettings'),
		  'LANG_TIME' => $this->lang->line('timezone'),
		  'TimeZone' => TimeZoneList($this->timeZone),
		  'LANG_LANGUAGE' => $this->lang->line('defaultlang'),
		  'LANGUAGE' => LanguageList($this->lng),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * Process User Settings.
	 * @example index.php/ucpform/settingsSubmit
	*/
	public function settingsSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
		$this->load->model('Usermodel');
        $this->load->helper('form');

		$this->form_validation->set_rules('language', $this->lang->line('nolang'), 'required|xss_clean');
		$this->form_validation->set_rules('pm_notice', $this->lang->line('pm_notify'), 'required|xss_clean');
        $this->form_validation->set_rules('show_email', $this->lang->line('showemail'), 'required|xss_clean');
		$this->form_validation->set_rules('time_format', $this->lang->line('timeformat'), 'required|xss_clean');
		$this->form_validation->set_rules('time_zone', $this->lang->line('timezone'), 'required|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			#set username field.
			$this->Usermodel->setId($this->userID);

			$Updatedata = array(
			  'PM_Notify' => $this->input->post('pm_notice', TRUE),
			  'Hide_Email' => $this->input->post('show_email', TRUE),
			  'date_format' => $this->input->post('date_format', TRUE),
			  'Time_format' => $this->input->post('time_format', TRUE),
			  'Time_Zone' => $this->input->post('time_zone', TRUE),
			  'Language' => $this->input->post('language', TRUE)
			  );

			#update user.
			$this->Usermodel->UpdateUser($Updatedata);

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('usrsettingssuccess'),
            		'fields' => array(
					  'pm_notice' => '',
					  'show_email' => '',
					  'date_format' => '',
					  'time_format' => '',
					  'time_zone' => '',
					  'language' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
					  'pm_notice' => form_error('pm_notice'),
					  'show_email' => form_error('show_email'),
					  'date_format' => form_error('date_format'),
					  'time_format' => form_error('time_format'),
					  'time_zone' => form_error('time_zone'),
					  'language' => form_error('language')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * Modify Messenger Info.
	 * @example index.php/ucpform/messenger
	*/
	public function messenger() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'messenger', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'UCPMESSENGERFORM' => form_open('ucpform/messengerSubmit/', array('name' => 'frmMessenger', 'id' => 'frmMessenger', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  "LANG_MSN" => $this->lang->line("msn"),
		  "MSN" => $this->Usermodel->getMSn(),
		  "LANG_AOL" => $this->lang->line("aol"),
		  "AOL" => $this->Usermodel->getAol(),
		  "LANG_ICQ" => $this->lang->line("icq"),
		  "ICQ" => $this->Usermodel->getIcq(),
		  "LANG_YAHOO" => $this->lang->line('yim'),
		  "YAHOO" => $this->Usermodel->getYahoo(),
		  "LANG_SAVESETTINGS" => $this->lang->line('savesettings'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * Process Messenger Info.
	 * @example index.php/ucpform/messengerSubmit
	*/
	public function messengerSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
		$this->load->model('Usermodel');
        $this->load->helper('form');

		$this->form_validation->set_rules('msn', $this->lang->line('msn'), 'valid_email|xss_clean');
        $this->form_validation->set_rules('icq', $this->lang->line('icq'), 'numeric|xss_clean');
		$this->form_validation->set_rules('yim', $this->lang->line('yim'), 'valid_email|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			#set username field.
			$this->Usermodel->setId($this->userID);

			$Updatedata = array(
			  'MSN' => $this->input->post('msn', TRUE),
			  'AOL' => $this->input->post('aol', TRUE),
			  'Yahoo' => $this->input->post('yim', TRUE),
			  'ICQ' => $this->input->post('icq', TRUE)
			  );

			#update user.
			$this->Usermodel->UpdateUser($Updatedata);

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('usrmessengersuccess'),
            		'fields' => array(
					  'msn' => '',
					  'aol' => '',
					  'yim' => '',
					  'icq' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
					  'msn' => form_error('msn'),
					  'aol' => form_error('aol'),
					  'yim' => form_error('yim'),
					  'icq' => form_error('icq')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * Signature Form.
	 * @example index.php/ucpform/signature
	*/
	public function signature() {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		$this->twig->_twig_env->addFunction('FormatMsg', new Twig_Function_Function('FormatTopicBody'));
		$this->twig->_twig_env->addFunction('CensorFilter', new Twig_Function_Function('censorFilter'));
		echo $this->twig->render(strtolower(__CLASS__), 'signature', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'UCPUSETTINGSFORM' => form_open('ucpform/signatureSubmit/', array('name' => 'frmSignature', 'id' => 'frmSignature', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  "LANG_SMILES" => $this->lang->line("moresmiles"),
		  'LANG_CURRENTSIG' => $this->lang->line("cursig"),
		  'CURRENTSIG' => $this->Usermodel->getSig(),
		  "SMILES" => form_smiles(),
		  'LANG_SIG' => $this->lang->line('sig'),
		  "LANG_SAVESETTINGS" => $this->lang->line('savesettings'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * Process Signature.
	 * @example index.php/ucpform/signatureSubmit
	*/
	public function signatureSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
		$this->load->model('Usermodel');
        $this->load->helper('form');

		$this->form_validation->set_rules('signature', $this->lang->line('sig'), 'max_length[255]|callback_SpamFilter|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			#set username field.
			$this->Usermodel->setId($this->userID);

			$Updatedata = array(
			  'Sig' => $this->input->post('signature', TRUE)
			  );

			#update user.
			$this->Usermodel->UpdateUser($Updatedata);

            // validation ok
            $data = array(
            		'status' => 'success', 
            		'msg' =>  $this->lang->line('usrsigsuccess'),
            		'fields' => array(
					  'signature' => ''
                    )
    		);
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
					  'signature' => form_error('signature')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * Delete the defined pm message.
	 * @example index.php/ucpform/deletepmmessage/5
	*/
	public function deletepmmessage($id) {
		//make sure the message belongs to the logged in user first.
		if ($this->Pmmodel->IsPMOwner($this->userID)) {
			$this->Pmmodel->setId($id);
			$this->Pmmodel->deleteMessage();
			
            $data = array(
				'status' => 'success',
				'msg' => $this->lang->line('delpmsuccessfully')
            );			
		} else {
            $data = array(
				'status' => 'error',
				'msg' => $this->lang->line('pmaccessdenied')
            );
		}
		echo json_encode($data); //return results in JSON format.
	}

	/**
	 * Archives the defined pm message.
	 * @example index.php/ucpform/archivepmmessage/5
	*/
	public function archivepmmessage($id) {
		//make sure the message belongs to the logged in user first.
		if ($this->Pmmodel->IsPMOwner($this->userID)) {
			
			//see if user your sending to is over their quota.
			if (Pmmodel::QuotaCheck('Archive', $this->userID)) {
				$data = array(
					'status' => 'error',
					'msg' => $this->lang->line('folderfull')
				);
			} else {
				$this->Pmmodel->setId($id);
				$this->Pmmodel->archiveMessage();

				$data = array(
					'status' => 'success',
					'msg' => $this->lang->line('archpmsuccessfully')
				);
			}
		} else {
            $data = array(
				'status' => 'error',
				'msg' => $this->lang->line('pmaccessdenied')
            );
		}
		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * New PM Form.
	 * @example index.php/ucpform/newpm/5
	*/
	public function newpm($sendto=null) {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		$this->twig->_twig_env->addFunction('FormatMsg', new Twig_Function_Function('FormatTopicBody'));
		echo $this->twig->render(strtolower(__CLASS__), 'newpm', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'NEWPMFORM' => form_open('ucpform/newpmSubmit/', array('name' => 'frmNewPM', 'id' => 'frmNewPM', 'class' => 'form')),
		  "LANG_SMILES" => $this->lang->line("moresmiles"),
		  'LANG_ERROR' => $this->lang->line('error'),
		  "SMILES" => form_smiles(),
		  'LANG_TO' => $this->lang->line('send'),
		  "TO" => is_null($sendto) ? "" : $this->Usermodel->getUsernameFromUID($sendto),
		  'LANG_SUBJECT' => $this->lang->line('subject'),
		  "LANG_SENDPM" => $this->lang->line('sendpm'),
		  "LANG_PMMSG" => $this->lang->line('pmmsg'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	public function newpmSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email', 'form_validation'));
        $this->load->helper('form');

		$this->form_validation->set_rules('subject', $this->lang->line('subject'), 'required|max_length[50]|xss_clean');
		$this->form_validation->set_rules('sendto', $this->lang->line('send'), 'required|alpha_numeric|max_length[25]|callback_validatePMSender|xss_clean');
		$this->form_validation->set_rules('pmMsg', $this->lang->line('pmmsg'), 'required|max_length[255]|callback_SpamFilter|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			
			//convert username to user id (if valid).
			$senderUID = $this->Usermodel->getUIDFromUsername($this->input->post('sendto', TRUE));
			if (!$senderUID) {
				$data = array(
						'status' => 'error',
						'msg' => $this->lang->line('invaliduser'),
						'fields' => array(
							  'subject' => '',
							  'sendto' => '',
							  'pmMsg' => ''
						)
				);
			} else {
				//see if user your sending to is over their quota.
				if (!Pmmodel::QuotaCheck('Inbox', $senderUID)) {
					$data = array(
							'status' => 'error',
							'msg' => $this->lang->line('overquota'),
							'fields' => array(
							  'subject' => '',
							  'sendto' => '',
							  'pmMsg' => ''
							)
					);
				} else {
					#set field values.
					$this->Pmmodel->setSubject($this->input->post('subject', TRUE));
					$this->Pmmodel->setSender($this->userID);
					$this->Pmmodel->setReceiver($senderUID);
					$this->Pmmodel->setMessage($this->input->post('pmMsg', TRUE));

					#create pm message.
					$pmId = $this->Pmmodel->CreateMessage();
					
					//get some basic settings to determine if user receiving pm wants to get notified.
					$pmSettings = $this->Usermodel->getPMNotifyData($senderUID);
					
					//see if user wants to be notified about a new PM.
					if ($pmSettings['notify'] == 1) {
						//send out email.        	
						$this->email->to($pmSettings['email']);
						$this->email->from($this->preference->getPreferenceValue("board_email"), $this->title);
						$this->email->subject($this->lang->line('pmsubject'));
						$this->email->message($this->twig->renderNoStyle('/emails/'.$pmSettings['lang'].'/eml_pm_notify.twig', array(
							'PM_RECEIVER' => $pmSettings['username'],
							'PM_SENDER' => $this->logged_user,						  
							'PM_SUBJECT' => $this->input->post('subject', TRUE),
							'BOARDADDR' => $this->boardUrl,
							'INDEX_PAGE' => $this->config->item('index_page'),
							'PM_ID' => $pmId
						)));

						//send out email.
						$this->email->send();
					}

					// validation ok
					$data = array(
							'status' => 'success', 
							'msg' =>  $this->lang->line('sentpmsuccessfully'),
							'fields' => array(
							  'subject' => '',
							  'sendto' => '',
							  'pmMsg' => ''
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
					  'sendto' => form_error('sendto'),
					  'pmMsg' => form_error('pmMsg')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}

	/**
	 * Reply PM Form.
	 * @example index.php/ucpform/replypm
	 * @todo look into a reply quote feature.
	*/
	public function replypm($id) {
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
		} else {
				$this->notifications('error', $this->lang->line('pm404'));
				redirect('/', 'location');
		}
		
		#see if a RE: was already added, if so don't add another RE:
		if($this->Pmmodel->getSubject() == "RE:&nbsp;".$this->Pmmodel->getSubject()){
			$subject = $this->Pmmodel->getSubject();
		}else{
			$subject = "RE:&nbsp;".$this->Pmmodel->getSubject(); 
		}
		
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		$this->twig->_twig_env->addFunction('FormatMsg', new Twig_Function_Function('FormatTopicBody'));
		echo $this->twig->render(strtolower(__CLASS__), 'replypm', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'NEWPMFORM' => form_open('ucpform/replypmSubmit/', array('name' => 'frmReplyPM', 'id' => 'frmReplyPM', 'class' => 'form')),
		  "LANG_SMILES" => $this->lang->line("moresmiles"),
		  'LANG_ERROR' => $this->lang->line('error'),
		  "SMILES" => form_smiles(),
		  'SUBJECT' => $subject,
		  'RECEIVER' => $this->Pmmodel->getSender(),
		  "LANG_SENDPM" => $this->lang->line('sendpm'),
		  "LANG_PMMSG" => $this->lang->line('pmmsg'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	/**
	 * Process Reply PM Form.
	 * @example index.php/ucpform/replypmSubmit
	*/
	public function replypmSubmit() {
		$data = array();
	
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email', 'form_validation'));
        $this->load->helper('form');

		$this->form_validation->set_rules('subject', $this->lang->line('subject'), 'required|max_length[50]|xss_clean');
		$this->form_validation->set_rules('sendto', $this->lang->line('send'), 'required|alpha_numeric|max_length[25]|callback_validatePMSender|xss_clean');
		$this->form_validation->set_rules('pmMsg', $this->lang->line('pmmsg'), 'required|max_length[255]|callback_SpamFilter|xss_clean');
        $this->form_validation->set_error_delimiters('', '');

		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			
			//convert username to user id (if valid).
			$senderUID = $this->Usermodel->getUIDFromUsername($this->input->post('sendto', TRUE));
			if (!$senderUID) {
				$data = array(
						'status' => 'error',
						'msg' => $this->lang->line('invaliduser'),
						'fields' => array(
						  'pmMsg' => ''
						)
				);
			} else {
				//see if user your sending to is over their quota.
				if (Pmmodel::QuotaCheck('Inbox', $senderUID)) {
					$data = array(
							'status' => 'error',
							'msg' => $this->lang->line('overquota'),
							'fields' => array(
							  'pmMsg' => ''
							)
					);
				} else {
					#set field values.
					$this->Pmmodel->setSubject($this->input->post('subject', TRUE));
					$this->Pmmodel->setReceiver($this->userID);
					$this->Pmmodel->setSender($senderUID);
					$this->Pmmodel->setMessage($this->input->post('pmMsg', TRUE));

					#create pm message.
					$pmId = $this->Pmmodel->CreateMessage();
					
					//get some basic settings to determine if user receiving pm wants to get notified.
					$pmSettings = $this->Usermodel->getPMNotifyData($senderUID);
					
					//see if user wants to be notified about a new PM.
					if ($pmSettings['notify'] == 1) {
						//send out email.        	
						$this->email->to($pmSettings['email']);
						$this->email->from($this->preference->getPreferenceValue("board_email"), $this->title);
						$this->email->subject($this->lang->line('pmsubject'));
						$this->email->message($this->twig->renderNoStyle('/emails/'.$pmSettings['lang'].'/eml_pm_notify.twig', array(
							'PM_RECEIVER' => $pmSettings['username'],
							'PM_SENDER' => $this->logged_user,						  
							'PM_SUBJECT' => $this->input->post('subject', TRUE),
							'BOARDADDR' => $this->boardUrl,
							'INDEX_PAGE' => $this->config->item('index_page'),
							'PM_ID' => $pmId
						)));

						//send out email.
						$this->email->send();
					}

					// validation ok
					$data = array(
							'status' => 'success', 
							'msg' =>  $this->lang->line('sentpmsuccessfully'),
							'fields' => array(
							  'pmMsg' => ''
							)
					);
				}
			}
        } else {
            $data = array(
            		'status' => 'error',
            		'msg' => $this->lang->line('formfail'),
            		'fields' => array(
					  'pmMsg' => form_error('pmMsg')
                    )
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * Process friendship request.
	 * @example index.php/ucpform/requestfriendship/4
	*/
	public function requestfriendship($id) {
		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email'));
		$this->load->model('Relationshipmodel');
		
		$this->Relationshipmodel->setUsername($id);
		$this->Relationshipmodel->setUId($this->userID);
		$this->Relationshipmodel->setStatus(0);
		$rid = $this->Relationshipmodel->CreateRelationship();
		
		//get some basic info about user.
		$usrBasics = $this->Usermodel->getPMNotifyData($id);

		//send out email.        	
		$this->email->to($usrBasics['email']);
		$this->email->from($this->preference->getPreferenceValue("board_email"), $this->title);
		$this->email->subject($this->lang->line('emlFriendReqSubject'));
		$this->email->message($this->twig->renderNoStyle('/emails/'.$usrBasics['lang'].'/eml_friendship_approval.twig', array(
			'USERNAME' => $this->logged_user,
			'FRIEND' => $usrBasics['username'],
			'BOARDADDR' => $this->boardUrl,
			'INDEX_PAGE' => $this->config->item('index_page'),
			'RID' => $rid
		)));

		//send out email.
		$this->email->send();
		
		$data = array(
		  'status' => 'success', 
		  'msg' =>  $this->lang->line('friendrequestsuccess')
		);
		echo json_encode($data); //return results in JSON format.
	}
	
	/**
	 * Process block request.
	 * @example index.php/ucpform/blockuser/4
	*/
	public function blockuser($id) {
		// LOAD LIBRARIES
		$this->load->model('Relationshipmodel');
		
		$this->Relationshipmodel->setUsername($id);
		$this->Relationshipmodel->setUId($this->userID);
		$this->Relationshipmodel->setStatus(2);
		$rid = $this->Relationshipmodel->CreateRelationship();
		
		$data = array(
		  'status' => 'success', 
		  'msg' =>  $this->lang->line('blockusersuccess')
		);
		echo json_encode($data); //return results in JSON format.
	}
}