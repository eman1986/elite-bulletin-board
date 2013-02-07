<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * user_helper.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 11/11/2012
*/

/**
 * Get Avatar from Gravatar Service.
 * @param string $eml The email to look up on gravatar.
 * @param string $size Setup the size of the avatar.
 * @param string $dImage Setup a default image.
 * @param string $rating Restrict the type of avatar to allow.
 * @param boolean $secure Force Gravatar to use SSL?
 * @return string URL to return an image.
 * @version 10/10/12
*/
function getGravatar($eml, $size = "medium", $dImage = "gravatar", $rating = "ignore", $secure = false) {
	//base URL.
	$gravatarUrl = (!$secure) ? 'http://www.gravatar.com/avatar/' : 'https://www.gravatar.com/avatar/';
	
	//availables sizes (gravatar allows 1-2048px, a preset of sizes is set to prevent abuse).
	$availableSizes = array(
	  "tiny" => "16",
	  "small" => "32",
	  "medium" => "48",
	  "large" => "64",
	  "huge" => "128"
	);
	
	//available options.
	$defaultImage = array(
	  "gravatar" => "",
	  "404" => "404",
	  "mystery-man" => "mm",
	  "patterns" => "identicon",
	  "monsters" => "monsterid",
	  "faces" => "wavatar",
	  "8bit" => "retro"
	);
	
	//the ratings filter.
	$ratings = array(
	  "ignore" => "", //ignores the ratings rule.
	  "all" => "g",
	  "general" => "pg",
	  "adult" => "r",
	  "sexual" => "x"
	);
	
	//setup default value if incorrect value entered.
	if (!array_key_exists($size, $availableSizes)) {
		$size = "medium";
	}
	if (!array_key_exists($dImage, $defaultImage)) {
		$dImage = "gravatar";
	}
	if (!array_key_exists($rating, $ratings)) {
		$rating = "ignore";
	}
	
	//setup query string.
	if ($rating != "ignore") {
		$qsRating = 'r='.$ratings[$rating];
	} else {
		$qsRating = '';
	}
	if ($dImage == "gravatar") {
		$qsDefault = "d=";
	} else {
		$qsDefault = "d=".$defaultImage[$dImage];
	}
	$qsSize = 's='.$availableSizes[$size];

	return $gravatarUrl.md5(strtolower(trim($eml))).'?'.$qsRating.'&'.$qsSize.'&'.$qsDefault;
}

/**
 * Displays a list of users & guest currently online.
 * @return string
*/
function whosonline() {

	#obtain codeigniter object.
	$ci =& get_instance();
	
	$online = '';
	
	$ci->db->select('u.id, u.Username')
	  ->from('ebb_online o')
	  ->join('ebb_users u', 'o.Username=u.id', 'LEFT')
	  ->where('o.ip IS NULL');
	$onlineLogged = $ci->db->get();
	foreach ($onlineLogged->result() as $row) {
	    #gain status of users.
		$ci->load->model('Groupmodel', 'groupsonline');
		$ci->load->model('Usermodel', 'usersonline');
		$ci->usersonline->getUser($row->id);
		$ci->groupsonline->GetGroupData($ci->usersonline->getGid());

		if ($ci->groupsonline->getLevel() == 1){
			$online .=  '<strong>'.anchor('viewprofile/'.$row->id, $row->Username).'</strong>&nbsp;';
		}elseif ($ci->groupsonline->getLevel() == 2){
			$online .=  '<em>'.anchor('viewprofile/'.$row->id, $row->Username).'</em>&nbsp;';
		}elseif($ci->groupsonline->getLevel() == 3){
			$online .=  anchor('viewprofile/'.$row->id, $row->Username).'&nbsp;';
		}else{
			$online .= '&nbsp;';
		}
	}
	return ($online);
}

/**
 * Used to create a completely random password.
 * @return string
 * @version 7/24/11
*/
function makeRandomPassword() {
	$salt = "abchefghjkmnpqrstuvwxyz0123456789";
	$pass = "";
	srand((double)microtime()*1000000);
  	$i = 0;
  	while ($i <= 7) {
		$num = rand() % 33;
		$tmp = substr($salt, $num, 1);
		$pass = $pass . $tmp;
		$i++;
  	}
  	return $pass;
}

/**
 * Used to create a random-generated password salt.
 * @return string and encrypted string
*/
function makeSalt() {
    static $seed = "./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    $algo = (PHP_VERSION_ID  >= 50307) ? '$2y' : '$2a'; //DON'T CHANGE THIS!!
    $strength = '$12'; //too high will time out script.
    $salt = '$';
    for ($i = 0; $i < 22; $i++) {
        $salt .= substr($seed, mt_rand(0, 63), 1);
    }
    return $algo . $strength . $salt;
}

/**
 * Blowfish encrypt user's password.
 * @param string $password
 * @return string
 * @version 02/20/12
 */
function makeHash($password) {
    return crypt($password, makeSalt());
}

/**
 * Verify the hash encryption is valid.
 * @param string $password
 * @param string $hash
 * @return boolean
 * @version 02/20/12
 */
function verifyHash($password, $hash) {
    return $hash == crypt($password, $hash);
}

/**
 * Used to update the whos online system.
 * @param string $user The username we want to update.
 * @version 11/1/11
*/
function update_whosonline_users($user){

	#obtain codeigniter object.
	$ci =& get_instance();
	
	$ci->db->select('id')->from('ebb_online')->where('Username', $user);
	
	//see if we add or update online status.
	if ($ci->db->count_all_results() == 0){
		#setup values.
		$data = array(
		   'Username' => $user,
		   'time' => time(),
		   'location' => $_SERVER['PHP_SELF']);

		#add new preference.
		$ci->db->insert('ebb_online', $data);
	}else{
		//user is still here so lets up their time to let the script know the user is still around.
		$data = array(
		   'time' => time(),
		   'location' => $_SERVER['PHP_SELF']);
		
		$ci->db->where('Username', $user);
		$ci->db->update('ebb_online', $data);
	}
}

/**
 * Used to update the whos online system.
 * @version 11/16/11
*/
function update_whosonline_guest(){

	#obtain codeigniter object.
	$ci =& get_instance();
	
	$ip = detectProxy();

	$ci->db->select('ip')->from('ebb_online')->where('ip', $ip);

	//see if we add or update online status.
	if ($ci->db->count_all_results() == 0){
		#setup values.
		$data = array(
		   'ip' => $ip,
		   'time' => time(),
		   'location' => $_SERVER['PHP_SELF']);

		#add new preference.
		$ci->db->insert('ebb_online', $data);
	}else{
		//user is still here so lets up their time to let the script know the user is still around.
		$data = array(
		   'time' => time(),
		   'location' => $_SERVER['PHP_SELF']);
		
		$ci->db->where('ip', $ip);
		$ci->db->update('ebb_online', $data);
	}
}

/**
 * Flood check.
 * @param string $user User we're updating.
 * @version 05/29/12
*/
function update_user($user){
	
	#obtain codeigniter object.
	$ci =& get_instance();

	//update user's last post.
	$data = array(
		'last_post' => time());
		
	$ci->db->where('Username', $user);
	$ci->db->update('ebb_users', $data);
}

/**
 * Sniffs out any proxy and displays actual IP.
 * @return string
 * @version 4/19/10
*/
function detectProxy(){

	$ip_sources = array(
		"HTTP_X_FORWARDED_FOR",
		"HTTP_X_FORWARDED",
		"HTTP_FORWARDED_FOR",
		"HTTP_FORWARDED",
		"HTTP_X_COMING_FROM",
		"HTTP_COMING_FROM",
		"REMOTE_ADDR"
	);
	
	#loop through array.
	foreach ($ip_sources as $ip_source){
		#If the ip source exists, capture it
		if (isset($_SERVER[$ip_source])){
			$proxy_ip = $_SERVER[$ip_source];
			break;
		}
	}
	
	#if all else fails, just set a false value.
	$proxy_ip = (isset($proxy_ip)) ? $proxy_ip : $_SERVER["REMOTE_ADDR"];

	return ($proxy_ip);
}

/**
 * Subscribe or unsubscribe from a topic.
 * @param string $user the user we want to this to affect.
 * @param integer $tid the Topic ID we want this to affect.
 * @param string $mode are we subscribing or unsubscribing?
 * @version 09/22/12
 */
function subscriptionManager($user, $tid, $mode) {

	#obtain codeigniter object.
	$ci =& get_instance();
	
	//get a check to see if they are a part the topic defined.
	$ci->db->select('tid')
	  ->from('ebb_topic_watch')
	  ->where('username', $user);
	$q = $ci->db->get();
	
	//see if they want to subscribe or unsubscribe to a topic.
	if ($mode == "subscribe" && $q->num_rows() == 0) {
		$data = array(
		  "username" => $user,
		  "tid" => $tid,
		  "read_status" => 0
		);
		$ci->db->insert('ebb_topic_watch', $data);		
	} elseif ($mode == "unsubscribe" && $q->num_rows() > 0) {
		$ci->db->where('tid', $tid);
		$ci->db->where('username', $user);
		$ci->db->delete('ebb_topic_watch');
	}
}

/**
 * Checks to see if the user is banned or suspended.
 * @version 05/10/12
*/
function checkBan(){

	#obtain codeigniter object.
	$ci =& get_instance();

	#see if user is suspended.
	if($ci->suspend_length > 0){
		#see if user is still suspended.
		$math = 3600 * $ci->suspend_length;
		$suspend_date = $ci->suspend_time + $math;
		$today = time() - $math;

		if($suspend_date > $today){
			exit(show_error($ci->lang->line('suspended')));
		}
	}
	#see if the IP of the user is banned.
	$uip = detectProxy();

	$ci->db->distinct('ban_item')->from('ebb_banlist')->where('ban_type', 'IP')->like('ban_item', $uip)->limit(1);
	$banChk = $ci->db->count_all_results();

	#output an error msg.
	if($banChk == 1){
		exit(show_error($ci->lang->line('banned')));
	}
}