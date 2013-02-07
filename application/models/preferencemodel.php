<?php
if (!defined('BASEPATH')) {exit('No direct script access allowed');}
/**
 * preferencemodel.php
 * @package Elite Bulletin Board v3
 * @author Elite Bulletin Board Team <http://elite-board.us>
 * @copyright  (c) 2006-2011
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 07/02/2012
*/

/**
 * Preference Entity
 */
class Preferencemodel extends CI_Model {

	/**
	 * DATA MEMBERS
	*/

	private $prefName;
	private $prefValue;
	private $prefType;

    public function __construct() {
        parent::__construct();
    }

	/**
	 * PROPERTIES
	*/

	/**
	 * set value for pref_name 
	 *
	 * type:VARCHAR,size:255,default:null,unique
	 *
	 * @param mixed $prefName
	 * @return Preferencemodel
	 */
	public function &setPrefName($prefName) {
		$this->prefName=$prefName;
		return $this;
	}

	/**
	 * get value for pref_name 
	 *
	 * type:VARCHAR,size:255,default:null,unique
	 *
	 * @return mixed
	 */
	public function getPrefName() {
		return $this->prefName;
	}

	/**
	 * set value for pref_value 
	 *
	 * type:VARCHAR,size:255,default:null
	 *
	 * @param mixed $prefValue
	 * @return Preferencemodel
	 */
	public function &setPrefValue($prefValue) {
		$this->prefValue=$prefValue;
		return $this;
	}

	/**
	 * get value for pref_value 
	 *
	 * type:VARCHAR,size:255,default:null
	 *
	 * @return mixed
	 */
	public function getPrefValue() {
		return $this->prefValue;
	}

	/**
	 * set value for pref_type 
	 *
	 * type:BIT,size:0,default:null
	 *
	 * @param mixed $prefType
	 * @return Preferencemodel
	 */
	public function &setPrefType($prefType) {
		$this->prefType=$prefType;
		return $this;
	}

	/**
	 * get value for pref_type 
	 *
	 * type:BIT,size:0,default:null
	 *
	 * @return mixed
	 */
	public function getPrefType() {
		return $this->prefType;
	}

	/**
	 * METHODS
	*/

	/**
	 * Load the preference properties based on defined value.
	 * @param string $pref Preference defined by object.
	 * @version 04/12/12
	 */
	public function GetPreferenceData($pref) {

		//fetch topic data.
		$this->db->select('pref_name, pref_value, pref_type')->from('ebb_preference')->where('pref_name', $pref)->limit(1);
		$query = $this->db->get();

		//see if we have any records to show.
		if($query->num_rows() > 0) {
			$PrefData = $query->row();
			
			//populate properties with values.
			$this->setPrefName($PrefData->pref_name);
			$this->setPrefValue($PrefData->pref_value);
			$this->setPrefType($PrefData->pref_type);
		} else {
			//no record was found, throw an error.
			show_error($this->lang->line('invalidpref').'<hr />File:'.__FILE__.'<br />Line:'.__LINE__, 500, $this->lang->line('error'));
			log_message('error', 'invalid preference value was provided.'); //log error in error log.
		}

	}

	/**
	 * Creates New preference value.
	 * @version 1/31/12
	 */
	public function CreatePreferenceValue() {
		#setup values.
		$data = array(
		   'pref_name' => $this->getPrefName(),
		   'pref_value' => $this->setPrefValue(),
		   'pref_type' => $this->setPrefType());

		#add new preference.
		$this->ci->db->insert('ebb_preference', $data);
	}

	/**
	 * Update Preference Values
	 * @version 1/31/12
	 */
	public function UpdatePreferenceValue() {
		#update preferences.
		$this->db->where('pref_name', $this->getPrefName());
		$this->db->update('ebb_preference', array('pref_value' => $this->getPrefValue(), 'pref_type' => $this->getPrefType()));
	}

	/**
	 * Delete preference value from database.
	 * @version 1/17/12
	 */
	public function DeletePreferenceValue() {
		$this->db->delete('ebb_preference', array(' pref_name' => $this->getPrefName()));
	}

}
