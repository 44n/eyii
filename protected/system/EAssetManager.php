<?php
class EAssetManager extends CAssetManager{	private $_baseUrl;
	private $_basePath;

	public function getBaseUrl()
	{
		if($this->_baseUrl===null)
		{
			$this->setBaseUrl(Yii::app()->originBaseUrl.'/'.self::DEFAULT_BASEPATH);
		}
		return $this->_baseUrl;
	}

	public function setBaseUrl($value)
	{
		$this->_baseUrl=rtrim($value,'/');
	}



	public function getBasePath()
	{
		if($this->_basePath===null)
		{
			$this->setBasePath(Yii::getPathOfAlias('webroot').DIRECTORY_SEPARATOR.self::DEFAULT_BASEPATH);
		}
		return $this->_basePath;
	}

	public function setBasePath($value)
	{
		if(($basePath=realpath($value))!==false && is_dir($basePath) && is_writable($basePath))
			$this->_basePath=$basePath;
		else
			throw new CException(Yii::t('yii','CAssetManager.basePath "{path}" is invalid. Please make sure the directory exists and is writable by the Web server process.',
				array('{path}'=>$value)));
	}}