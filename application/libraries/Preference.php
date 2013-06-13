<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
  * Preference.php
  * @package Elite Bulletin Board v3
  * @author Elite Bulletin Board Team <http://elite-board.us>
  * @copyright (c) 2006-2013
  * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause License
  * @version 06/12/2013
*/

class Preference{

	/**
	 * @var object CodeIgniter object.
	*/
	private $ci;
	
	/**
	 * System Values (Read-Only)
	 * @var integer
	*/
	public static $system = 0;
	
	/**
	 * String Values
	 * @var integer
	*/
	public static $string = 1;
	
	/**
	 * Numeric Values
	 * @var integer
	*/
	public static $numeric = 2;
	
	/**
	 * Boolean Values
	 * @var integer
	*/
	public static $boolean = 3;


    public function __construct() {
        $this->ci =& get_instance();
    }
 	
	/**
	 * Obtains the value of a defined preference.
	 * @param string $prefName Name of preference to look for.
	 * @return string the desired preference value.
	*/
	public function getPreferenceValue($prefName){
		//get preference value.
		$this->ci->Preferencemodel->GetPreferenceData($prefName);
		return $this->ci->Preferencemodel->getPrefValue();
	}

	/**
	 * Save the defined preference value.
	 * @param string $prefName Name of preference to look for.
	 * @param string $prefType The value type of this preference.
	 * @param string $prefValue New value of preference.
	*/
	public function savePreferences($prefName, $prefType, $prefValue){
		//populate properties with values.
		$this->ci->Preferencemodel->setPrefName($prefName);
		$this->ci->Preferencemodel->setPrefType($prefType);
		$this->ci->Preferencemodel->setPrefValue($prefValue);

		//update preferences.
		$this->ci->Preferencemodel->UpdatePreferenceValue();
	}

	/**
	 * Create a new preference value(used for updates or modification-purposes only).
	 * @param string $prefName Name of preference to look for.
	 * @param string $prefValue Value of preference.
	 * @param string $prefType Type of preference.
	*/
	public function newPreference($prefName, $prefValue, $prefType){
		//populate properties with values.
		$this->ci->Preferencemodel->setPrefName($prefName);
		$this->ci->Preferencemodel->setPrefValue($prefValue);
		$this->ci->Preferencemodel->setPrefType($prefType);

		//add new preference.
		$this->ci->Preferencemodel->CreatePreferenceValue();
	}

	/**
	 * Deletes a preference from the database(used for updates or modification-purposes only).
	 * @param string $prefName Name of preference to look for.
	*/
	public function deletePreference($prefName){

		//populate properties with values.
		$this->ci->Preferencemodel->setPrefName($prefName);

		//call delete method.
		$this->ci->Preferencemodel->DeletePreferenceValue();
	}
}
