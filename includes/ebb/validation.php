<?php
namespace ebb;

if (!defined('IN_EBB') ) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: validation.php
Last Modified: 10/20/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class validation {

    /**
     * validate Emails by RegEx and by MX Checking.
     * @param string $str email to validate.
     * @return bool
    */
    public function validateEmail($str) {
        //Level 1 - see if anything is there.
        if (!empty($str)) {
            //Level 2 - see if it meets the correct format(name@domain)
            if (preg_match("(\w[-._\w]*\w@\w[-._\w]*\w\.\w{2,3})",$str)) {
                //Level 3 - see if the MX record is valid.
                if (checkdnsrr(array_pop(explode("@",$str)),"MX")) {
                    return TRUE;
                } else {
                    return FALSE;
                }//END L3.
            } else {
                return FALSE;
            }//END L2.
        } else {
            return FALSE;
        }//END L1.
    }

    /**
     * validate Emails by DB to see if its blacklisted.
     * @param string $str email to validate.
     * @return bool
    */
    //NOTE: rewrite this to simplify the logic of this function.
    public function bannedEmails($str) {

        global $db;

        //domain check.
        $checkDomain = explode("@", $str);

        $db->SQL = "SELECT match_type, ban_item FROM ebb_banlist WHERE ban_type='Email' AND ban_item like '$checkDomain[1]' or ban_item='$str'";
        $emailMatchChk = $db->affectedRows();
        $emailBanSQL = $db->query();

        //see if the email being used is blacklisted or not.
        if ($emailMatchChk == 0) {
            return FALSE;
        } else {
            while ($row = mysql_fetch_assoc($emailBanSQL)) {
                if ($row['match_type'] == "Wildcard") {
                    return TRUE;
                } else {
                    if ($row['ban_item'] == $str) {
                        return TRUE;
                    }//END EXACT CHECK.
                }//END WILDCARD CHECK.
            }//END WHILE LOOP.
        }//END SQL COUNT CHECK.
    }

    /**
     * validate usernames by DB to see if its blacklisted.
     * @param string $str username to validate.
     * @return bool
    */
    //NOTE: rewrite this to simplify the logic of this function.
    public function blacklistedUsernames($str) {

        global $db;

        $db->SQL = "SELECT blacklisted_username FROM ebb_blacklist";
        $result = $db->query();

        while($row = mysql_fetch_assoc($result)) {
            if (stristr($str, $row['blacklisted_username'])) {
                return TRUE;
            } else {
                return FALSE;
            }//END CHECK.
        }//END WHILE LOOP.
    }

    /**
     * validate string is a number.
     * @param string $str string to validate.
     * @return bool
    */
    public function validateNumeric($str) {

        //Level 1 - see if anything is there.
        if (!empty($str)) {
            //Level 2 - See if the variable is a number.
            if(is_numeric($str)) {
                return (true);
            }else{
                return (false);
            }//END L2.
        }else{
            return (false);
        }//END L1.
    }

    /**
     * validate string contains only alpha characters.
     * @param string $str string to validate.
     * @return bool
    */
    public function validateAlpha($str) {

        //Level 1 - check for empty string.
        if (!empty($str)) {
            //Level 2 - perform a Regex check to determine the string is all alpha.
            if (preg_match("/^[a-zA-Z]+$/", $str)) {
                return TRUE;
            } else {
                return FALSE;
            }//END L2.
        } else {
            return FALSE;
        }//END L1.
    }

    /**
     * validate string contains only alpha and numeric characters.
     * @param string str string to validate.
     * @return bool
    */
    public function validateAlphaNumeric($str) {

        //Level 1 - check for empty string.
        if (!empty($str)) {
            //Level 2 - perform a Regex check to determine the string is either alpha or numeric.
            if (preg_match("/^[a-zA-Z0-9]+$/", $str)) {
                return TRUE;
            } else {
                return FALSE;
            }//END L2.
        } else {
            return FALSE;
        }//END L1.
    }
}