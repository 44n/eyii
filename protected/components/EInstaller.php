<?php
class EInstallerDb extends CComponent{	private $_db;

	public function getDbConnection()
	{
		if($this->_db===null)
		{
		$this->_db=Yii::app()->getComponent('db');
		if(!$this->_db instanceof CDbConnection)
			throw new CException(Yii::t('yii', 'The "db" application component must be configured to be a CDbConnection object.'));
		}
		return $this->_db;
	}

	public function setDbConnection($db)
	{
		$this->_db=$db;
	}

	public function execute($sql, $params=array())
	{
		$this->getDbConnection()->createCommand($sql)->execute($params);

	}


	public function insert($table, $columns)
	{
		$this->getDbConnection()->createCommand()->insert($table, $columns);
	}

	public function update($table, $columns, $conditions='', $params=array())
	{
		$this->getDbConnection()->createCommand()->update($table, $columns, $conditions, $params);
	}

	public function delete($table, $conditions='', $params=array())
	{
		$this->getDbConnection()->createCommand()->delete($table, $conditions, $params);
	}

	public function createTable($table, $columns, $options=null)
	{
		$this->getDbConnection()->createCommand()->createTable($table, $columns, $options);
	}

	public function renameTable($table, $newName)
	{
		$this->getDbConnection()->createCommand()->renameTable($table, $newName);
	}


	public function dropTable($table)
	{
		$this->getDbConnection()->createCommand()->dropTable($table);
	}

	public function truncateTable($table)
	{
		$this->getDbConnection()->createCommand()->truncateTable($table);
	}

	public function addColumn($table, $column, $type)
	{
		$this->getDbConnection()->createCommand()->addColumn($table, $column, $type);
	}

	public function dropColumn($table, $column)
	{
		$this->getDbConnection()->createCommand()->dropColumn($table, $column);
	}

	public function renameColumn($table, $name, $newName)
	{
		$this->getDbConnection()->createCommand()->renameColumn($table, $name, $newName);
	}

	public function alterColumn($table, $column, $type)
	{
		$this->getDbConnection()->createCommand()->alterColumn($table, $column, $type);
	}

	public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete=null, $update=null)
	{
		$this->getDbConnection()->createCommand()->addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
	}

	public function dropForeignKey($name, $table)
	{
		$this->getDbConnection()->createCommand()->dropForeignKey($name, $table);
	}

	public function createIndex($name, $table, $column, $unique=false)
	{
		$this->getDbConnection()->createCommand()->createIndex($name, $table, $column, $unique);
	}

	public function dropIndex($name, $table)
	{
		$this->getDbConnection()->createCommand()->dropIndex($name, $table);
	}

	public function refreshTableSchema($table)
	{
		$this->getDbConnection()->getSchema()->getTable($table,true);
	}


	public function addPrimaryKey($name,$table,$columns)
	{
		$this->getDbConnection()->createCommand()->addPrimaryKey($name,$table,$columns);
	}

	public function dropPrimaryKey($name,$table)
	{
		$this->getDbConnection()->createCommand()->dropPrimaryKey($name,$table);
	}
}

class EInstaller extends CApplicationComponent{

	private $_db;

	public function getDb(){		if($this->_db == null){			$this->_db = new EInstallerDb;
		}
		return $this->_db;
	}

	public function getAccess(){		return Yii::app()->authManager;
	}

	public function addPostInstall($key, $url){		$postInstall = Yii::app()->settings->get('postInstall', array());
		$postInstall[$key] = $url;
		Yii::app()->settings->set('postInstall', $postInstall);
	}

	public function completeStep($step=""){
		$curent = Yii::app()->settings->get('installStep', 'info');

		switch($step){
			case 're':
				$curent = 'info';
				Yii::app()->isInstall = false;
			break;
			case 'reAuto':
				$curent = 'auto';
				Yii::app()->isInstall = false;
			break;
			case 'info':
				 $curent = 'db';
			break;
			case 'db':
				 $curent = 'auto';
			break;
			case 'auto':
				 $curent = 'postInstall';
			break;
			case 'postInstall':
				$curent = 'complete';
				Yii::app()->isInstall = true;
			break;
			default:
				if($curent == 'postInstall'){					$postInstall = Yii::app()->settings->get('postInstall', array());
					unset($postInstall[$step]);
					Yii::app()->settings->set('postInstall', $postInstall);
				}
			break;
		}

		Yii::app()->settings->set('installStep', $curent);

		$this->nextStep($curent);
	}

	private function nextStep($step){

		switch($step){			case 'info':
				$url = Yii::app()->createUrl('install/info');
			break;
			case 'db':
				$url = Yii::app()->createUrl('install/db');
			break;
			case 'auto':
				$url = Yii::app()->createUrl('install/auto');
			break;
			case 'complete':
				$url = Yii::app()->createUrl('install/complete');
			break;
			case 'postInstall':
				$postInstall = Yii::app()->settings->get('postInstall', array());
				if(empty($postInstall)){
					return $this->completeStep("postInstall");
				}

				list(,$url) = each($postInstall);
			break;
		}
		Yii::app()->getRequest()->redirect($url);
	}

	public function installModules(){

		$this->installEyii();

		$preInstall = array('user', 'analytic', 'firewall');

		foreach($preInstall as $module){
			$m = Yii::app()->getModule($module);
			if($m !== null)$m->doInstall();
		}

		foreach(Yii::app()->modules as $id => $module){
			if(!in_array($id, $preInstall)){
				$m = Yii::app()->getModule($id);
				if($m !== null)$m->doInstall();
			}
		}

		return true;
	}

	private function installEyii(){		include_once(Yii::getPathOfAlias('application.update.install').'.php');
		$this->setInstallEyiiVersion(EYii::VERSION);	}

	private function unInstallEyii(){
		include_once(Yii::getPathOfAlias('application.update.uninstall').'.php');
		$this->setInstallEyiiVersion('0');
	}

	public function getNeedEyiiUpdate(){
		return $this->isNotInstallEyiiVersion(EYii::VERSION);
	}

	public function isNotInstallEyiiVersion($version){
		return version_compare($version, $this->installedEyiiVersion, '>');
	}

	public function setInstallEyiiVersion($step){
		Yii::app()->settings->set('EyiiVersion', $step);
	}

	public function getInstalledEyiiVersion(){
		return Yii::app()->settings->get('EyiiVersion', 0);
	}
}