<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * upload.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 12/19/2012
*/

/**
 * Handler of uploaded files.
 */
class Upload extends EBB_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url', 'attachment'));
	}

	/**
	 * Index Page for this controller.
	 * @example index.php/upload/
	 * @example index.php/upload/index/
	*/	
	public function index() {
		//load attachments model.
		$this->load->model(array('Attachmentsmodel'));
		
		//render to HTML.
		echo $this->twig->render(strtolower(__CLASS__), 'attachmentmanager', array (
		  'BOARD_URL' => $this->boardUrl,
		  'THEME_NAME' => $this->getStyleName(),
		  'LANG_FILENAME' =>  $this->lang->line('filename'),
		  'LANG_FILESIZE' => $this->lang->line('filesize'),
		  'LANG_FILETYPE' => $this->lang->line('filetype'),
		  'LANG_NOFILES' => $this->lang->line('noattachments'),
		  'FILES' => $this->Attachmentsmodel->GetAttachments($this->logged_user)
		));
	}
	
	/**
	 * Uploads file(s) to web server.
	 * @example index.php/upload/do_upload/
	*/	
	public function do_upload() {
		
		//load attachments model.
		$this->load->model(array('Attachmentsmodel'));

		//setup uploader class.
		$config = array();
		$config['upload_path'] = $this->config->item('upload_path');
		$config['allowed_types'] = BuildAllowedExtensionList();
		$config['max_size'] = $this->preference->getPreferenceValue("attachment_quota");
		$config['max_filename'] = 100;
		$config['remove_spaces'] = TRUE; //to make web-ready.

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload()) {
			echo json_encode(array('status' => 'error', 'msg' => $this->upload->display_errors()));
		} else {
			$data = $this->upload->data();
			
			//encrypt the filename to prevent sniffing.
			$fileNameSalt = CreateAttachmentSalt();
			$encryptedFileName = sha1($data['file_name'].$fileNameSalt);
			rename($data['full_path'], $this->config->item('upload_path').$encryptedFileName);
			
			//setup attachment in db.
			$this->Attachmentsmodel->setUserName($this->logged_user);
			$this->Attachmentsmodel->setTiD(0); //at this stage, this is not important
			$this->Attachmentsmodel->setPiD(0); //at this stage, this is not important
			$this->Attachmentsmodel->setFileName($data['file_name']);
			$this->Attachmentsmodel->setEncryptedFileName($encryptedFileName);
			$this->Attachmentsmodel->setEncryptionSalt($fileNameSalt);
			$this->Attachmentsmodel->setFileType($data['file_type']);
			$this->Attachmentsmodel->setFileSize($data['file_size']);
			$this->Attachmentsmodel->setDownloadCount(0);
			$this->Attachmentsmodel->CreateAttachment();

			//set the data for the json array
			echo json_encode(array('status' => 'success', 'msg' => $data['file_name'].'&nbsp;'.$this->lang->line('fileuploaded')));
		}

	}

	/**
	 * Deletes a file from the server.
	 * @example index.php/upload/delete/5
	*/	
	public function delete($id) {
		//load attachments model.
		$this->load->model(array('Attachmentsmodel'));
		
		$success = $this->Attachmentsmodel->DeleteAttachment($id);
		
		//see if the file successfully deleted.
		if ($success) {
			//remove entry from db.
			$this->db->where('Filename', $id);
			$this->db->delete('ebb_attachments');

			echo json_encode(array('status' => 'success', 'msg' => $this->lang->line('fdeleteok')));
		} else {
			echo json_encode(array('status' => 'error', 'msg' => $this->lang->line('cantdelete')));
		}

	}
} //END Class