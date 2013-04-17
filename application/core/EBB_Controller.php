<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * EBB_Controller.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/15/2013
*/

class EBB_Controller extends CI_Controller {

	/**
	 * define data member.
	*/

	/**
	 * Current Logged In user.
	 * @var string
	*/
	public $logged_user;
	
	/**
	 * Current User's ID.
	 * @var integer
	*/
	public $userID;

	/**
	 *  User's Group Access Level.
	 * @var intege
	*/
	public $groupAccess;

	/**
	 * User's GroupID.
	 * @var int
	*/
	public $gid;

	/**
	 * User's Style selection.
	 * @var integer
	*/
	public $style;

	/**
	 * Time format.
	 * @var string
	*/
	public $timeFormat;
	
	/**
	 * Date format.
	 * @var string
	*/
	public $dateFormat;
	
	/**
	 * The format for PHP date().
	 * @var string
	*/
	public $dateTimeFormat;

	/**
	 * Time Zone
	 * @var string
	*/
	public $timeZone;

	/**
	 * Language
	 * @var string
	*/
	public $lng;

	/**
	 * Board Title
	 * @var string
	*/
	public $title;
	
	/**
	 * Board URL
	 * @var string 
	*/
	public $boardUrl;
	
	/**
	 * Length of User's Suspension.
	 * @var integer
	*/
	public $suspend_length;
	
	/**
	 * Date user got suspended..
	 * @var integer
	*/
	public $suspend_time;
	
	/**
	 * The type of notification being thrown.
	 * @var string
	*/
	public $notifyType;
	
	/**
	 * The notification message to output to user.
	 * @var string
	*/
	public $notifyMsg;
	
	/**
	 * Style List Array.
	 * @var array 
	*/
	public $styleList = array();
	
	/**
	 * The count of new PM messages.
	 * @var integer
	*/
	public $newPMCount = 0;

	/**
	 * Loads global data.
	 * @access Public
	 * @version 08/30/12
	*/
	public function __construct() {

		parent::__construct();
		
		//see if debug mode is enabled.
		if ($this->config->item('debug_mode')) {
			$this->firephp->setEnabled(true);
		} else {
			$this->firephp->setEnabled(false);
		}
		
		#grab any notification messages.
		$this->notifyType = $this->session->flashdata('NotifyType');
		$this->notifyMsg = $this->session->flashdata('NotifyMsg');

		//delete any online data from the last 3 minutes.
		$this->db->delete('ebb_online', array('time <' => SESSION_TIMEOUT));

		#login setup
		if ($this->session->userdata('ebbUserID') != FALSE) {

			//see if user is logged in via cookies.
			if ($this->input->cookie('ebbUserID', TRUE) != FALSE) {
				$ebbuserid = $this->input->cookie('ebbUserID', TRUE);
			} elseif ($this->session->userdata('ebbUserID') != FALSE) {
				$ebbuserid = $this->session->userdata('ebbUserID');
			} else {
				exit(show_error($this->lang->line('invalidlogin'), 500, $this->lang->line('error')));
			} //END authenication check.

			$params = array(
			  'user' => $ebbuserid
			);
			$this->load->library('user', $params);

			//validate login session
			if ($this->user->validateLoginSession($this->session->userdata('ebbLastActive'), $this->session->userdata('ebbLoginKey'), 0)) {

				//load user model.
				$this->load->model(array('Usermodel','Stylemodel', 'Pmmodel'));

				$userEntity = $this->Usermodel->getUser($ebbuserid);

				if ($userEntity) {
					//setup logged in user.
					$this->gid = $this->Usermodel->getGid();
					$this->logged_user = $this->Usermodel->getUserName();
					$this->userID = $this->Usermodel->getId();
					$this->style = $this->Usermodel->getStyle();
					$this->timeFormat = $this->Usermodel->getTimeFormat();
					$this->dateFormat = $this->Usermodel->getDateFormat();
					$this->dateTimeFormat = getDateTimeFormat($this->dateFormat, $this->timeFormat); //@todo replace old usage with this one.
					$this->timeZone = $this->Usermodel->getTimeZone();
					$this->lng = $this->Usermodel->getLanguage();
					$this->suspend_length = $this->Usermodel->getSuspendLength();
					$this->suspend_time = $this->Usermodel->getSuspendTime();
					$this->styleList = $this->Stylemodel->GetStyles();
					$this->newPMCount = Pmmodel::getNewMessageCount($this->userID);

					//get group data.
					$groupData = $this->Groupmodel->GetGroupData($this->gid);
					
					if ($groupData) {
						#see if user is marked as banned.
						if($this->Groupmodel->getPermissionType() == 6){
							exit(show_error($this->lang->line('banned')));
						}

						//detect group status.
						$this->groupAccess = $this->Groupmodel->getLevel();
					} else {
						show_error($this->lang->line('invalidgid'), 500, $this->lang->line('error'));
					}

					//see if a user is either suspended or banned.
					//checkBan();

					//update user's onhline status.
					update_whosonline_users($this->userID);
				} else {
					show_error($this->lang->line('invaliduser'), 500, $this->lang->line('error'));
				}
			} else {
				//session is invalid, log user out and clear session data.
				$this->db->where('username', $ebbuserid);
				$this->db->delete('ebb_login_session');
				
				//clear online status session.
				$this->db->delete('ebb_online', array('Username' => $this->logged_user));
				
				#clear session data.				
				$this->session->unset_userdata('ebbUserID');
				$this->session->unset_userdata('ebbLastActive');
				$this->session->unset_userdata('ebbLoginKey');

				//handle time out based on how user is accessing their session.
				if (IS_AJAX) {
					echo '<script type="text/javascript">window.location.reload()</script>'; //reload page so server-side can run.
				} else {
					#set message to output to user.
					$this->session->set_flashdata('NotifyType', 'warning');
					$this->session->set_flashdata('NotifyMsg', $this->lang->line('expiredsess'));
					redirect('/login/Login', 'location'); //session expired.
				}
			}
		} else {
			//guest account.
			$this->logged_user = "guest";
			$this->userID = 0;
			$this->Groupmodel->IsGuest = TRUE;
			$this->groupAccess = 0;
			$this->groupProfile = 0;
			$this->gid = 0;
			$this->style = $this->preference->getPreferenceValue("default_style");
			$this->timeFormat = $this->preference->getPreferenceValue("timeformat");
			$this->dateFormat = $this->preference->getPreferenceValue("dateformat");
			$this->dateTimeFormat = getDateTimeFormat($this->dateFormat, $this->timeFormat);
			$this->timeZone = $this->preference->getPreferenceValue("timezone");
			$this->lng = $this->preference->getPreferenceValue("default_language");
			
			//keep guest session in tact.
			update_whosonline_guest();

		} //END login session check.

		//language setup.
		$this->lang->load('ebb', $this->lng);

		//load up global settings.
		$this->title = $this->preference->getPreferenceValue("board_name");
		$this->boardUrl = $this->config->item('base_url');

	}
	
	/**
	 * GLOBAL FUNCTIONS 
	 */
	
	/**
	 * setup notification messages.
	 * @param string $type the type of message being broadcasted.
	 * @param string $msg the message to broadcast to user.
	 * @version 06/17/12
	 */
	public function notifications($type, $msg) {
		$this->session->set_flashdata('NotifyType', $type);
		$this->session->set_flashdata('NotifyMsg', $msg);
	}
	
	/**
	 * Get the name of the style.
	 * @return string|boolean the name of the style or FALSE if theme is not found.
	 * @version 07/30/12
	*/
	public function getStyleName() {
		#get the style template path from the db.
		$this->db->select('Temp_Path')->from('ebb_style')->where('id', $this->style);
		$styleQ = $this->db->get();

		if($styleQ->num_rows() > 0) {
			$theme = $styleQ->row();
			return $theme->Temp_Path;
		} else {
			show_error('Invalid Style Selected.<hr />File:'.__FILE__.'<br />Line:'.__LINE__, 500, $this->lang->line('error'));
			return FALSE;
		}
	}
	
	/**
	 * CI FORM VALIDATION METHODS.
	*/
	
	/**
	 * Validate the URL entered is valid.
	 * @param string $str URL to validate against.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	*/
	public function validateUrl($str) {
		if (!empty($str) && preg_match('/(http|https):\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/',$str)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('validateUrl', $this->lang->line('invalidurl'));
			return FALSE;
		}
	}

	/**
	 * Validates The current email of the logged in user.
	 * @param string $str the value we're validating.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	*/
	public function validateCurrentEmail($str) {
		if ($str <> $this->Usermodel->getEmail()) {
			$this->form_validation->set_message('validateCurrentEmail', $this->lang->line('noemailmatch'));
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/**
	 * Validate the user getting this hasn't blocked the requested sender.
	 * @param string $str the value we're validating.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	*/
	public function validatePMSender($str) {
		$this->load->model('Relationshipmodel');
		if ($this->Relationshipmodel->IsBannedByUser($this->userID, $str)) {
			$this->form_validation->set_message('validatePMSender', $this->lang->line('blocked'));
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/**
	 * Validates CAPTCHA.
	 * @param string $str the value we're validating.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	*/
	public function ValidateCaptcha($str) {

		if (sha1($str) <> $this->session->userdata("CAPTCHA_Ans")) {
			$this->form_validation->set_message('ValidateCaptcha', $this->lang->line('captchanomatch'));
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Validate Email.
	 * @param string $str the form value under validation.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	 */
	public function ValidateEmail($str) {
		
		#Level 1 - see if the MX record is valid.
		if (($this->preference->getPreferenceValue('mx_check') == 1) and (checkdnsrr(array_pop(explode("@",$str)),"MX"))) {
			#Level 2 - validate email isn't blacklisted.
			$checkDomain = explode("@", $str);
			$this->db->select('ban_email')->from('ebb_banlist_email')->where('ban_wildcard', 1)->like('ban_email', $checkDomain)->or_where('ban_email', $str);
			if ($this->db->count_all_results() == 0) {
				#Level 3 - ensure this email isn't already in use.
				$this->db->select('Email')->from('ebb_users')->where('Email', $str);
				if ($this->db->count_all_results() == 0) {
					return TRUE;
				} else {
					$this->form_validation->set_message('ValidateEmail', $this->lang->line('emailexist'));
					return FALSE;
				}
			} else {
				$this->form_validation->set_message('ValidateEmail', $this->lang->line('emailban'));
				return FALSE;	
			}
		} else {
			$this->form_validation->set_message('ValidateEmail', $this->lang->line('invalidemail'));
			return FALSE;
		}
		
	}

	/**
	 * Validates the username is not banned or in use.
	 * @param string $str the value we're validating
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	 */
	public function ValidateUserName($str) {
		#Level 1 - Validiate username isn't banned.
		$this->db->select('ban_user')
		  ->from('ebb_banlist_user')
		  ->where('ban_wildcard', 1)
		  ->like('ban_user', $str)
		  ->or_where('ban_user', $str);
		
		if ($this->db->count_all_results() == 0) {
			#Level 2 - validate the usename isn't already in use.
			$this->db->select('Username')->from('ebb_users')->where('Username', $str);
			if ($this->db->count_all_results() == 0) {
				return TRUE;
			} else {
				$this->form_validation->set_message('ValidateUserName', $this->lang->line('usernameexist'));
			return FALSE;
			}
		} else {
			$this->form_validation->set_message('ValidateUserName', $this->lang->line('usernameblacklisted'));
			return FALSE;
		}
	}
	
	/**
	 * Validate the info entered on the password recovery form.
	 * @param string $str The value from the form.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	*/
	public function ValidateAccount($str) {
		
		$this->db->select('id')
		  ->from('ebb_users')
		  ->where('Username', $str)
		  ->or_where('Email', $str)
		  ->limit(1);
		
		if ($this->db->count_all_results() == 0) {
			$this->form_validation->set_message('ValidateAccount', $this->lang->line('invalidrecoveryinfo'));
			return FALSE;
		} else {
			return TRUE;
		}

	}
	
	/**
	 * Validate that no spam is detected.
	 * @param string $str The value from the form.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	*/
	public function SpamFilter($str) {
		$this->load->model('Spamlistmodel');
		
		//see if anything is listed as spam.
		if ($this->Spamlistmodel->searchSpamList($str)) {
			$this->form_validation->set_message('SpamFilter', $this->lang->line('spamwarn'));
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/**
	 * Validate the poll options meet the criteria.
	 * @param string $str The value from the form.
	 * @return boolean TRUE, validation passes; FALSE, validation failed.
	 */
	public function PollOptionValidation($str) {
		$pollOptions = explode(PHP_EOL, $str);
		
		//see if at least 2 options are listed.
		if (count($pollOptions) < 2) {
			$this->form_validation->set_message('PollOptionValidation', $this->lang->line('moreoption'));
			return FALSE;
		} else {
			for ($i = 0; $i <= count($pollOptions)-1; $i++) {
				//make sure this exceeds the 50 character max limit.
				if (strlen($pollOptions[$i]) > 50) {
					$this->form_validation->set_message('PollOptionValidation', $this->lang->line('longpoll'));
					return FALSE;
					break;
				} elseif (strlen($pollOptions[$i]) < 2) {
					$this->form_validation->set_message('PollOptionValidation', $this->lang->line('shortpoll'));
					return FALSE;
					break;
				}
				
				//only spaces, numbers, and alpha characters are allowed.
				if(!preg_match("/^[a-zA-Z0-9 ]+$/", $pollOptions[$i])) {
					$this->form_validation->set_message('PollOptionValidation', $this->lang->line('invalidpolloptions'));
					return FALSE;
					break;
				}
			}
			return TRUE;
		}
	}
}//END Class.