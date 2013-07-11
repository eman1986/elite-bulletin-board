<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * acpuser.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 07/10/2013
*/

class Acpuser extends EBB_Controller {

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
	
	public function user() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		// LOAD LIBRARIES
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->helper(array('boardindex','form', 'form_select'));
		
		//render to HTML.
		echo $this->twig->render('acp/'.strtolower(__CLASS__), 'user', array (
		  'INDEX_PAGE' => $this->config->item('index_page'),
          'BOARD_URL' => $this->boardUrl,
		  'LANG_USERMGR' => $this->lang->line('manageusers'),
		  'USERFORM' => form_open('ACP/acpuser/userSubmit/', array('name' => 'frmUserSearch', 'id' => 'frmUserSearch', 'class' => 'form')),
		  'LANG_ERROR' => $this->lang->line('error'),
		  'LANG_USERNAME' => $this->lang->line('username'),
		  'LANG_SEARCH' => $this->lang->line('search'),
		  'LANG_FORMFAILURE' => $this->lang->line('formfail')
		));
	}
	
	public function userSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		$data = array();
		
		//LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email', 'form_validation', 'user_agent'));
        $this->load->helper(array('form', 'user'));
		
		//setup validation rules.
        $this->form_validation->set_rules('userSearch', $this->lang->line('username'), 'required|xss_clean');
		$this->form_validation->set_error_delimiters('','');
		
		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			$this->load->model('Usermodel');
			$UserId = $this->Usermodel->getUIDFromUsername($this->input->post('userSearch', TRUE));
			
			if (!$UserId) {
				$data = array(
				  'status' => 'error',
				  'msg' => $this->lang->line('usernamenotexists'),
				  'fields' => array(
					'userSearch' => form_error('userSearch')
					)
				);
			} else {
				// validation ok
				$data = array(
				  'status' => 'success', 
				  'msg' =>  $UserId,
				  'fields' => array(
					'userSearch' => ''
					)
				);
			}
        } else {
            $data = array(
			  'status' => 'error',
			  'msg' => $this->lang->line('formfail'),
			  'fields' => array(
				'userSearch' => form_error('userSearch')
				)
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	public function manage($id) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}

		$this->load->model('Usermodel', 'acpUsr');
		$UserData = $this->acpUsr->getUser($id);

		if (!$UserData) {
			exit(show_error($this->lang->line('usernamenotexists'), 403, $this->lang->line('error')));
		} else {
			// LOAD LIBRARIES
			$this->load->library(array('encrypt', 'form_validation'));
			$this->load->helper(array('boardindex','form', 'form_select'));

			//render to HTML.
			echo $this->twig->render('acp/'.strtolower(__CLASS__), 'manage', array (
			  'INDEX_PAGE' => $this->config->item('index_page'),
			  'BOARD_URL' => $this->boardUrl,
			  'USERID' => $id,
			  'LANG_USERMGR' => $this->lang->line('manageusers'),
			  'USERMGRFORM' => form_open('ACP/acpuser/manageSubmit/', array('name' => 'frmUserMgr', 'id' => 'frmUserMgr', 'class' => 'form')),
			  'LANG_ERROR' => $this->lang->line('error'),
			  'LANG_USERNAME' => $this->lang->line('username'),
			  'USERNAME' => $this->acpUsr->getUserName(),
			  'LANG_EMAIL' => $this->lang->line('email'),
			  'EMAIL' => $this->acpUsr->getEmail(),
			  'LANG_CUSTOM_TITLE' => $this->lang->line('customtitle'),
			  'CUSTOM_TITLE' => $this->acpUsr->getCustomTitle(),
			  'LANG_TIME_ZONE' => $this->lang->line('customtitle'),
			  'TIME_ZONE_SELECT' => $this->acpUsr->getCustomTitle(),
			  'LANG_TIMEZONE' => $this->lang->line('timezone'),
			  'TIMEZONE_SELECT' => TimeZoneList($this->acpUsr->getTimeZone()),
			  'LANG_DATE_FORMAT' => $this->lang->line('dateformat'),
			  'DATE_FORMAT_SELECT' => dateFormatSelect($this->acpUsr->getDateFormat()),
			  'LANG_TIME_FORMAT' => $this->lang->line('timeformat'),
			  'TIME_FORMAT_SELECT' => timeFormatSelect($this->acpUsr->getTimeFormat()),
			  "LANG_WWW" => $this->lang->line('www'),
			  "WWW" => $this->acpUsr->getWWW(),
			  "LANG_MSN" => $this->lang->line("msn"),
			  "MSN" => $this->acpUsr->getMSn(),
			  "LANG_AOL" => $this->lang->line("aol"),
			  "AOL" => $this->acpUsr->getAol(),
			  "LANG_ICQ" => $this->lang->line("icq"),
			  "ICQ" => $this->acpUsr->getIcq(),
			  "LANG_YAHOO" => $this->lang->line('yim'),
			  "YAHOO" => $this->acpUsr->getYahoo(),
			  'LANG_SIG' => $this->lang->line('sig'),
			  'SIG' => $this->acpUsr->getSig(),
			  'LANG_ACTIVE_USER' => $this->lang->line('activeuser'),
			  'ACTIVE_USER' => $this->acpUsr->getActive(),
			  'LANG_YES' => $this->lang->line('yes'),
			  'LANG_NO' => $this->lang->line('no'),
			  'LANG_SAVESETTINGS' => $this->lang->line('savesettings'),
			  'LANG_DELETEUSER' => $this->lang->line('deluser'),
			  'LANG_CONFIRM_DELETE_USER' => $this->lang->line('confirmuserdelete'),
			  'LANG_FORMFAILURE' => $this->lang->line('formfail')
			));
		}
	}
	
	public function manageSubmit() {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		//LOAD LIBRARIES
        $this->load->library(array('encrypt', 'email', 'form_validation', 'user_agent'));
        $this->load->helper(array('form', 'user'));
		$this->load->model('Usermodel', 'acpUsr');

		//setup validation rules.
        $this->form_validation->set_rules('username', $this->lang->line('username'), 'required|alpha_numeric|xss_clean');
		$this->form_validation->set_rules('email', $this->lang->line('email'), 'required|valid_email|xss_clean');
		$this->form_validation->set_rules('user_sig', $this->lang->line('sig'), 'max_length[255]|callback_SpamFilter|xss_clean');
		$this->form_validation->set_error_delimiters('','');
		
		$data = array();
		
		//see if the form processed correctly without problems.
        if ($this->form_validation->run()) {
			$UserData = $this->acpUsr->getUser($this->input->post('userId', TRUE));
			
			if (!$UserData) {
				$data = array(
				  'status' => 'error',
				  'msg' => $this->lang->line('usernamenotexists'),
				  'fields' => array(
					'userSearch' => form_error('userSearch')
					)
				);
			} else {
				#set username field.
				$this->acpUsr->setId($this->input->post('userId', TRUE));

				$UpdateUserData = array(
				  'Username' => $this->input->post('username', TRUE),
				  'Email' => $this->input->post('email', TRUE),
				  'WWW' => $this->input->post('www', TRUE),
				  'MSN' => $this->input->post('msn', TRUE),
				  'AOL' => $this->input->post('aol', TRUE),
				  'Yahoo' => $this->input->post('yahoo', TRUE),
				  'ICQ' => $this->input->post('icq', TRUE),
				  'Sig' => $this->input->post('user_sig', TRUE),
				  'active' => $this->input->post('active_user', TRUE),
				  'Custom_Title' => $this->input->post('custom_title', TRUE)
				  );

				#update user.
				$this->acpUsr->UpdateUser($UpdateUserData);
				
				#log this into our audit system.
				$this->Cplogmodel->setUser($this->userID);
				$this->Cplogmodel->setAction("Updated User Information: ".$this->input->post('username', TRUE));
				$this->Cplogmodel->setDate(time());
				$this->Cplogmodel->setIp(detectProxy());
				$this->Cplogmodel->logAction();
				
				
				// validation ok
				$data = array(
				  'status' => 'success', 
				  'msg' =>  $this->lang->line('successacpuserupdate'),
				  'fields' => array(
					'username' => '',
					'email' => '',
					'www' => '',
					'msn' => '',
					'aol' => '',
					'icq' => '',
					'yahoo' => '',
					'active_user' => '',
					'custom_title' => '',
					'time_format' => '',
					'date_format' => '',
					'time_zone' => ''
					)
				);
			}
        } else {
			$this->firephp->log(validation_errors()); //@TODO find out why its breaking here.
			
            $data = array(
			  'status' => 'error',
			  'msg' => $this->lang->line('formfail'),
			  'fields' => array(
				'username' => form_error('username'),
				'email' => form_error('email'),
				'www' => form_error('www'),
				'msn' => form_error('msn'),
				'aol' => form_error('aol'),
				'icq' => form_error('icq'),
				'yahoo' => form_error('yahoo'),
				'active_user' => form_error('active_user'),
				'custom_title' => form_error('custom_title'),
				'time_format' => form_error('time_format'),
				'date_format' => form_error('date_format'),
				'time_zone' => form_error('time_zone')
				)
            );
        }        
        echo json_encode($data); //return results in JSON format.
	}
	
	public function deleteuser($id) {
		if (!IS_AJAX) {
			exit(show_error($this->lang->line('ajaxerror'), 403, $this->lang->line('error')));
		}
		
		$data = array();
		
		//Load user model
		$this->load->model('Usermodel', 'acpUsr');
		
		$UserData = $this->acpUsr->getUser($id);

		if (!$UserData) {
			$data = array(
			  'status' => 'error',
			  'msg' => $this->lang->line('usernamenotexists'),
			  'fields' => array(
				'userSearch' => form_error('userSearch')
				)
			);
		} else {
			#delete user.
			$deleted = $this->acpUsr->DeleteUser();
			
			if ($deleted) {
				#log this into our audit system.
				$this->Cplogmodel->setUser($this->userID);
				$this->Cplogmodel->setAction("Deleted a user");
				$this->Cplogmodel->setDate(time());
				$this->Cplogmodel->setIp(detectProxy());
				$this->Cplogmodel->logAction();

				// validation ok
				$data = array(
				  'status' => 'success', 
				  'msg' =>  $this->lang->line('successacpuserdelete')
				);
			} else {
				// validation ok
				$data = array(
				  'status' => 'error', 
				  'msg' =>  $this->lang->line('failureacpuserupdate')
				);
			}
		}
		echo json_encode($data); //return results in JSON format.	
	}
	
}
