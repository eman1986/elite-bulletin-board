<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * user.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 11/14/2012
*/

class user{

	#define data member.
	
	/**
	 * Username in session.
	 * @var string
	*/
	private $user;
	
	/**
	 * CodeIgniter object.
	 * @var object
	*/
	private $ci;
	
    /**
	 * Setup User class structure.
	 * @param string $params our parameters for this object.
	 * @version 07/28/12
	*/
	public function __construct($params){
		
		//setup CodeIgniter instance.
		$this->ci =& get_instance();
		
		$this->user = trim($params['user']);
		
		#run a validation on the username entered.
		if($this->checkUsername() == false){
			exit(show_error($this->ci->lang->line('invaliduser').'<hr />File:'.__FILE__.'<br />Line:'.__LINE__, 500, $this->ci->lang->line('error')));
			log_message('error', 'invalid username was provided: '.$this->user); //log error in error log.
		}
	}

    /**
	 * Clear User data member.
	 * @version 09/28/09
	*/
	public function __destruct(){
  		unset($this->user);
	}
	
	/**
	 * see if username entered is valid.
	 * @version 07/28/12
	 * @return boolean
	 * @access private
	*/
	private function checkUsername(){
	    #check against the database to see if the username match.
		$this->ci->db->select('id')->from('ebb_users')->where('id', $this->user)->limit(1);
		$validateStatus = $this->ci->db->count_all_results();

		#setup bool. value to see if user is active or not.
		if($validateStatus == 0){
		    return(false);
		}else{
		    return(true);
		}
	}

	/**
	 * Validate Login Key is valid.
	 * @version 03/04/12
	 * @param string $key encrypted key hash being validated.
	 * @param integer $type 0=login key;1=admin key
	 * @return boolean
	 */
	private function ValidateLoginKey($key, $type) {
		#see what type of keyt we're validating.
		if ($type == 0) {
			#check against the database to see if the username match.
			$this->ci->db->select('login_key')->from('ebb_login_session')->where('username', $this->user)->where('login_key', $key)->limit(1);
			$validateKey = $this->ci->db->count_all_results();
		} elseif ($type == 1) {
			#check against the database to see if the username match.
			$this->ci->db->select('admin_key')->from('ebb_login_session')->where('username', $this->user)->where('admin_key', $key)->limit(1);
			$validateKey = $this->ci->db->count_all_results();
		} else {
			return false;
		}

		#setup bool. value to see if user is active or not.
		if($validateKey == 0){
		    return(false);
		}else{
		    return(true);
		}
	}

	/**
	 * Validates current login session.
	 * @access Public
	 * @version 04/11/12
	 * @return bool
	*/
	public function validateLoginSession($lastActive, $loginKey, $keyType) {
		#Level 1, validate username.
		if ($this->checkUsername()) {
			#Level 2, validate activity & key.
			if ((time() - $lastActive < 300) AND ($this->ValidateLoginKey($loginKey, $keyType))) { //5 minutes
				#Level 3, update activity value and regenerate key.
				$new_loginKey = sha1(makeRandomPassword());
				$new_lastActive = time() + 300;

				#set new values in session.
				$this->ci->session->set_userdata('ebbLastActive', $new_lastActive);
				$this->ci->session->set_userdata('ebbLoginKey', $new_loginKey);

				#add new values to database.
				$data = array(
				  'last_active' => $new_lastActive,
				  'login_key' => $new_loginKey
				);
				$this->ci->db->where('username',$this->user);
				$this->ci->db->update('ebb_login_session', $data);

				return (true); //session is valid
			} else {
				return (false); //session is invalid
			}
		} else {
			return (false); //session is invalid
		}
	}
}
