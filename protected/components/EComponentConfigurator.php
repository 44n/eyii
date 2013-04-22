<?php
class EComponentConfigurator extends CApplicationComponent{	protected $_data;
	public $configFile;

	public function init(){		if($this->configFile == null)
			$this->configFile = Yii::getPathOfAlias('eyii.config.components').'.php';

		if(file_exists($this->configFile))
			$this->_data = require($this->configFile);
		else
			$this->_data = array();

		parent::init();
	}

	public function get($name, $default = array()){		if(isset($this->_data[$name]))
			return $this->_data[$name];
		return $default;
	}

	public function set($name, $data){		$this->_data[$name] = $data;
		$this->save();
	}

	protected function save(){		@file_put_contents($this->configFile,'<?php return '.var_export($this->_data, true).";");
	}
}