<?php
namespace ebb;
use Exception;

if (!defined('IN_EBB')) {
    die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: template.php
Last Modified: 10/20/2013

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

    /**
     * @param string $file The template file we wish to process.
     * @param string $styleFolder The folder the template resides in.
    */
    public function __construct($file, $styleFolder="clearblue2") {

        global $boardDir;

        #set styleDir to the path of the requested styleID.
        $this->styleDir = trailingSlashRemover($_SERVER['DOCUMENT_ROOT']).'/'.$boardDir.'/template/'.$styleFolder.'/';

        #see if template file exists.
        if (!file_exists($this->styleDir.$file.'.htm')){
            throw new \Exception('Template file ('.$this->styleDir.$file.'.htm) was not found.');
        }else{
            #get the contents of the template file
            $contents = file_get_contents($this->styleDir.$file.'.htm');

            #see if template file is empty.
            if(empty($contents)){
                throw new \Exception('Template file is empty.');
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