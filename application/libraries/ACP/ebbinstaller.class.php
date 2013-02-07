<?php
if (!defined('IN_EBB') ) {
	die("<b>!!ACCESS DENIED HACKER!!</b>");
}
/**
Filename: ebbinstaller.class.php
Last Modified: 7/13/2010

Term of Use:
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
*/

class EBBInstaller{

	/**
	*acpSmileInstaller
	*
	*Obtains a list of smile paks to install.
	*
	*@modified 5/18/10
	*
	*@access public
	*/
	public function acpSmileInstaller(){

		global $boardAddr;

		$handle = opendir(FULLPATH."/install");
		$smiles = '';

		#obtain the list of installer files.
		while (($smileFile = readdir($handle))) {
			#list only installers with the .smile.php extension.
			if (is_file(FULLPATH."/install/$smileFile") && false !== strpos($smileFile, '.smile.php')) {
				$smileInstaller = str_replace(".smile.php", "", $smileFile);
				$smiles .= '<div class="smileinstaller">- <a href="'.$boardAddr.'/install/'.$smileFile.'">'.$smileInstaller.'</a></div>';
			}
		}
		return ($smiles);
	}

	/**
	*acpPluginInstaller
	*
	*Obtains a list of plugin installers.
	*
	*@modified 7/13/10
	*
	*@access public
	*/
	public function acpPluginInstaller(){

		global $boardAddr;

		$handle = opendir(FULLPATH."/install");
		$plugin = '';

		#obtain the list of installer files.
		while (($pluginFile = readdir($handle))) {
            #list only installers with the .plugin.php extension.
			if (is_file(FULLPATH."/install/$pluginFile") && false !== strpos($pluginFile, '.plugin.php')) {
				$pluginInstaller = str_replace(".plugin.php", "", $pluginFile);
				$plugin .= '<div class="plugininstaller">- <a href="'.$boardAddr.'/install/'.$pluginFile.'">'.$pluginInstaller.'</a></div>';
			}
		}
		return ($plugin);
	}

	/**
	*acpStyleInstaller
	*
	*Obtains a list of style installers.
	*
	*@modified 7/13/10
	*
	*@access public
	*/
	public function acpStyleInstaller(){

		global $boardAddr;

		$styler = '';
		$handle = opendir(FULLPATH."/install");

        #obtain the list of installer files.
		while (($styleFile = readdir($handle))) {
            #list only installers with the .style.php extension.
			if (is_file(FULLPATH."/install/$styleFile") && false !== strpos($styleFile, '.style.php')) {
				$StyleInstaller = str_replace(".style.php", "", $styleFile);
				$styler .= '<div class="styleinstaller"><a href="'.$boardAddr.'/install/'.$styleFile.'">'.$StyleInstaller.'</a></div>';
			}
		}
		return ($styler);
	}

}
?>
