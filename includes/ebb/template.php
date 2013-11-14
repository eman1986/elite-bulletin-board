<?php
namespace ebb;
use Exception;
use Twig_Loader_Filesystem;
use Twig_Environment;
use Twig_SimpleFunction;

if (!defined('IN_EBB')) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
 * template.php
 * @package Elite Bulletin Board
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2015
 * @version 11/04/2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
*/

class template {

    /**
     * Twig Template Engine Instance
     * @var string
    */
    public $twigTpl;

    /**
     * @param string $styleFolder The folder the template resides in.
     * @throws Exception if template engine cannot load template file.
    */
    public function __construct($styleFolder="clearblue2") {
        // load environment
        $loader = new Twig_Loader_Filesystem(FULLPATH."/template/".$styleFolder, FULLPATH."/template_cache");
        $this->twigTpl = new Twig_Environment($loader);

        //global Filters/Functions goes here.
        $this->twigTpl->addFunction(new Twig_SimpleFunction('nl_2_br', 'nl2br'));
        $this->twigTpl->addFunction(new Twig_SimpleFunction('formatDate', 'dateTimeFormatter'));
    }

    /**
     * render a twig template file
     * @param string $template The template file to load
     * @param array $data what to parse
     * @return mixed
    */
    public function output($template, $data = array()) {
        //load up the template.
        $template = $this->twigTpl->loadTemplate('/'.$template.'.twig');
        return $template->render($data);
    }
}