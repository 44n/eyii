<?php
class EWebApplication extends CWebApplication{	public  $name = 'EASY YII';

	public  $defaultController = "site";

	private $_isBackEnd = false;

	private $_preffix;

	private $_originBaseUrl;

	private $_language;

	public $sourceLanguage = 'en';

	public function __construct($config=null, $isBackEnd = false){		$this->isBackEnd = $isBackEnd;

		Yii::setApplication($this);

		$this->setBasePath(YII_BASE_PATH);

		Yii::setPathOfAlias('application',$this->getBasePath());
		Yii::setPathOfAlias('webroot', realpath(EYII_DOCUMENT_ROOT));
		Yii::setPathOfAlias('ext',$this->getBasePath().DIRECTORY_SEPARATOR.'extensions');


		$this->preinit();

		$this->initSystemHandlers();
		$this->registerCoreComponents();

		if(is_string($config))
			$config=require($config);

		$this->configure($config);
		$this->attachBehaviors($this->behaviors);
		$this->preloadFirst();
		$this->preloadComponents();
		$this->init();
	}

	protected function preloadFirst(){
		$this->getComponent('firewall');
		$this->getComponent('analytic');

		if(!$this->isInstall)
			$this->preload = array();

		if($this->isBackEnd)
			$this->getComponent('bootstrap');
	}

	public function end($status=0, $exit=true){
		$this->getComponent('cronManager')->run();

		parent::end($status, $exit);
	}

	public function getIsBackEnd(){
		return $this->_isBackEnd;
	}


	private $_isInstall;
	public function getIsInstall(){
		if($this->_isInstall === null){
			$this->_isInstall = $this->settings->get('isInstall', false);
		}

		return $this->_isInstall;
	}

	public function setIsInstall($v=true){
		$this->_isInstall = (bool)$v;
		$this->settings->set('isInstall', $this->_isInstall);
	}

	public function unlockFrontEnd($module = ''){		if(empty($module)){
			$this->settings->set('lockFrontEnd', array());
		}else{			$d = $this->settings->get('lockFrontEnd', array());
			unset($d[$module]);
			$this->settings->set('lockFrontEnd', $d);
		}
	}

	public function lockFrontEnd($module = '1'){		$d = $this->settings->get('lockFrontEnd', array());
		$d[$module] = "";
		$this->settings->set('lockFrontEnd', $d);
	}


	private $_isBlockFrontEnd;
	public function getIsBlockFrontEnd(){
		if($this->_isBlockFrontEnd === null){			if($this->isInstall == false){				$this->_isBlockFrontEnd = true;
				return true;
			}

			$ar = $this->settings->get('lockFrontEnd', array());
			$this->_isBlockFrontEnd = (empty($ar) == false);
		}

		return $this->_isBlockFrontEnd;
	}

	public function setIsBackEnd($v = true){
		$this->_isBackEnd = (bool)$v;
	}


	public function getPreffix(){		if($this->_preffix == null){
			$SCRIPT_ROOT = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
			$EYII_DOCUMENT_ROOT = Yii::getPathOfAlias('webroot');
			if($EYII_DOCUMENT_ROOT == $SCRIPT_ROOT){				$this->_preffix = false;
			}else{				$this->_preffix = substr($SCRIPT_ROOT, strlen($EYII_DOCUMENT_ROOT)+1);
			}
		}

		return $this->_preffix;
	}

	public function setPreffix($v){		if($v !== false){			$v = trim($v,'\\/');
		}elseif($v == ""){			$v = false;
		}
		$this->_preffix = $v;
	}

	public function getOriginBaseUrl(){		if($this->_originBaseUrl == null){
			$DOCUMENT_ROOT = realpath($_SERVER['DOCUMENT_ROOT']);
			$EYII_DOCUMENT_ROOT = Yii::getPathOfAlias('webroot');

			if($EYII_DOCUMENT_ROOT == $DOCUMENT_ROOT){				$this->_originBaseUrl = "";
			}else{				$this->_originBaseUrl = substr($EYII_DOCUMENT_ROOT, strlen($DOCUMENT_ROOT));
				$this->_originBaseUrl = trim($this->_originBaseUrl,'\\/');
				$this->_originBaseUrl = "/".str_replace(DIRECTORY_SEPARATOR,"/",$this->_originBaseUrl);
			}
		}
		return $this->_originBaseUrl;
	}

	public function getConfigPath($name){		return Yii::getPathOfAlias('private.configs.'.$name).'.php';
	}

	public function getLanguage(){
		return $this->_language===null ? $this->user->language : $this->_language;
	}

	public function setLanguage($language){
		$this->_language=$language;
	}

	public function getSecretKey(){
		return md5(Yii::getPathOfAlias('webroot'));
	}

	protected function registerCoreComponents(){

		$this->theme = '123$';

		parent::registerCoreComponents();

		$rules = array();
		$rulesPath = Yii::getPathOfAlias('eyii.config.urlManagerRules').'.php';
		if(file_exists($rulesPath))
			$rules = require($rulesPath);

		$components = array(
			'firewall'=>array(
				'class'=>'application.replaceable.FireWall',
			),
			'analytic'=>array(
				'class'=>'application.replaceable.Analytic',
			),
			'session'=>array(
				'class'=>'CHttpSession',
			),
			'assetManager'=>array(
				'class'=>'EAssetManager',
			),
			'fileManager'=>array(
				'class'=>'application.replaceable.FileManager',
			),
			'user'=>array(
				'class'=>'application.replaceable.WebUser',
			),
			'themeManager'=>array(
				'class'=>'EThemeManager',
				'themeClass' => 'ETheme'
			),
			'authManager'=>array( /*CPhpAuthManager*/
				'class'=>'application.replaceable.AuthManager',
			),
			'clientScript'=>array(
				'class'=>'application.components.EClientScript',
			),
			'db'=>array(
				'class'=>'CDbConnection',
			),
			'errorHandler'=>array(
				'class'=>'CErrorHandler',
				'errorAction'=>'site/error',
			),
			'urlManager'=>array(
				'class'=>'EUrlManager',
				'urlFormat'=>'path',
				'caseSensitive'=>false,
				'showScriptName'=>false,
				'rules'=>$rules
			),
			'cache' => array(
				'class'=>'CFileCache',
			),
			'request'=>array(
				'class'=>'EHttpRequest',
			),
			'settings'=>array(
				'class'=>'ESettings',
				'cachePath'=>Yii::getPathOfAlias('private.configs.app'),
			),
			'bootstrap' => array(
				'class' => 'application.vendors.yiibooster.components.Bootstrap',
				'responsiveCss' => true,
			),
			'componentConfigurator' => array(
				'class' => 'application.components.EComponentConfigurator',
			),
			'cronManager'=>array(
				'class' => 'application.components.ECronManager',
			),
			'templateManager'=>array(
				'class' => 'application.components.ETemplateManager',
			),
			'layoutMenu'=>array(
				'class' => 'ELayoutMenu'
			),
		);

		$this->setComponents($components);

		$components = array();
		$componentsPath = Yii::getPathOfAlias('eyii.config.components').'.php';
		if(file_exists($componentsPath))
			$components = require($componentsPath);

		if($this->isBackEnd){
			$modulesExtensionPaths = array();
			$modulesExtensionPaths['eyii'] = 'eyii.messages';
			foreach($this->modules as $id=>$data){
				$modulesExtensionPaths[$id] = $id.'.messages';
			}

			$components['messages'] = array(
				'extensionPaths' => $modulesExtensionPaths,
			);

			$components['install'] = array(
				'class' => 'application.components.EInstaller',
			);
		}

		$this->setComponents($components);
	}

	public function setComponent($id, $component, $merge=true){		if(is_array($component)){			if(!empty($component['needPreload'])){				$this->preload[] = $id;
			}
			unset($component['needPreload']);
		}
		parent::setComponent($id, $component, $merge);
	}

	public function createOriginUrl($route, $params=array(), $ampersand='&')
	{
		return $this->getUrlManager()->createOriginUrl($route, $params, $ampersand);
	}

	private $_initModules = false;
	private function initModules(){		if($this->_initModules)return;
		$this->_initModules = true;


		$setModules = Yii::app()->cache->get('WebAppModules');
		if($setModules == false || YII_DEBUG){
			$setModules = array();
			$dir = Yii::getPathOfAlias('application.modules');
			$handle=opendir($dir);
			while(($file=readdir($handle))!==false)
			{
				if($file==='.' || $file==='..')
					continue;
				$path=$dir.DIRECTORY_SEPARATOR.$file;
				if(is_dir($path) && !$this->hasModule($file)){					$setModules[$file] = array('class'=>"application.modules.".$file.".".ucfirst($file)."Module");
				}

			}
			closedir($handle);
			Yii::app()->cache->set('WebAppModules',$setModules, 600);
		}

		if(!empty($setModules))$this->modules = $setModules;
	}

	public function getModule($id){		$m = parent::getModule($id);
		if($m !== null)return $m;
		if($this->_initModules == false){			$this->initModules();
			return $this->getModule($id);
		}
	}

	public function getModules()
	{
		if($this->_initModules == false){
			$this->initModules();
		}
		return parent::getModules();
	}

	public function hasModule($id)
	{
		$has = parent::hasModule($id);
		if($has)return $has;
		if($this->_initModules == false){
			$this->initModules();
			return $this->hasModule($id);
		}
	}

	private $_supportEmail;

	public function getSupportEmail(){		if($this->_supportEmail == null){			$this->_supportEmail = 'support@'.$this->request->serverName;
			$this->_supportEmail = $this->settings->get('supportEmail', $this->_supportEmail);
		}
		return $this->_supportEmail;
	}

	public function setSupportEmail($v){		$this->settings->set('supportEmail', $v);
		$this->_supportEmail = $v;
	}

	private $_backEndPreffix;

	public function getbackEndPreffix(){
		if($this->_backEndPreffix == null){

			$this->_backEndPreffix = 'admin';
			if($this->isBackEnd)
				$this->_backEndPreffix = $this->preffix;

			$this->_backEndPreffix = $this->settings->get('backEndPreffix', $this->_backEndPreffix);
		}
		return $this->_backEndPreffix;
	}

	public function setbackEndPreffix($v){
		$this->settings->set('backEndPreffix', $v);
		$this->_backEndPreffix = $v;
	}

	private $_publicUrl;
	function getPublicUrl(){
		if($this->_publicUrl == null){
			$DOCUMENT_ROOT = realpath($_SERVER['DOCUMENT_ROOT']);
			$PUBLIC_ROOT = Yii::getPathOfAlias('public');
			$this->_publicUrl = substr($PUBLIC_ROOT, strlen($DOCUMENT_ROOT));
			$this->_publicUrl = trim($this->_publicUrl,'\\/');
			$this->_publicUrl = "/".str_replace(DIRECTORY_SEPARATOR,"/",$this->_publicUrl);
		}

		return $this->_publicUrl;
	}

	function publicUrl($file = ""){		$file = str_replace("\\","/", $file);
		$file = trim($file, "/");
		return $this->publicUrl."/".$file;
	}
}