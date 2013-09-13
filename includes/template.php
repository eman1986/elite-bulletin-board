<?php
if (!defined('IN_EBB') ) {
die("<B>!!ACCESS DENIED HACKER!!</B>");
}
/*
Filename: template.php
Last Modified: 9/12/2013

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
class template {

    /**
     * @var string
    */
    private $page;

    /**
     * @var string
    */
    private $styleDir;

    public function __construct($styleID, $file) {

        global $db, $boardDir;

        #do a check to see if the styleID used is valid.
        if($this->StyleCheck($styleID) == 0){
            $error = new notifySys("Invalid Style Selected.", false, true, __FILE__, __LINE__);
            $error->genericError();
        }else{
            #get the style template path from the db.
            $db->SQL = "SELECT Temp_Path FROM ebb_style WHERE id='$styleID'";
            $theme = $db->fetchResults();
        }

        #set styleDir to the path of the requested styleID.
        $this->styleDir = trailingSlashRemover($_SERVER['DOCUMENT_ROOT']).'/'.$boardDir.'/template/'.$theme['Temp_Path'].'/';

        #see if template file exists.
        if (!file_exists($this->styleDir.$file.'.htm')){
            $error = new notifySys('Template file ('.$this->styleDir.$file.'.htm) was not found.', false, true, __FILE__, __LINE__);
            $error->genericError();
        }else{
            #get the contents of the template file
            $contents = file_get_contents($this->styleDir.$file.'.htm');

            #see if template file is empty.
            if(empty($contents)){
                $error = new notifySys('Template file is empty.', false, true, __FILE__, __LINE__);
                $error->genericError();
            }else{
                #Add template contents into output variable.
                $this->page = $contents;
            }
        }
    }

    /**
     * tell the template class what to parse
     * @param array $tags
    */
    public function replace_tags($tags) {
        foreach ($tags as $tag => $data) {
            #if data is array, traverse recursive array of tags
            if (is_array($data)) {
                $this->page = preg_replace("/\{$tag/",'', $this->page);
            }
            $this->page = str_replace('{'.$tag.'}', $data, $this->page);
        }
    }

    /**
     * Outputs formatted template.
    */
    public function output() {
        echo $this->page;
    }
}