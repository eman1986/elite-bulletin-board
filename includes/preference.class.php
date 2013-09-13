<?php
if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: preference.class.php
Last Modified: 6/28/2010

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class preference{

 	/**
	*getPreferenceValue
	*
	*Obtains the value of a defined preference.
	*
	*@modified 8/2/09
	*
	*@param prefName[str] - Name of preference to look for.
	*
	*@access public
	*/
	public function getPreferenceValue($prefName){
	
		global $db;
		
		#get preference.
		$db->SQL = "SELECT pref_value FROM ebb_preference WHERE pref_name='".$db->filterMySQL($prefName)."' LIMIT 1";
		$prefValue = $db->fetchResults();
		
		return($prefValue['pref_value']);
	}
	
 	/**
	*getPreferenceType
	*
	*Obtains the type of a defined preference.
	*
	*@modified 8/2/09
	*
	*@param prefName[str] - Name of preference to look for.
	*
	*@access public
	*/
	public function getPreferenceType($prefName){

		global $db;

		#get preference type.
		$db->SQL = "SELECT pref_type FROM ebb_preference WHERE pref_name='".$db->filterMySQL($prefName)."' LIMIT 1";
		$prefType = $db->fetchResults();

		return($prefType['pref_type']);
	}
	
 	/**
	*savePreferences
	*
	*Save the defined preference value.
	*
	*@modified 6/28/10
	*
	*@param prefName[str] - Name of preference to look for.
	*@param prefValue[str] - value of preference.
	*
	*@access public
	*/
	public function savePreferences($prefName, $prefValue){

		global $db;
		
		#update preferences.
		$db->SQL = "UPDATE ebb_preference SET pref_value='".$db->filterMySQL($prefValue)."' WHERE pref_name='$prefName'";
		$db->query();
	}
	
 	/**
	*newPreference
	*
	*Create a new preference value(used for updates or modification-purposes only).
	*
	*@modified 6/28/10
	*
	*@param prefName[str] - Name of preference.
	*@param prefValue[str] - Value of preference.
	*@param prefType[int] - Type of preference.
	*
	*@access public
	*/
	public function newPreference($prefName, $prefValue, $prefType){

		global $db;

		#add new preference.
		$db->SQL = "INSERT INTO ebb_preference (pref_name, pref_value, pref_type) VALUES('".$db->filterMySQL($prefName)."', '".$db->filterMySQL($prefValue)."', '".$db->filterMySQL($prefType)."')";
		$db->query();
	}
	
 	/**
	*deletePreference
	*
	*Deletes a preference from the database(used for updates or modification-purposes only).
	*
	*@modified 6/28/10
	*
	*@param prefName[str] - Name of preference to delete.
	*
	*@access public
	*/
	public function deletePreference($prefName){

		global $db;

		#add new preference.
		$db->SQL = "DELETE FROM ebb_preference WHERE pref_name='".$db->filterMySQL($prefName)."' LIMIT 1";
		$db->query();
	}
}
?>
