<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * loginmgr.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/04/2013
*/

class auth {
	/**
	 * declare data members
	*/
    
    /**
	 * Username to validate.
	 * @var string
	*/
    private $user;
    
    /**
	 * Password to validate.
	 * @var string
	*/
    private $pass;
    
    /**
	 * Codeigniter Object.
	 * @var Object
	*/
    private $ci;

    /**
	 * Setup common value.
	 * @version 03/04/12
	 * @param array $param parameter array.
	*/
	public function __construct($params) {
		//get CodeIgniter objects.
		$this->ci =& get_instance();	

		#define data member values.
		$this->user = $params['usr'];
		$this->pass = $params['pwd'];
	}

    /**
	 * Clean-up class after we're done.
	 * @version 02/26/12
	 * @access public
	*/
	public function __destruct(){
		unset($this->user);
		unset($this->lastActive);
		unset($this->loginKey);
		unset($this->adminActivity);
		unset($this->adminKey);
	}

    /**
	 * Performs a check through the database to ensure the requested username is valid.
	 * @version 07/25/12
	 * @return boolean
	 * @access private
	*/
	private function validateUser() {
	    #check against the database to see if the username and password match.
	    $validateUser = $this->ci->db->select('id')->from('ebb_users')->where('Username', $this->user)->limit(1)->count_all_results();
	    
		#setup boolean return.
		if($validateUser == 0) {
		    return FALSE;
		} else {
		    return TRUE;
		}
	}
	
    /**
	 * Performs a check through the database to ensure the requested password is valid.
	 * @version 07/25/12
	 * @return boolean
	 * @access private
	*/
	private function validatePwd() {
		#check against the database to see if the username and password match.
		$this->ci->db->select('Password')->from('ebb_users')->where('Username', $this->user)->limit(1);
		$query = $this->ci->db->get();
		$pwdFetch = $query->row();

		//see if that username is in the database, if not fail immediately!
		if($query->num_rows() == 0) {
			return FALSE;
		} else {
			//validate hash matches 100%.
			if (verifyHash($this->pass, $pwdFetch->Password) === true) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

    /**
	 * Performs a check through the database to ensure the requested information is valid.
	 * @version 07/25/12
	 * @return boolean
	 * @access public
	*/
	public function validateLogin() {
		#See if this is a guest account.
		if(($this->user == "guest") OR ($this->pass == "guest")) {
		    return FALSE;
		} else {
			#see if user entered the correct information.
			if($this->validateUser() && $this->validatePwd()) {
			    return TRUE;
			} else {
			    return FALSE;
			}
		}
	}

    /**
	 * Performs login process, creating any sessions or cookies needed for the system.
	 * @param boolean $remember keep user login info in tact?
	 * @version 07/28/12
	 * @access public
	*/
	public function logOn($remember){
	    
		//@TODO work on handling case when browser is closed before logging off.
		//@TODO work on persistent login handling.

		#set session to a secure status.
		//ini_get('session.cookie_secure',true);

		#setup variables.
		$loginKey = sha1(makeRandomPassword());
		$lastActive = time() + 300;
		
		#see if user wants to remain logged on.
		if($remember == FALSE) {
			#create a session.
			$this->ci->session->set_userdata('ebbUserID', $this->getUserID());
			$this->ci->session->set_userdata('ebbLastActive', $lastActive);
			$this->ci->session->set_userdata('ebbLoginKey', $loginKey);
		} else {
			#create cookie.
            $ebbUser = array(
                'name'   => 'ebbUserID',
                'value'  => $this->getUserID(),
                'expire' => '2592000',
                'domain' => '.'.$this->ci->config->item('cookie_domain'),
                'path'   => $this->ci->config->item('cookie_path'),
                'secure' => $this->ci->config->item('cookie_secure')
            );
            $this->ci->input->set_cookie($ebbUser);

            //login key data should be in session as it will always be changing,
            $this->ci->session->set_userdata('ebbLastActive', $lastActive);
			$this->ci->session->set_userdata('ebbLoginKey', $loginKey);
		}
				
		#remove user's IP from who's online list.
        $this->ci->db->delete('ebb_online', array('ip' => detectProxy()));

		#add login session in db.
		$data = array(
		  'username' => $this->getUserID(),
		  'last_active' => $lastActive,
		  'login_key' => $loginKey
		);
		$this->ci->db->insert('ebb_login_session', $data);
		$this->setLastActive();
	}
	
    /**
	 * Performs logout process, removing any sessions or cookies created from the system.
	 * @version 07/28/12
	 * @access public
	*/
	public function logOut(){

		#set session to a secure status.
		//ini_get('session.cookie_secure',true);

		#see if user is using cookies or sessions.
		if($this->input->cookie('ebbUser', TRUE) <> FALSE){
            #delete cookies.
            $ebbuser = array(
                'name'   => 'ebbUser',
                'value'  => $this->getUserID(),
                'expire' => '',
                'domain' => '.'.$this->ci->config->item('cookie_domain'),
                'path'   => $this->ci->config->item('cookie_path'),
                'secure' => $this->ci->config->item('cookie_secure')
            );
            $this->ci->input->set_cookie($ebbuser);

			#remove user from who's online list.
            $this->ci->db->delete('ebb_online', array('Username' => $this->getUserID()));
			
			#clear session data.
			$this->ci->session->sess_destroy();
		}else{
			#remove user from who's online list.
			$this->ci->db->delete('ebb_online', array('Username' => $this->getUserID()));

			#clear session data.
			$this->ci->session->sess_destroy();
		}
	}

    /**
	 * Checks to see if the user is verified as active or still waiting for activation.
	 * @version 07/28/12
	 * @access public
	 * @return boolean TRUE user is active; FALSE they aren't.
	*/
	public function isActive() {
		#see if we got a valid username first.
		if ($this->validateUser()) {
			#check against the database to see if the username and password match.
			$this->ci->db->select('active')->from('ebb_users')->where('Username', $this->user)->limit(1);
			$query = $this->ci->db->get();
			$validateStatus = $query->row();

			#setup bool. value to see if user is active or not.
			if($validateStatus->active == 0){
				return FALSE;
			}else{
				return TRUE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Get User's ID.
	 * @access public
	 * @version 07/28/12
	 * @return integer User ID.
	*/
	public function getUserID() {
		#see if we got a valid username first.
		if ($this->validateUser()) {
			#check against the database to see if the username and password match.
			$this->ci->db->select('id')->from('ebb_users')->where('Username', $this->user)->limit(1);
			$query = $this->ci->db->get();
			$fetchUserId = $query->row();

			return $fetchUserId->id;
		} else {
			return 0;
		}
	}

    /**
	 * disable user's active status.
	 * @version 07/28/12
	 * @access public
	 * @return boolean TRUE deactivated user; FALSE an error occurred.
	*/
	public function deactivateUser() {
		#see if we got a valid username first.
		if ($this->validateUser()) {
			$this->ci->db->where('Username', $this->user);
			$this->ci->db->update('ebb_users', array('active' => 0));
			return TRUE;
		} else {
			return FALSE;
		}
	}

    /**
	 * See how many times the user failed to login correctly.
	 * @version 07/28/12
	 * @access public
	 * @return integer the current incorrect login count.
	*/
	public function getFailedLoginCt() {
		#see if we got a valid username first.
		if ($this->validateUser()) {
			#get the count from the user table.
			$this->ci->db->select('failed_attempts')->from('ebb_users')->where('Username', $this->user)->limit(1);
			$query = $this->ci->db->get();
			$getFailedLoginCt = $query->row();

			return $getFailedLoginCt->failed_attempts;
		} else {
			return 0;
		}
	}
	
	/**
	 * set when the user is considered active.
	 * @return boolean TRUE the last visit value was update; FALSE, it was not.
	*/
	public function setLastActive() {
		#see if we got a valid username first.
		if ($this->validateUser()) {
			$this->ci->db->where('Username', $this->user);
			$this->ci->db->update('ebb_users', array('last_visit' => time()));
			return TRUE;
		} else {
			return FALSE;
		}
	}

    /**
	 * increment fail count for defined user.
	 * @version 07/28/12
	 * @access public
	 * @return boolean TRUE updated fail login count; FALSE an error occurred.
	*/
	public function setFailedLogin() {
		#see if we got a valid username first.
		if ($this->validateUser()) {
			#get new count.
			$newCount = $this->getFailedLoginCt();
			$incrementFailedCt = $newCount + 1;

			$this->ci->db->where('Username', $this->user);
			$this->ci->db->update('ebb_users', array('failed_attempts' => $incrementFailedCt));
			return TRUE;
		} else {
			return FALSE;
		}
	}

    /**
	 * Clears the failed count to 0.
	 * @version 07/28/12
	 * @access public
	 * @return boolean TRUE Failed login reset; FALSE an error occurred.
	*/
	public function clearFailedLogin() {
		#see if we got a valid username first.
		if ($this->validateUser()) {
			#clear count.
			$this->ci->db->where('Username', $this->user);
			$this->ci->db->update('ebb_users', array('failed_attempts' => 0));
			return TRUE;
		} else {
			return FALSE;
		}
	}
}