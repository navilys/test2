<?php
require_once(PATH_PLUGINS.'pmReplicator'.PATH_SEP.'classes'.PATH_SEP.'class.tclAuxiliary.php');
require_once(PATH_PLUGINS.'pmWorkspaceManagement'.PATH_SEP.'classes'.PATH_SEP.'class.connectionHandler.php');
/**
 * Install databases required for multitenant
 * @author Edwin Paredes
 *
 */
class multitenantInstaller{
	const sPath = 'pmWorkspaceManagement/';
	public static function install(){
		if(self::verifyDbFile())
			return 'already installed';
		self::createDatabase();
		self::createDBfile();
		return 'success';
	}
	public function createDatabase(){
		//open file with creational queries
		$fp = fopen(PATH_PLUGINS.self::sPath.'data/mysql/multitenant.sql', 'r');
		$sQuery = fread($fp,99999);
		fclose($fp);
		G::pr($sQuery);//die;
		//connect to database with PDO using processmaker admin configuration
		$oUtility = new tclAuxiliary();
		$cResult = $oUtility->GetHashAdminConfiguration();
		$aParameters['sAdapter'] = 'mysql';
		$aParameters['sHost'] = $cResult[0];
		$aParameters['sName'] = 'wf_workflow';
		$aParameters['sUser'] = $cResult[1];
		$aParameters['sPass'] = $cResult[2];
		$oConnection = new connectionHandler($aParameters);
		$oConnection->connect();
		$oConnection->query($sQuery);
		//create db.php file for accessing the created database
		self::createDBfile();

	}
	public function createDBfile(){
		$sFile = '<?
			$dbAdapter = "mysql";
			$dbHost = "localhost:3306";
			$dbName = "multitenant";
			$dbUser = "multitenant";
			$dbPass = "multitenant";
		';
		$fp = fopen(PATH_PLUGINS.self::sPath.'db.php', 'w');
		fwrite($fp, $sFile);
		fclose($fp);
	}
	public function verifyDbFile(){
		return is_file(PATH_PLUGINS.self::sPath.'db.php');
	}
}