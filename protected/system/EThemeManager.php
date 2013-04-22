<?php
class EThemeManager extends CThemeManager{	private $_basePath;
	public function getTheme($name)
	{
		$theme = parent::getTheme($name);
		if($theme == null)return $this->getDefaultTheme();
		return $theme;
	}

	public function getDefaultTheme(){		$name = 'default';
		$themePath=$this->getBasePath().DIRECTORY_SEPARATOR.$name;
		$class=Yii::import($this->themeClass, true);
		return new $class($name,$themePath,$this->getBaseUrl().'/'.$name);	}

	public function getBasePath()
	{
		if($this->_basePath===null)
			$this->setBasePath(Yii::getPathOfAlias('webroot.'.self::DEFAULT_BASEPATH));
		return $this->_basePath;
	}

	public function setBasePath($value)
	{
		$this->_basePath=realpath($value);
		if($this->_basePath===false || !is_dir($this->_basePath))
			throw new CException(Yii::t('yii','Theme directory "{directory}" does not exist.',array('{directory}'=>$value)));
	}}