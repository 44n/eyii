<?php
class EWebModule extends CWebModule{

	function getSettings(){
		if($this->_settings === null){
			$this->_settings = new ESettings;
			$this->_settings->cachePath = Yii::getPathOfAlias('private.configs.'.$this->id);
			$this->_settings->init();
		}

		return $this->_settings;
	}

	public function moduleInformation(){
			'lastModif' => date('Y-m-d'),
			'systemId' => '',
			'version' => '0',/*if this version isNotInstall then system show can update*/
			'info' => ''
		);
	}

	function extUploadUpdate(){

	function extUpdateAvailable(){

		$res = file_get_contents($info['api']."&type=version");
		if(!empty($res)){

		$version = $this->settings->get('extVersion',$info['version']);

		return $this->isNotInstall($version);

	public function getNeedModuleUpdate(){
		return $this->isNotInstall($info['version']);
	}

	public function isNotInstall($v){
		return version_compare($v, $this->installedVersion, '>');
	}

	public function setInstallVersion($v){
		$this->settings->set('installVersion', $v);
	}

	public function getInstalledVersion(){
		return $this->settings->get('installVersion', 0);
	}

	public function doInstall(){
	}

	public function doUnInstall(){
		$this->unInstall(Yii::app()->install);
	}

	protected function install($installer){}
	protected function unInstall($installer){}

	public function getBackUpRules(){return array();	}

	public function getLayoutMenu(){return array();	}
}