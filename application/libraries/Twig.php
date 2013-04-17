<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 * Twig.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright  (c) 2006-2011
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 04/14/2013
 * CREDIT
 * @author Bennet Matschullat <bennet.matschullat@giantmedia.de>
 * @since 07.03.2011 - 12:00:39
 */

/**
 * This will help interactTwig within Codeigniter.
*/
class Twig {

	#
	# LOCAL CONSTANCE
	#

    const TWIG_CONFIG_FILE = "twig";
    
	#
	# DATA MEMBERS
	#

    protected $_template_dir;
    protected $_cache_dir;    
    private $ci;
    public $_twig_env;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->config->load(self::TWIG_CONFIG_FILE); // load config file
        
        // set include path for twig
        ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . APPPATH . 'third_party/Twig');
        require_once (string) 'Autoloader.php';
        
        // register autoloader        
        Twig_Autoloader::register();
        log_message('debug', 'twig autoloader loaded');
        
        // init paths
        $this->_template_dir = $this->ci->config->item('template_dir');
        $this->_cache_dir = $this->ci->config->item('cache_dir');
                
        // load environment
        $loader = new Twig_Loader_Filesystem($this->_template_dir, $this->_cache_dir);
        $this->_twig_env = new Twig_Environment($loader);	
		
		//global Filters/Functions goes here.
		$this->_twig_env->addFunction('URL_TAG', new Twig_Function_Function('anchor'));
		$this->_twig_env->addFunction('LinkTag', new Twig_Function_Function('link_tag'));
		$this->_twig_env->addFunction('IMG', new Twig_Function_Function('img'));
		$this->_twig_env->addFunction('nl_2_br', new Twig_Function_Function('nl2br'));
		$this->_twig_env->addFilter('PostedDate', new Twig_Filter_Function('datetimeFormatter')); //this is depreatiated.
		$this->_twig_env->addFunction('formatDate', new Twig_Function_Function('datetimeFormatter'));
    }

	/**
	 * render a twig template file
	 * @param string $controller the controller that the template is related to.
	 * @param string $template template name
	 * @param array $data contains all varnames'
	 * @param boolean $render
	 * @param boolean $return
	 * @return mixed
	 * @version 07/30/12
	*/
    public function render($controller, $template, $data = array(), $render = true) {
		//load up the template.
		$template = $this->_twig_env->loadTemplate($controller.'/'.$template.'.twig');
		return ($render)?$template->render($data):$template;
    }

	/**
	 * render a twig template file with no style required.
	 * @param string $template template name
	 * @param array $data contains all varnames'
	 * @param boolean $render
	 * @return mixed
	 * @version 12/01/11
	*/
	public function renderNoStyle($template, $data = array(), $render = true) {
        $template = $this->_twig_env->loadTemplate($template);
        //log_message('debug', 'twig template loaded');
        return ($render)?$template->render($data):$template;
    }   
}