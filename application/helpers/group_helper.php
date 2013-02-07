<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * group_helper.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright  (c) 2006-2011
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 07/02/2012
*/

// THIS IS MAINLY USED FOR INTERACTION
// WITH THE TWIG TEMPLATE ENGINE.


/**
 * Get the Group Name for the defined user.
 * @version 1/3/12
 * @param mixed $user Current UserName
 * @param integer $gid GroupID
 * @return mixed
 */
function GetGroupName($user, $gid) {
	//see if the object is used yet.
	if (!class_exists('Groupmodel')) {
		require_once(APPPATH.'models/groupmodel.php');
	}

	$groupObj = new Groupmodel();
	$groupObj->gid = $gid;
	$groupObj->user = $user;

	return $groupObj->getGroupName();
}

/**
 * Validate to see if user can access the requested area.
 * @version 1/3/12
 * @param mixed $user Current UserName
 * @param integer $gid GroupID
 * @param int $type type of permission being checked (0=board, 1=group).
 * @param string $action The action being validated.
 * @return boolean
 */
function ValidateAccess($user, $gid, $type, $action) {
	//see if the object is used yet.
	if (!class_exists('Groupmodel')) {
		require_once(APPPATH.'models/groupmodel.php');
	}

	$groupObj = new Groupmodel();
	$groupObj->gid = $gid;
	$groupObj->user = $user;

	return $groupObj->validateAccess($type, $action);
}

/**
 * Obtain the Access Level for User's associated group.
 * @version 1/3/12
 * @param mixed $user Current UserName
 * @param integer $gid GroupID
 * @return integer
 */
function GetGroupAccessLevel($user, $gid) {
	//see if the object is used yet.
	if (!class_exists('Groupmodel')) {
		require_once(APPPATH.'models/groupmodel.php');
	}

	$groupObj = new Groupmodel();
	$groupObj->gid = $gid;
	$groupObj->user = $user;

	return $groupObj->getGroupAccessLevel();
}
