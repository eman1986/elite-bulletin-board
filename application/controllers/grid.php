<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * grid.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/16/2013
*/

/**
 * Grid Controller
 * @abstract EBB_Controller 
 */
class Grid extends EBB_Controller {

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
	 * Get a list of users.
	 * @example index.php/grid/memberlist
	*/
	public function memberlist() {
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Usermodel->countAll();
		$jTableResult['Records'] = $this->Usermodel->ListAll($this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}
	
	/**
	 * Get a list of groups.
	 * @example index.php/grid/listgroups
	*/
	public function listgroups() {
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Groupmodel->countAll();
		$jTableResult['Records'] = $this->Groupmodel->ListAll($this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}
	
	/**
	 * Get a list of attachments.
	 * @example index.php/grid/listattachments
	*/
	public function listattachments() {
		//Attachmentsmodel
		$this->load->model('Attachmentsmodel');
		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Attachmentsmodel->countAll($this->userID);
		$jTableResult['Records'] = $this->Attachmentsmodel->ListAllByUser($this->userID,$this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}

	/**
	 * Get a list of subscriptions.
	 * @example index.php/grid/listsubscriptions
	*/
	public function listsubscriptions() {
		//load Topicmodel
		$this->load->model('Topicmodel');
		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Topicmodel->countAllSubscriptionsByUser($this->userID);
		$jTableResult['Records'] = $this->Topicmodel->ListAllSubscriptionsByUser($this->userID,$this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}
	
	/**
	 * Get a list of pm messages.
	 * @example index.php/grid/listmessages/inbox
	*/	
	public function listmessages($folder) {
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Pmmodel->countAllMessagesByUser($this->userID, $folder);
		$jTableResult['Records'] = $this->Pmmodel->getPMMessagesByUserID($this->userID, $this->timeZone, $this->dateTimeFormat, $folder, $this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);		
	}

	/**
	 * Get a list of ACP audits.
	 * @example index.php/grid/listacpaudit
	*/
	public function listacpaudit() {
		//load Pmmodel
		$this->load->model('Cplogmodel');
		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Cplogmodel->countAll();
		$jTableResult['Records'] = $this->Cplogmodel->listAll($this->timeZone, $this->dateTimeFormat, $this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}
	
	/**
	 * Get a list of installed styles.
	 * @example index.php/grid/liststyles
	*/
	public function liststyles() {
		//load Stylemodel
		$this->load->model('Stylemodel');
		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Stylemodel->countAll();
		$jTableResult['Records'] = $this->Stylemodel->listAll($this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}
	
	/**
	 * Get a list of spam words.
	 * @example index.php/grid/listspamwords
	*/
	public function listspamwords() {
		//load Spamlistmodel
		$this->load->model('Spamlistmodel');
		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Spamlistmodel->countAll();
		$jTableResult['Records'] = $this->Spamlistmodel->listAll($this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}
	
	/**
	 * Get a list of censor words.
	 * @example index.php/grid/listcensorwords
	*/
	public function listcensorwords() {
		//load Censormodel
		$this->load->model('Censormodel');
		
		//Return result to jTable
		$jTableResult = array();
		$jTableResult['Result'] = "OK";
		$jTableResult['TotalRecordCount'] = $this->Censormodel->countAll();
		$jTableResult['Records'] = $this->Censormodel->listAll($this->input->get('jtSorting', TRUE), $this->input->get('jtPageSize', TRUE), $this->input->get('jtStartIndex', TRUE));
		print json_encode($jTableResult);
	}
	
}