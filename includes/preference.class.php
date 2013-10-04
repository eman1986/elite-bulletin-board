<?php
if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: preference.class.php
Last Modified: 10/03/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class preference {

    /**
     * PDO instance.
     * @var PDO
    */
    protected $db;

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

    public function __construct(PDO $db) {
        $this->db = $db;
    }


    /**
     * Obtains the value of a defined preference.
     * @param string $prefName Name of preference to look for.
     * @return string
    */
    public function getPreferenceValue($prefName) {
        try {
            $query = $this->db->prepare('SELECT pref_value FROM ebb_preference WHERE pref_name=:pref_name LIMIT 1');
            $query->execute(array(":pref_name" => $prefName));

            if ($query->rowCount() > 0) {
                $prefVal = $query->fetch(PDO::FETCH_OBJ);

                return $prefVal->pref_value;
            } else {
                throw new Exception('Invalid preference option requested.');
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
        catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Obtains the type of a defined preference.
     * @param string $prefName
     * @return string
    */
    public function getPreferenceType($prefName) {
        try {
            $query = $this->db->prepare('SELECT pref_type FROM ebb_preference WHERE pref_name=:pref_name LIMIT 1');
            $query->execute(array(":pref_name" => $prefName));

            if ($query->rowCount() > 0) {
                $prefVal = $query->fetch(PDO::FETCH_OBJ);

                return $prefVal->pref_type;
            } else {
                throw new Exception('Invalid preference option requested.');
            }
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return null;
        }
        catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Save the defined preference value.
     * @param $prefName
     * @param $prefValue
     * @return bool
    */
    public function savePreferences($prefName, $prefValue){
        try {
            $query = $this->db->prepare('UPDATE ebb_preference SET pref_value=:pref_value  WHERE pref_name=:pref_name');
            $query->execute(array(":pref_value" => $prefValue, ":pref_name" => $prefName));
            return TRUE;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }

    /**
     * Create a new preference value(used for updates or modification-purposes only).
     * @param $prefName
     * @param $prefValue
     * @param $prefType
     * @return bool
    */
    public function newPreference($prefName, $prefValue, $prefType){
        try {
            $query = $this->db->prepare('INSERT INTO ebb_preference (pref_name, pref_value, pref_type) VALUES(:pref_name, :pref_value, :pref_type)');
            $query->execute(array(":pref_name" => $prefName, ":pref_value" => $prefValue, ":pref_type" => $prefType));
            return TRUE;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }

    /**
     * Deletes a preference from the database(used for updates or modification-purposes only).
     * @param $prefName
     * @return bool
     */
    public function deletePreference($prefName){
        try {
            $query = $this->db->prepare('DELETE FROM ebb_preference WHERE pref_name=:pref_name');
            $query->execute(array(":pref_name" => $prefName));
            return TRUE;
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            return FALSE;
        }
    }
}