<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
*/

$route['ACP']  = "ACP/main/index";
$route['ACP/phpinfo']  = "ACP/main/phpinfo";

$route['viewboard/(:num)']  = "boards/viewboard/$1";
$route['viewboard/(:num)/(:num)']  = "boards/viewboard/$1/$2";
$route['viewtopic/(:num)']  = "boards/viewtopic/$1";
$route['viewtopic/(:num)/(:num)']  = "boards/viewtopic/$1/$2";
$route['feed/(:num)']  = "boards/boardFeed/$1";
$route['latesttopics']  = "boards/latestPost";
$route['reply/(:num)']  = "boards/reply/$1";
$route['newtopic/(:num)']  = "boards/newtopic/$1";
$route['newpoll/(:num)']  = "boards/newpoll/$1";
$route['print/(:num)']  = "boards/printable/$1";
$route['reporttopic/(:num)']  = "boards/reporttopic/$1";
$route['download/(:num)']  = "boards/download/$1";
$route['quote/(:num)/(:num)/(:num)']  = "boards/quote/$1/$2/$3";
$route['deletetopic/(:num)']  = "boards/deletetopic/$1";
$route['deletepost/(:num)']  = "boards/deletepost/$1";
$route['editpost/(:num)']  = "boards/editpost/$1";
$route['edittopic/(:num)']  = "boards/edittopic/$1";
$route['activate/(:num)/(:num)']  = "login/ActivateAccount/$1/$2";
$route['viewprofile/(:num)']  = "users/viewprofile/$1";
$route['login']  = "login/LogIn";
$route['logout']  = "login/LogOut";
$route['register']  = "login/register";
$route['resetpassword']  = "login/PasswordRecovery";

/*
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "boards";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */
