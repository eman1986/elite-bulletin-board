<?php
if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
 * notifySys.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright  (c) 2006-2011
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 7/21/2011
*/

class notifySys{

    #declare data members
    private $msg;
	private $displayTitle;
	private $debugStat;
	private $errFile;
	private $errLine;

/**
	*__construct
	*
	*Setup Error Message.
	*
	*@modified 5/18/11
	*
	*@param string $message - Message to be displayed later.
	*@param boolean $titlestat - determines if breadcumb will be displayed or not.
	*@param boolean $debug - See if we wish to see debugging details (default value is false).
	*@param string $fle - The filename where the error occured at (default value is N/A).
	*@param string $ln - The line where the error occured at (default value is N/A).
	 * @param boolean $ajax - Is this an AJAX request? (default is false)
	*
	*@access public
	*/
	public function __construct($message, $titleStat, $debug=false, $fle="N/A", $ln="N/A"){

		#define some values to use in the error class.
		$this->msg = $message;
		$this->displayTitle = $titleStat;
		$this->debugStat = $debug;
		$this->errFile = $fle;
		$this->errLine = $ln;
	}
	
    /**
	*__destruct
	*
	*Clean up variables used in __construct function.
	*
	*@modified 5/18/11
	*
	*@access public
	*/
    public function __destruct(){
		unset($this->msg);
	    unset($this->displayTitle);
	    unset($this->debugStat);
		unset($this->errFile);
	    unset($this->errLine);
	}

    /**
	*displayError
	*
	*Displays error message that will match the current style being used by the user.
	*
	*@modified 7/21/11
	*
	*@access public
	*/
	public function displayError(){

		global $title, $lang, $style;

		//see if we're in install-mode
		if (!defined('EBBINSTALLED')) {
			$tpl = new templateEngine(0, "error", "installer");
		} else {
			$tpl = new templateEngine($style, "error");
		}

		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[error]",
		"ERRORMSG" => "$this->msg",
		"FILE" => "$this->errFile",
		"LINE" => "$this->errLine"));

		#see if the titlebar show display.
		if ($this->displayTitle == false){
			$tpl->removeBlock("titlebar");
		}
		
		#see if we need debugging info(filename & line of error).
		if($this->debugStat == false){
		    $tpl->removeBlock("debug");
		}
	
		#output result
		echo $tpl->outputHtml();

		#halt further processing.
		exit;
	}

    /**
			*displayMessage
			*
			*Displays general message that will match the current style being used by the user.
			*
			*@modified 7/21/11
			*
			*@access public
			*/
	public function displayMessage(){
	
		global $title, $lang, $style;

		//see if we're in install-mode
		if (!defined('EBBINSTALLED')) {
			$tpl = new templateEngine(0, "error-message", "installer");
		} else {
			$tpl = new templateEngine($style, "error-message");
		}
		
		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[info]",
		"ERRORMSG" => "$this->msg",
		"FILE" => "$this->errFile",
		"LINE" => "$this->errLine"));

		#see if the titlebar show display.
		if ($this->displayTitle == false){
			$tpl->removeBlock("titlebar");
		}

		#see if we need debugging info(filename & line of error).
		if($this->debugStat == false){
		    $tpl->removeBlock("debug");
		}

		#output result
		echo $tpl->outputHtml();
	}

    /**
			*displayValidate
			*
			*Displays validate error message that will match the current style being used by the user.
			*
			*@modified 7/21/11
			*
			*@access public
			*/
	public function displayValidate(){
	
		global $title, $lang, $style;

		//see if we're in install-mode
		if (!defined('EBBINSTALLED')) {
			$tpl = new templateEngine(0, "error-validate", "installer");
		} else {
			$tpl = new templateEngine($style, "error-validate");
		}
		
		$tpl->parseTags(array(
		"TITLE" => "$title",
		"LANG-TITLE" => "$lang[error]",
		"ERRORMSG" => "$this->msg"));

		#see if the titlebar show display.
		if ($this->displayTitle == false){
			$tpl->removeBlock("titlebar");
		}

		#output result
		echo $tpl->outputHtml();
	}

    /**
     * Setup Error for Ajax Requests
     * @param $type string what type  of message are we displaying
    */
    public function displayAjaxError($type){
		global $style;

		//see if we're in install-mode
		if (!defined('EBBINSTALLED')) {
			$tpl = new templateEngine(0, "error-ajax", "installer");
		} else {
			$tpl = new templateEngine($style, "error-ajax");
		}
		
		$tpl->parseTags(array(
		"ERRORMSG" => "$this->msg"));

		#see if the titlebar show display.
		if ($type == "success"){
			$tpl->removeBlock("error");
			$tpl->removeBlock("warning");
		} elseif ($type == "warning") {
			$tpl->removeBlock("error");
			$tpl->removeBlock("success");
		} else {
			$tpl->removeBlock("success");
			$tpl->removeBlock("warning");
		}

		#output result
		echo $tpl->outputHtml();
	}

    /**
	*genericError
	*
	*Displays generic error message used when style isn't important or loaded.
	*
	*@modified 12/4/10
	*
	*@access public
	*/
	public function genericError(){

    	#see if we need debugging info(filename & line of error).
		if($this->debugStat == false){
			die($this->msg);
		}else{
			die($this->msg.'<hr />File:'.$this->errFile.'<br />Line:'.$this->errLine);
		}
	}
}
?>
