<?php
define('IN_EBB', true);
/*
Filename: smiles.php
Last Modified: 10/25/2007

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/
include "config.php";
require "header.php";
//output
$allsmile = showall_smiles();
$page = new template($template_path ."/smiles.htm");
$page->replace_tags(array(
  "TITLE" => "$title",
  "LANG-TITLE" => "$post[moresmiles]",
  "LANG-TEXT" => "$post[smiletxt]",
  "SMILE" => "$allsmile",
  "LANG-CLOSEWINDOW" => "$txt[closewindow]"));
$page->output();
?>
