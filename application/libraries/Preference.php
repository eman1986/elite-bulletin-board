<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
  * Preference.php
  * @package Elite Bulletin Board v3
  * @author Elite Bulletin Board Team <http://elite-board.us>
  * @copyright  (c) 2006-2011
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  * @version 07/02/2012
*/

class Preference{

	/**
	 * @var object CodeIgniter object.
	*/
	private $ci;


    public function __construct() {
        $this->ci =& get_instance();
    }
 	
	/**
	 * Obtains the value of a defined preference.
	 * @version 1/17/12
	 * @param string $prefName Name of preference to look for.
	 * @access public
	*/
	public function getPreferenceValue($prefName){
		//get preference value.
		$this->ci->Preferencemodel->GetPreferenceData($prefName);
		return $this->ci->Preferencemodel->getPrefValue();
	}

	/**
	 * Save the defined preference value.
	 * @version 1/17/12
	 * @param string $prefName Name of preference to look for.
	 * @param string $prefType The value type of this preference.
	 * @param string $prefValue New value of preference.
	 * @access public
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
	 * @version 1/17/12
	 * @param string $prefName Name of preference to look for.
	 * @param string $prefValue Value of preference.
	 * @param string $prefType Type of preference.
	 * @access public
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
	 * @version 1/17/12
	 * @param string $prefName Name of preference to look for.
	 * @access public
	*/
	public function deletePreference($prefName){

		//populate properties with values.
		$this->ci->Preferencemodel->setPrefName($prefName);

		//call delete method.
		$this->ci->Preferencemodel->DeletePreferenceValue();
	}
}
