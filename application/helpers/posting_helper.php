<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * posting_helper.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/17/2013
*/

/**
 * converts text-based smiles into graphical ones.
 * @param string $string the string to search for emoticons.
 * @return mixed
*/
function smiles($string) {
	#obtain codeigniter object.
	$ci =& get_instance();
	$ci->load->helper('smiley');
	return parse_smileys($string, $ci->config->item('base_url')."images/smiles/");
}

/**
 * Outputs the list of smiles available(up to 30).
 * @param int $boardPref_smiles Does the board allow smiles?
 * @return string HTML displaying smiles.
*/
function form_smiles($boardPref_smiles = 1) {
	#obtain codeigniter object.
	$ci =& get_instance();
	
	//get smileys array.
	include(APPPATH.'config/smileys.php');

	if ($boardPref_smiles == 0){
		$smile = '';
	}else{
		$smile = '';
		$x = 0;

		//loop through to build a list of smiles.
		foreach ($smileys as $emoticon=>$attrib) {
			if (($x % 30) == 0) {
				$smile .= "<br />"; //line break once we've reached our number per row assigned.
				$x = 0; //reset counter for next row.
			}

			//setup image properties.
			$image_properties = array(
			  'src' => $ci->config->item('base_url').'images/smiles/'.$attrib[0],
			  'width' => $attrib[1],
			  'height' => $attrib[2],
			  'alt' => $attrib[3],
			  'title' => $emoticon
			);

			//output smiles and increment counter.
			$smile .= '<a href="#smiles" title="'.$emoticon.'">'.img($image_properties).'</a>';
			$x++; //increment counter.
		}
	}
	return $smile;
}

/**
 * Formats our messages converting over BBCode tags into HTML content.
 * @param string $str the string to check for our BBCode tags.
 * @param boolean $allowimgs allow parsing of image tags?
 * @return string
*/
function BBCode($str, $allowimgs = FALSE) {
	$ci =& get_instance();

	//ensure we sanitised the string first!
	$str = $ci->security->xss_clean($str);
	$str = auto_link($str, 'both', TRUE);
		
    //see if we're able to parse the img tag.
    if ($allowimgs) {
		$find = array(
		  '~\[b\](.*?)\[/b\]~is',
		  '~\[i\](.*?)\[/i\]~is',
		  '~\[u\](.*?)\[\/u\]~is',
		  '~\[img\](.*?)\[\/img\]~is',
		  '~\[url\="?(.*?)"?\](.*?)\[\/url\]~is',
		  '~\[list\](.*?)\[\/list\]~is',
		  '~\[list\=(.*?)\](.*?)\[\/list\]~is',
		  '/\[\*\]\s?(.*?)\n/ms',
		  '~\[size\="?(.*?)"?\](.*?)\[\/size\]~is',
		  '~\[center\](.*?)\[\/center\]~is',
		  '~\[right\](.*?)\[\/right\]~is',
		  '~\[left\](.*?)\[\/left\]~is',
		  '~\[sub\](.*?)\[\/sub\]~is',
		  '~\[sup\](.*?)\[\/sup\]~is',
		  '~\[color=(.*?)\](.*?)\[\/color\]~is',
		  '~\[quote\](.*?)\[\/quote\]~is',
		  '~\[quote=(.*?)\](.*?)\[\/quote\]~is',
		  '~\[code\](.*?)\[\/code\]~is',
		  "/\\[youtube(=([0-9]+),([0-9]+))?\\](.+?)\\[\\/youtube\\]/se",
		);
		$replace = array(
		  '<b>\1</b>',
		  '<i>\1</i>',
		  '<u>\\1</u>',
		  '<img src="\\1" alt="" />',
		  '<a href="\1" target="_blank">\2</a>',
		  '<ul>\1</ul>',
		  '<ol start="\1">\2</ol>',
		  '<li>\\1</li>',
		  '<span style="font-size:\1%">\2</span>',
		  '<div align="center">\\1</div>',
		  '<div align="right">\\1</div>',
		  '<div align="left">\\1</div>',
		  '<sub>\\1</sub>',
		  '<sup>\\1</sup>',
		  '<span style="color: \\1">\\2</span>',
		  '<div class="quoteheader">Quote:</div><blockquote class="quote">\\1</blockquote>',
		  '<div class="quoteheader">\\1 Wrote:</div><blockquote class="quote">\\2</blockquote>',
		  '<div class="codeheader">Code:</div><div class="code"><pre style="display: inline;">\\1</pre></div>',
		  "youtubeParse('\\4')"
		);
    } else {
		$find = array(
		  '~\[b\](.*?)\[/b\]~is',
		  '~\[i\](.*?)\[/i\]~is',
		  '~\[u\](.*?)\[\/u\]~is',
		  '~\[url\="?(.*?)"?\](.*?)\[\/url\]~is',
		  '~\[list\](.*?)\[\/list\]~is',
		  '~\[list\=(.*?)\](.*?)\[\/list\]~is',
		  '/\[\*\]\s?(.*?)\n/ms',
		  '~\[size\="?(.*?)"?\](.*?)\[\/size\]~is',
		  '~\[center\](.*?)\[\/center\]~is',
		  '~\[right\](.*?)\[\/right\]~is',
		  '~\[left\](.*?)\[\/left\]~is',
		  '~\[sub\](.*?)\[\/sub\]~is',
		  '~\[sup\](.*?)\[\/sup\]~is',
		  '~\[color=(.*?)\](.*?)\[\/color\]~is',
		  '~\[quote\](.*?)\[\/quote\]~is',
		  '~\[quote=(.*?)\](.*?)\[\/quote\]~is',
		  '~\[code\](.*?)\[\/code\]~is',
		  "/\\[youtube(=([0-9]+),([0-9]+))?\\](.+?)\\[\\/youtube\\]/se",
		);
		$replace = array(
		  '<b>\1</b>',
		  '<i>\1</i>',
		  '<u>\\1</u>',
		  '<a href="\1" target="_blank">\2</a>',
		  '<ul>\1</ul>',
		  '<ol start="\1">\2</ol>',
		  '<li>\\1</li>',
		  '<span style="font-size:\1%">\2</span>',
		  '<div align="center">\\1</div>',
		  '<div align="right">\\1</div>',
		  '<div align="left">\\1</div>',
		  '<sub>\\1</sub>',
		  '<sup>\\1</sup>',
		  '<span style="color: \\1">\\2</span>',
		  '<div class="quoteheader">Quote:</div><blockquote class="quote">\\1</blockquote>',
		  '<div class="quoteheader">\\1 Wrote:</div><blockquote class="quote">\\2</blockquote>',
		  '<div class="codeheader">Code:</div><div class="code"><pre style="display: inline;">\\1</pre></div>',
		  "youtubeParse('\\4')"
		);
	}

	return preg_replace($find, $replace, $str);
}

/**
 * This is a helper function for the you tube BBCode.
 * @param vCode [str] - the vcode assigned by youtube.
 * @return mixed
*/
function youtubeParse($vCode) {
	return '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/'.$vCode.'"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/'.$vCode.'" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
}

/**
 * Same functionality as BBcode, only used for printer-friendly pages and has a limited number of things it'll parse.
 * @param string $str the string to check for our BBCode tags.
 * @return string
 */
function BBCode_print($str) {
	$ci =& get_instance();

	//ensure we sanitised the string first!
	$str = $ci->security->xss_clean($str);
	$str = auto_link($str, 'both', TRUE);

	$find = array(
	  '~\[b\](.*?)\[/b\]~is',
	  '~\[i\](.*?)\[/i\]~is',
	  '~\[u\](.*?)\[\/u\]~is',
	  '~\[url\="?(.*?)"?\](.*?)\[\/url\]~is',
	  '~\[list\](.*?)\[\/list\]~is',
	  '~\[list\=(.*?)\](.*?)\[\/list\]~is',
	  '/\[\*\]\s?(.*?)\n/ms',
	  '~\[size\="?(.*?)"?\](.*?)\[\/size\]~is',
	  '~\[center\](.*?)\[\/center\]~is',
	  '~\[right\](.*?)\[\/right\]~is',
	  '~\[left\](.*?)\[\/left\]~is',
	  '~\[sub\](.*?)\[\/sub\]~is',
	  '~\[sup\](.*?)\[\/sup\]~is',
	  '~\[color=(.*?)\](.*?)\[\/color\]~is',
	  '~\[quote\](.*?)\[\/quote\]~is',
	  '~\[quote=(.*?)\](.*?)\[\/quote\]~is',
	  '~\[code\](.*?)\[\/code\]~is'
	);
	$replace = array(
	  '<b>\1</b>',
	  '<i>\1</i>',
	  '<u>\\1</u>',
	  '<a href="\1" target="_blank">\2</a>',
	  '<ul>\1</ul>',
	  '<ol start="\1">\2</ol>',
	  '<li>\\1</li>',
	  '<span style="font-size:\1%">\2</span>',
	  '<div align="center">\\1</div>',
	  '<div align="right">\\1</div>',
	  '<div align="left">\\1</div>',
	  '<sub>\\1</sub>',
	  '<sup>\\1</sup>',
	  '<span style="color: \\1">\\2</span>',
	  '<div class="quoteheader">Quote:</div><blockquote class="quote">\\1</blockquote>',
	  '<div class="quoteheader">\\1 Wrote:</div><blockquote class="quote">\\2</blockquote>',
	  '<div class="codeheader">Code:</div><div class="code"><pre style="display: inline;">\\1</pre></div>'
	);

	return preg_replace($find, $replace, $str);
}

/**
 * See if a censored word was used in a string.
 * @param string $string The string we wish to validate.
 * @return string The string with the censored word(s) starred out.
 */
function censorFilter($string) {

	#obtain codeigniter object.
	$ci =& get_instance();

	#see if an invalid operation was set.
	if (!isset($string) || empty($string)) {
		return NULL; //nothing entered, then just return null.
	}

	$ci->load->model('Censormodel');

   return $ci->Censormodel->searchCensorList($string);
}

/**
 * Prevent users from performing an action too soon from another action.
 * @param string $type (posting;search).
 * @param string $LastActivity DB entry for last post time.
 * @return boolean
*/
function flood_check($type, $LastActivity){

	#see what action to perform based on type.
	switch($type){
	case 'posting':
		//30 second check.
		$currtime = time() - 30;

		#see if user is posting too quickly.
		if ($LastActivity > $currtime){
			$flood = TRUE;
		}else{
			$flood = FALSE;
		}
	break;
	case 'search':
		//20 second check.
		$currtime = time() - 20;

		#see if user is posting too quickly.
		if ($LastActivity > $currtime){
			$flood = TRUE;
		}else{
			$flood = FALSE;
		}	
	break;
	}
	return ($flood);
}

/**
 * increments the user's post count.
 * @param string $user user to increment count.
 * @TODO add logic to Usermodel
*/
function post_count($user){

	#obtain codeigniter object.
	$ci =& get_instance();
	
	$ci->db->select('Post_Count')
	  ->from('ebb_users')
	  ->where('id', $user);
	$q = $ci->db->get();
	$usrData = $q->row();
	
	$newPostCt = $usrData->Post_Count + 1;
	
	#update user.
	$data = array(
	  "Post_Count" => $newPostCt
	);
	$ci->db->where('Username', $user);
	$ci->db->update('ebb_users', $data);

}

/**
 * updates the last post field.
 * @param integer $bid BoardID.
 * @param string $newTid new topic id.
 * @param string $time UNIX Timestamp
 * @param string $postedUser the new posted by user.
 * @param integer $page the current page to direct user to (NULL by default)
 * @TODO add logic to Boardmodel
*/
function update_board($bid, $newTid, $time, $postedUser, $page = null) {
	#obtain codeigniter object.
	$ci =& get_instance();
	
	#update board data.
	$data = array(
	  "last_update" => $time,
	  "tid" => $newTid,
	  "posted_user" => $postedUser,
	  "last_page" => $page
	);
	$ci->db->where('id', $bid);
	$ci->db->update('ebb_boards', $data);
}

/**
 * updates the last post field.
 * @param integer $bid BoardID.
 * @param string $time UNIX Timestamp.
 * @param string $postedUser the new posted by user.
 * @param string $newPid new post id. (NULL by default)
 * @param integer $page the current page to direct user to. (NULL by default)
 * @TODO add logic to Topicmodel
*/
function update_topic($tid, $time, $postedUser, $newPid = null, $page = null) {
	
	#obtain codeigniter object.
	$ci =& get_instance();
	
	#update board data.
	$data = array(
	  "last_update" => $time,
	  "pid" => $newPid,
	  "posted_user" => $postedUser,
	  "last_page" => $page
	);
	$ci->db->where('tid', $tid);
	$ci->db->update('ebb_topics', $data);

	#clear data from read table for the topic selected.
	$ci->db->where('Topic', $tid);
	$ci->db->delete('ebb_read_topic');

}
