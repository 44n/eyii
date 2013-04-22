<?php
class EComponentConfigurator extends CApplicationComponent{
	public $configFile;

	public function init(){
			$this->configFile = Yii::getPathOfAlias('eyii.config.components').'.php';

		if(file_exists($this->configFile))
			$this->_data = require($this->configFile);
		else
			$this->_data = array();

		parent::init();
	}

	public function get($name, $default = array()){
			return $this->_data[$name];
		return $default;
	}

	public function set($name, $data){
		$this->save();
	}

	protected function save(){
	}
}