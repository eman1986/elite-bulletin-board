<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * hook_handler.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 12/22/2012
*/
class hook_handler {
	
	private $ci;

    public function __construct() {
        $this->ci = get_instance();
    }
	
	
	/**
	 * Looks to see if board is disabled.
	*/
	public function boardStatus() {
		if ($this->ci->preference->getPreferenceValue("board_status") == 0) {
			show_error($this->ci->preference->getPreferenceValue('offline_msg'), 200, $this->ci->lang->line('info'));
		}
		
	}
	
	/**
	 * Detect if we need to run the installer.
	*/
	public function detectInstaller() {
		if (!$this->ci->config->item('installed')) {
			redirect('/install', 'location'); //redirect user to login form.
		}
	}
	
}