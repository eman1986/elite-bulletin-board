<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/*
Filename: posting_function.php
Last Modified: 10/21/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/


/**
 * Our list of emoticons.
 * @return array
*/
function getSmilesList() {
    return array(
        //smiley            image name                      width   height  alt
        ':)'            =>  array('smiley.png',             '16',   '16',   'smile'),
        'O:)'           =>  array('smiley-angel.png',       '16',   '16',   'angel'),
        ':?'            =>  array('smiley-confuse.png',     '16',   '16',   'confuse'),
        '8)'            =>  array('smiley-cool.png',        '16',   '16',   'cool'),
        ':cry:'         =>  array('smiley-cry.png',         '16',   '16',   'cry'),
        ":'("           =>  array('smiley-cry.png',         '16',   '16',   'cry'),
        ':eek:'         =>  array('smiley-eek.png',         '16',   '16',   'eek'),
        ':evil:'        =>  array('smiley-evil.png',        '16',   '16',   'evil'),
        ':D'            =>  array('smiley-grin.png',        '16',   '16',   'Grin'),
        ':*'            =>  array('smiley-kiss.png',        '16',   '16',   'kiss'),
        ':kiss:'        =>  array('smiley-kiss.png',        '16',   '16',   'kiss'),
        ':lol:'         =>  array('smiley-lol.png',         '16',   '16',   'lol'),
        ':angry:'       =>  array('smiley-mad.png',         '16',   '16',   'mad'),
        ':mrgreen:'     =>  array('smiley-mr-green.png',    '16',   '16',   'mr. green grin'),
        ':nerd:'        =>  array('smiley-nerd.png',        '16',   '16',   'nerdy smile'),
        ':|'            =>  array('smiley-neutral.png',     '16',   '16',   'neutral'),
        ':P'            =>  array('smiley-razz.png',        '16',   '16',   'raspberry'),
        ':oops:'        =>  array('smiley-red.png',         '16',   '16',   'embarrassed'),
        ':roll:'        =>  array('smiley-roll.png',        '16',   '16',   'eye roll'),
        ':stressed:'    =>  array('smiley-roll-sweat.png',  '16',   '16',   'stressed'),
        ':('            =>  array('smiley-sad.png',         '16',   '16',   'sad'),
        ':zzz:'         =>  array('smiley-sleep.png',       '16',   '16',   'sleepy'),
        '8O'            =>  array('smiley-surprise.png',    '16',   '16',   'surprised'),
        ':!'            =>  array('smiley-surprise.png',    '16',   '16',   'surprised'),
        ':sweat:'       =>  array('smiley-sweat.png',       '16',   '16',   'sweating'),
        ':twisted:'     =>  array('smiley-twist.png',       '16',   '16',   'twisted'),
        ';)'            =>  array('smiley-wink.png',        '16',   '16',   'winking'),
        ':yell:'        =>  array('smiley-yell.png',        '16',   '16',   'yelling'),
        ':X'            =>  array('smiley-zipper.png',      '16',   '16',   'zipped'),
        ':%'            =>  array('smiley-zipper.png',      '16',   '16',   'zipped') // no comma after last item
    );
}

/**
 * converts text-based smiles into graphical ones.
 * @param string $str the string to search for emoticons.
 * @return string
*/
function smiles($str) {

    $smiles = getSmilesList();
    foreach ($smiles as $key => $val)
    {
        $str = str_replace($key, "<img src=\"images/smiles/".$smiles[$key][0]."\" width=\"".$smiles[$key][1]."\" height=\"".$smiles[$key][2]."\" alt=\"".$smiles[$key][3]."\" style=\"border:0;\" />", $str);
    }

    return $str;
}

/**
 * Outputs the list of smiles available(up to 30).
 * @param string $val the object to search for
 * @return string HTML displaying smiles.
*/
function form_smiles($val){

    global $allowsmile;

    if ($allowsmile == 0){
        $smile = '';
    } else {
        $x = 0; // we will use this to count to four later
        $smile = '';
        $smiles = getSmilesList();
        $used = array();
        foreach($smiles as $key => $value) {
            // Keep duplicates from being used, which can happen if the
            // mapping array contains multiple identical replacements.  For example:
            // :-) and :) might be replaced with the same image so both smileys
            // will be in the array.
            if (isset($used[$smiles[$key][0]]))
            {
                continue;
            }

            if (($x % 4) == 0) {
                $smile .= "<br />";  // $x == 4 so we start the line again
                $x = 0; // $x is now 4 so we reset it here to start the next line
            }

            $smile .= "<a href=\"javascript:void(0);\" onclick=\"smile('".$key. "', '".$val."')\"><img src=\"images/smiles/".$smiles[$key][0]."\"  width=\"".$smiles[$key][1]."\" height=\"".$smiles[$key][2]."\" alt=\"".$smiles[$key][3]."\" style=\"border:0;\" /></a><br />\n";
            $used[$smiles[$key][0]] = TRUE;
            $x++; // increment $x by 1 so we get our 4
        }
    }
    return $smile;
}

/**
 * Grabs a list of all available smiles.
 * @return string
 */
function showall_smiles() {

    global $allowsmile;

    if ($allowsmile == 0){
        $allsmile = '';
    } else {
        $x = 0; // we will use this to count to four later
        $allsmile = '';
        $smiles = getSmilesList();
        $used = array();
        foreach($smiles as $key => $value) {
            // Keep duplicates from being used, which can happen if the
            // mapping array contains multiple identical replacements.  For example:
            // :-) and :) might be replaced with the same image so both smileys
            // will be in the array.
            if (isset($used[$smiles[$key][0]]))
            {
                continue;
            }

            if (($x % 3) == 0) {
                $allsmile .= "</tr><tr>";  // $x == 8 so we start the line again
                $x = 0; // $x is now 4 so we reset it here to start the next line
            }

            $allsmile .= "<td class=\"td1\" align=\"center\" width=\"20%\"><img src=\"images/smiles/".$smiles[$key][0]."\"  width=\"".$smiles[$key][1]."\" height=\"".$smiles[$key][2]."\" alt=\"".$smiles[$key][3]."\" style=\"border:0;\" /></td>\n
            <td class=\"td2\" width=\"20%\">".$key."</td>";
            $used[$smiles[$key][0]] = TRUE;
            $x++; // increment $x by 1 so we get our 4
        }
    }

    return $allsmile;
}

/**
 * Formats our messages converting over BBCode tags into HTML content.
 * @param string $str the string to check for our BBCode tags.
 * @param boolean $allowimgs allow parsing of image tags?
 * @return string
*/
function BBCode($str, $allowimgs = FALSE) {
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
 * Same functionality as BBcode, only used for printer-friendly pages and has a limited number of things it'll parse.
 * @param string $str the string to check for our BBCode tags.
 * @return string
*/
function BBCode_print($str) {
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

/* This is a helper function for the you tube BBCode.
 * @param string vCode the vcode assigned by youtube.
 * @return mixed
*/
function youtubeParse($vCode) {
    return '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/'.$vCode.'"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/'.$vCode.'" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
}


#bbcode button output
function bbcode_form($val){

	global $allowbbcode, $allowimg;

	if ($allowbbcode == 0){
		$bbcode = '';
	}else{
		$bbcode = "<input type=\"button\" value=\"B\" onclick=\"javascript:bold('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"I\" onclick=\"javascript:italic('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"U\" onclick=\"javascript:underline('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Url\" onclick=\"javascript:url('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Quote\" onclick=\"javascript:quote('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Code\" onclick=\"javascript:code('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Marque\" onclick=\"javascript:marque('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Superscript\" onclick=\"javascript:sup('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Subscript\" onclick=\"javascript:sub('$val')\" class=\"submit\" />&nbsp;
		<input type=\"button\" value=\"List\" onclick=\"javascript:list('$val')\" class=\"submit\" />&nbsp;";
		if ($allowimg == 1){
			$bbcode .= "<input type=\"button\" value=\"Image\" onclick=\"javascript:img('$val')\" class=\"submit\" />";
		}
		$bbcode .= "<input type=\"button\" value=\"Left\" onclick=\"javascript:left('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Center\" onclick=\"javascript:center('$val')\" class=\"submit\" />
		<input type=\"button\" value=\"Right\" onclick=\"javascript:right('$val')\" class=\"submit\" />";
	}
    return ($bbcode);
}
#language filter function - filters words
function language_filter($string, $type) {

	global $db, $cp;
	
	if((!isset($string)) or (empty($string))){
		die('spam check is null.');
	}
	if((!isset($type)) or (empty($type))){
		die($cp['invalidcensoraction']);
	}
	#determine type action.
   	if($type == 1){
		$db->run = "SELECT Original_Word FROM `ebb_censor` where action='1'";
		$words = $db->query();
		$db->close();
		#see what to do based on action type.
		$stars = '';
		while ($row = mysql_fetch_assoc ($words)) {
			$obscenities = array ($row['Original_Word']);
			foreach ($obscenities as $curse_word) {
				if (stristr(trim($string), $curse_word)) {
					$length = strlen($curse_word);
					for ($i = 1; $i <= $length; $i++) {
						$stars .= "*";
					}
					$string = eregi_replace($curse_word,$stars,trim($string));
					$stars = "";
				}
			}
		}
	}else{
		$db->run = "SELECT Original_Word FROM `ebb_censor` where action='2'";
		$words = $db->query();
		$db->close();
		while ($row = mysql_fetch_assoc ($words)) {
			//see if anything matches the spam word list.
			if (preg_match("/\b".$row['Original_Word']."\b/i", $string)) {
				die('SPAMMING ATTEMPT!');
			}
		}
	}
   return ($string);
}
#flood check
function flood_check($string, $type){

	global $db;

   	if((!isset($string)) or (empty($string))){
		die('No string found.');
	}
	if((!isset($type)) or (empty($type))){
		die('No Type found.');
	}

	#see what action to perform based on type.
	switch($type){
	case 'posting':
		$currtime = time() - 30;
		$db->run = "SELECT last_post FROM ebb_users WHERE Username='$string'";
		$get_time_r = $db->result();
		$db->close();
		#see if user is posting too quickly.
		if ($get_time_r['last_post'] > $currtime){
			$flood = 1;
		}else{
			$flood = 0;
		}
	break;
	case 'search':
		$currtime = time() - 20;
		$db->run = "SELECT last_search FROM ebb_users WHERE Username='$string'";
		$get_time_r = $db->result();
		$db->close();
		#see if user is posting too quickly.
		if ($get_time_r['last_search'] > $currtime){
			$flood = 1;
		}else{
			$flood = 0;
		}	
	break;
	}
	return ($flood);
}
#increase user's post count.
function post_count($string){

	global $db;

   	if((!isset($string)) or (empty($string))){
		die('No string found.');
	}
	//get current post count then add on to it.
	$db->run = "select Post_Count from ebb_users where Username='$string'";
	$get_num = $db->result();
	$db->close();
	$increase_count = $get_num['Post_Count'] + 1;
	$db->run = "UPDATE ebb_users SET Post_Count='$increase_count' WHERE Username='$string'";
	$db->query();
	$db->close();
}
#update board table. error here!!!
function update_board($bid, $newlink, $user){

	global $db, $time; 
	#update lasy post details for the selected board.
	$db->run = "update ebb_boards SET last_update='$time' WHERE id='$bid'";
	$db->query();
	$db->close();
	//update post link for board.
	$db->run = "Update ebb_boards SET Post_Link='$newlink', Posted_User='$user' WHERE id='$bid'";
	$db->query();
	$db->close();
	#clear data from read table for the board selected.
	$db->run = "DELETE FROM ebb_read_board WHERE Board='$bid'";
	$db->query();
	$db->close();
}
#update topic table. error here!!!
function update_topic($tid, $newlink, $user){

	global $db, $time;
	#update lasy post details for the selected topic.
	$db->run = "update ebb_topics SET last_update='$time' WHERE tid='$tid'";
	$db->query();
	$db->close();
	#clear data from read table for the topic selected.
	$db->run = "DELETE FROM ebb_read_topic WHERE Topic='$tid'";
	$db->query();
	$db->close(); 
	//update post link for topic.
	$db->run = "Update ebb_topics SET Post_Link='$newlink' WHERE tid='$tid'";
	$db->query();
	$db->close();
	//update last poster for topic.
	$db->run = "Update ebb_topics SET Posted_User='$user' WHERE tid='$tid'";
	$db->query();
	$db->close();
}
