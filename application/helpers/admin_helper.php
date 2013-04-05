<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * admin_helper.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright (c) 2006-2013
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
 * @version 02/15/2013
*/

/**
 * See if there is a newer copy of Elite Bulletin Board.
 * @return string JSON string
*/
function versionCheck() {
	#obtain codeigniter object.
	$ci =& get_instance();
	
    $connectionTimeout = 3;
    $url = 'http://elite-board.sourceforge.net/ebb_update.php';
	
	// try to get it by @file_get_contents first
	// if we can't due to server settings, we'll
	// use cURL instead.
    if (@ini_get('allow_url_fopen') == 0 || strtolower(@ini_get('allow_url_fopen')) == 'off') {
        if (function_exists('curl_init')) {
			// Configuring curl options
			$options = array(
			  CURLOPT_HEADER => false,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_HTTPHEADER => array('Content-type: application/json'),
			  CURLOPT_TIMEOUT => $connectionTimeout
			  );
			
            $ch = curl_init($url); // initialize connection to server
			curl_setopt_array($ch, $options); // Setup curl options
			$data =  curl_exec($ch); // Getting jSON result string
            curl_close($ch); // Close connection to server
        } else {
			log_message('error', 'cURL library & http:// wrapper is disabled in the server configuration.'); //log error in error log.
            return array(
			  'status' => 'error',
			  'msg' => $ci->lang->line('updateerr'),
			  'notes' => NULL,
			  'patch_link' => NULL,
			  'severity' => NULL
            );
        }
    } else {
		//@TODO turn on allow_url_fopen to test file_get_contents() method of connecting to update server.
		$context = stream_context_create(array(
		  'http' => array(
			'timeout' => $connectionTimeout),
			'header' => "Content-type: application/json"
			)
		 );
		$data = @file_get_contents($url, null, $context);
		if ($data === false) {
			log_message('error', 'failure to connect to update server.'); //log error in error log.
			return array(
			  'status' => 'error',
			  'msg' => $ci->lang->line('updateerr'),
			  'notes' => NULL,
			  'patch_link' => NULL,
			  'severity' => NULL
            );
		}
		
	}

    if (empty($data)) {
		log_message('error', 'Server returned an empty result set, possible failure to connect to update server.'); //log error in error log.
		return array(
		  'status' => 'error',
		  'msg' => $ci->lang->line('updateerr'),
		  'notes' => NULL,
		  'patch_link' => NULL,
		  'severity' => NULL
		);
    }

	//convert data from json to php object
    $versionData = json_decode($data);
	$latestVersion = $versionData->version_major.'.'.$versionData->version_minor.'.'.$versionData->version_patch.' '.$versionData->version_build;
	
	/*
	 {
		"status": "success",
		"version_major": "3",
		"version_minor": "0",
		"version_patch": "0",
		"version_build": "RC2",
		"version_notes": null,
		"version_releasedate": "01\/30\/2013",
		"version_upgradelink": null,
		"version_severity": null
	  }
	 */
	
	#check major release number.
	if ($versionData->version_major < $ci->config->item("version_major")) {
		$versionStatus = false;
	} else {
		#check minor release number.
		if ($versionData->version_minor < $ci->config->item("version_minor")) {
			$versionStatus = false;
		} else {
			#check patch release.
			if ($versionData->version_patch < $ci->config->item("version_patch")) {
				$versionStatus = false;
			} else {
				#check build.
				if ($versionData->version_build != $ci->config->item("version_build")) {
					$versionStatus = false;
				} else {
					$versionStatus = true;
				} #END BUILD CHECK.
			} #END PATCH CHECK.
		} #END MINOR CHECK.
	} #END MAJOR CHECK.
	
	if (!$versionStatus) {
		return array(
		  'status' => 'success',
		  'msg' => sprintf($ci->lang->line('update_available'), $latestVersion, $versionData->version_releasedate),
		  'notes' => $versionData->version_notes,
		  'patch_link' => $versionData->version_upgradelink,
		  'severity' => $versionData->version_severity
		);
	} else {
		return array(
		  'status' => 'success',
		  'msg' => $ci->lang->line('update_notavailable'),
		  'notes' => NULL,
		  'patch_link' => NULL,
		  'severity' => NULL
		);
	}
}