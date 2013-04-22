<?php
class ETemplate extends CComponent{	protected $_templateInfo;
	protected $_data = array();
	protected $_output='';
	static $gadget = array();
	static $helper = array();


	public function __isset($name){
		$getter='get'.$name;
		if(method_exists($this,$getter))
			return $this->$getter()!==null;

		return isset($this->_data[$name]);
	}

	public function __unset($name){
		$setter='set'.$name;
		if(method_exists($this,$setter))
			$this->$setter(null);

		unset($this->_data[$name]);
	}

	public function __get($name){
		$getter='get'.$name;
		if(method_exists($this,$getter))
			return $this->$getter();

		if(isset($this->_data[$name])){
			return $this->_data[$name];
		}
	}

	public function __set($name,$value){
		$setter='set'.$name;
		if(method_exists($this,$setter))
			return $this->$setter($value);

		return $this->_data[$name] = $value;
	}


	function getEnableCaching(){
		return (empty($this->_templateInfo['enableCaching']))?false:true;
	}

	function getTemplateName(){
		return $this->_templateInfo['templateName'];
	}

	function setTemplateName($template){
		if(empty($this->_templateInfo['baseTemplateName']))
			$this->_templateInfo['baseTemplateName'] = $template;
		$this->_templateInfo['templateName'] = $template;
	}

	function getBaseTemplateName(){		return $this->_templateInfo['baseTemplateName'];	}

	function getTemplateFileName(){
		if(isset($this->_templateInfo['templateFileName']))return $this->_templateInfo['templateFileName'];
		$this->_templateInfo['templateFileName'] = Yii::app()->theme->getTemplateFile($this->getTemplateName());
		return $this->_templateInfo['templateFileName'];
	}

	function setTemplateCacheId($id='', $accessSensitivity = false, $userSensitivity = false){
		$this->_templateInfo['enableCaching'] = true;
		$this->_templateInfo['cacheId']['id'] = $id;
		$this->_templateInfo['cacheId']['accessSensitivity'] = $accessSensitivity;
		$this->_templateInfo['cacheId']['userSensitivity'] = $userSensitivity;
	}

	public function notCached($id='', $accessSensitivity = false, $userSensitivity = false){		$this->setTemplateCacheId($id, $accessSensitivity, $userSensitivity);

		if($this->isCached()){			$this->render();
			return false;
		}

		return true;
	}

	function setCachePrefix($prefix){		$this->_templateInfo['cacheId']['cachePrefix'] = $prefix;
	}

	public function getCacheKey(){
		if(isset($this->_templateInfo['cacheKey']))return $this->_templateInfo['cacheKey'];

		if(!empty($this->_templateInfo['cacheId']['userSensitivity'])){
			if(!Yii::app()->user->isGuest)
				$this->_templateInfo['cacheId']['userSensitivity'] = Yii::app()->user->id;
		}elseif(!empty($this->_templateInfo['cacheId']['accessSensitivity'])){
			$this->_templateInfo['cacheId']['accessSensitivity'] = Yii::app()->user->getRightsString();
		}

		$this->_templateInfo['cacheId']['baseTemplateName'] = $this->getBaseTemplateName();
		$this->_templateInfo['cacheId']['templateName'] = $this->getTemplateName();

		$this->_templateInfo['cacheId']['templateFileName'] = $this->getTemplateFileName();
		$this->_templateInfo['cacheId']['templateFileNameMTime'] = filemtime($this->getTemplateFileName());

		$this->_templateInfo['cacheKey'] = sha1(serialize($this->_templateInfo['cacheId']));

		return $this->_templateInfo['cacheKey'];
	}

	public function getCacheFileName(){
		if(empty($this->_templateInfo['cacheFileName'])){
			$this->_templateInfo['cacheFileName'] = $this->templateCachePath.DIRECTORY_SEPARATOR.$this->cacheKey.".php";
		}
		return $this->_templateInfo['cacheFileName'];
	}

	public function getTemplateCachePath(){
		if(empty($this->_templateInfo['templateCachePath'])){
			$this->_templateInfo['templateCachePath'] = $this->templateOriginCachePath.DIRECTORY_SEPARATOR.$this->templateName.DIRECTORY_SEPARATOR.Yii::app()->theme->name;
		}

		return $this->_templateInfo['templateCachePath'];
	}

	public function getTemplateOriginCachePath(){
		if(empty($this->_templateInfo['templateOriginCachePath'])){
			$this->_templateInfo['templateOriginCachePath'] = Yii::app()->templateManager->cachePath.DIRECTORY_SEPARATOR.str_replace('.',DIRECTORY_SEPARATOR, $this->baseTemplateName);
		}

		return $this->_templateInfo['templateOriginCachePath'];
	}

	public function isCached(){
		if(!isset($this->_templateInfo['isCached'])){
			if(!$this->enableCaching){
				$this->_templateInfo['isCached'] = false;
				return false;
			}

			if(!file_exists($this->cacheFileName)){
				$this->clearThisCache();
				$this->_templateInfo['isCached'] = false;
				return false;
			}


			$data = Yii::app()->cache->get($this->cacheKey);
			if($data === false){
				$this->_templateInfo['isCached'] = false;
				return false;
			}

			if(!$this->checkCacheFileSensitivity($data['fileSensitivity'])){
				$this->clearThisCache();
				$this->_templateInfo['isCached'] = false;
				return false;
			}

			$this->_templateInfo['isCached'] = true;
		}

		return $this->_templateInfo['isCached'];
	}

	protected function checkCacheFileSensitivity($data){
		foreach($data as $file){
			if(!file_exists($file['name']))return false;
			if(isset($file['mtime']))if($file['mtime'] != @filemtime($file['name']))return false;
		}
		return true;
	}

	public function addCacheFileSensitivity($file, $type='exist'){
		if($type == 'exist'){
			$this->_templateInfo['fileSensitivity'][] = array('name'=>$file);
		}else{
			$this->_templateInfo['fileSensitivity'][] = array('name'=>$file,'mtime' => @filemtime($file));
		}
	}

	public function clearThisCache(){
		Yii::app()->cache->delete($this->cacheKey);
		@unlink($this->cacheFileName);
	}

	protected function saveCache($cacheExpire, $cacheDependency){

		if(empty($this->_templateInfo['fileSensitivity'])){
			$data['fileSensitivity'] = array();
		}else{
			$data['fileSensitivity'] = $this->_templateInfo['fileSensitivity'];
		}

		Yii::app()->cache->set($this->cacheKey, $data, $cacheExpire, $cacheDependency);
		$dir = dirname($this->cacheFileName);
		if(!is_dir($dir))
			mkdir($dir, 0777, true);

		file_put_contents($this->cacheFileName, $this->_output);
		$this->_output = '';
		$this->_templateInfo['isCached'] = true;
	}

	public function fetch($assign=array(),$expire=0,$dependency=null, $returnBool=false){
		if(!empty($assign))$this->assign($assign);
		if($expire < 1){
			$expire = EExpire::HOUR;
		}



		if($this->isCached()){
			@touch($this->cacheFileName);
			ob_start();
			ob_implicit_flush(false);
			self::includeTemplateFile($this,$this->cacheFileName);
			$this->_output = ob_get_clean();
		}else{
			ob_start();
			ob_implicit_flush(false);
			self::includeTemplateFile($this,$this->templateFileName);
			$this->_output = ob_get_clean();

			if(($layout=$this->getLayoutFileName())!==false){
				$this->addCacheFileSensitivity($layout,'mtime');
				ob_start();
				ob_implicit_flush(false);
				self::includeTemplateFile($this, $layout);
				$this->_output = ob_get_clean();
			}

			if($this->enableCaching){
				$this->saveCache($expire, $dependency);
				ob_start();
				ob_implicit_flush(false);
				self::includeTemplateFile($this,$this->cacheFileName);
				$this->_output = ob_get_clean();
			}
		}

		if($returnBool)return true;
		return $this->_output;
	}

	public function render($assign=array(),$expire=0, $dependency=null){
		$this->fetch($assign, $expire, $dependency, true);
		$this->head()->render($this->_output);
		$this->showContent();
	}

	public function assign($data,$value=""){
		if(is_array($data)){
			foreach($data as $key=>$value){
				$this->$key = $value;
			}
		}elseif(is_string($data)){
			$this->$data=$value;
		}
	}

	public function getLayout(){		return (empty($this->_templateInfo['templateLayout']))?false:$this->_templateInfo['templateLayout'];	}

	public function setLayout($layout){
		$this->_templateInfo['templateLayout'] = $layout;
	}

	private function getLayoutFileName(){
		$layout = $this->getLayout();
		if($layout === false)return false;
		$file = Yii::app()->theme->getTemplateFile($layout);
		if(!file_exists($file))return false;
		return $file;
	}

	static function includeTemplateFile($t,$__includeTemplateFile){
		require($__includeTemplateFile);
	}

	public function showContent(){
		echo $this->_output;
		$this->_output = '';
	}



	protected function findFile($name, $type){
		$name = trim($name, "\\/");
		$name = str_replace(DIRECTORY_SEPARATOR,"/",$name);

		$webroot = Yii::getPathOfAlias('webroot');
		$find = str_replace("/",DIRECTORY_SEPARATOR,$name);
		$fullName = $webroot.DIRECTORY_SEPARATOR.$find;
		if(file_exists($fullName)){
			return Yii::app()->originBaseUrl."/".$name;
		}

		switch($type){
			case 'js':
				return Yii::app()->theme->getJsUrl($name);
			break;
			case 'css':
				return Yii::app()->theme->getCssUrl($name);
			break;
			case 'img':
				return Yii::app()->theme->getImgUrl($name);
			break;
		}
	}

	public function cssPath($name){
		return $this->findFile($name, 'css');
	}

	public function jsPath($name){
		return $this->findFile($name, 'js');
	}

	public function imgPath($name){
		return $this->findFile($name, 'img');
	}

	public function addCss($name, $media=""){
		if(!$this->isCached()&&$this->enableCaching){
			echo '<?$t->addCss('.var_export($name,true).', '.var_export($media,true).');?>';
			return;
		}

		$file = $this->findFile($name, 'css');

		Yii::app()->clientScript->registerCssFile($file, $media);

	}

	public function addCssContent($id, $content, $media=""){		if(!$this->isCached()&&$this->enableCaching){
			echo '<?$t->addCssContent('.var_export($id,true).', '.var_export($content,true).', '.var_export($media,true).');?>';
			return;
		}

		Yii::app()->clientScript->registerCss($id,$content,$media);	}

	public function beginCss(){
		ob_start();
		ob_implicit_flush(false);
	}

	public function endCss($id, $media=""){
		$content = ob_get_clean();
		$content = trim($content);
		if(!empty($content)){
			$this->addCssContent($id, $content, $media);
		}
	}


	public function addJs($name, $pos=null){
		if(!$this->isCached()&&$this->enableCaching){
			echo '<?$t->addJs('.var_export($name,true).','.var_export($pos,true).');?>';
			return;
		}

		$file = $this->findFile($name, 'js');
		Yii::app()->clientScript->registerScriptFile($file, $pos);

	}

	public function addCoreJs($name){		if(!$this->isCached()&&$this->enableCaching){
			echo '<?$t->addCoreJs('.var_export($name,true).');?>';
			return;
		}
		Yii::app()->clientScript->registerCoreScript($name);	}

	public function addJsContent($id,$content,$pos=null){
		if(!$this->isCached()&&$this->enableCaching){
			echo '<?$t->addJsContent('.var_export($id,true).', '.var_export($content,true).', '.var_export($pos,true).');?>';
			return;
		}		Yii::app()->clientScript->registerScript($id,$content,$pos);	}

	public function beginJs(){
		ob_start();
		ob_implicit_flush(false);
	}

	public function endJs($id, $pos=null){
		$content = ob_get_clean();
		$content = trim($content);
		if(!empty($content)){
			$this->addJsContent($id, $content, $pos);
		}
	}

	public function block($templateName){
		$file = Yii::app()->theme->getTemplateFile($templateName);
		$this->addCacheFileSensitivity($file,'mtime');
		self::includeTemplateFile($this,$file);
	}


	public function template($templateName, $data=array()){
		if(!$this->isCached()&&$this->enableCaching){
			echo '<?$t->template('.var_export($templateName,true).','.var_export($data,true).');?>';
			return;
		}

		$template = Yii::app()->templateManager->get($templateName);
		$accessSensitivity = false;
		$userSensitivity = false;

		if(isset($data['cacheAccessSensitivity'])){			$accessSensitivity = $data['cacheAccessSensitivity'];
			unset($data['cacheAccessSensitivity']);		}

		if(isset($data['cacheUserSensitivity'])){
			$userSensitivity = $data['cacheUserSensitivity'];
			unset($data['cacheUserSensitivity']);
		}

		if(isset($data['cacheExpire'])){
			$cacheExpire = $data['cacheExpire'];
			unset($data['cacheExpire']);
		}

		$template->setTemplateCacheId($templateName, $accessSensitivity, $userSensitivity);
		$template->fetch($data, $cacheExpire, null, true);
		$template->showContent();
	}

	static $headContructor;
	public function head(){		if(ETemplate::$headContructor == null){			ETemplate::$headContructor = new ETemplateHeadContructor;
			ETemplate::$headContructor->init();
		}

		return ETemplate::$headContructor->templateCall($this);	}

	public function createUrl($route,$params=array(),$ampersand='&'){
		return Yii::app()->createUrl($route,$params,$ampersand);
	}

	public function publicUrl($file = ""){
		return Yii::app()->publicUrl($file);
	}

	public function gadget($name, $parameters=array()){		if(!$this->isCached()&&$this->enableCaching){
			echo '<?$t->gadget('.var_export($name,true).','.var_export($parameters,true).');?>';
			return;
		}

		list($moduleId, ,$action) = explode(".",$name);

		if(isset(ETemplate::$gadget[$moduleId])){
			return ETemplate::$gadget[$moduleId]->runGadget($this, $action, $parameters);
		}

		$className = Yii::import($moduleId.'.components.'.ucfirst($moduleId).'Gadgets');

		ETemplate::$gadget[$moduleId] = new $className($moduleId);

		return ETemplate::$gadget[$name]->runGadget($this, $action, $parameters);
	}

	public function helper($name, $parameters=array()){
		list($moduleId, ,$action) = explode(".",$name);

		if(isset(ETemplate::$helper[$moduleId])){
			return ETemplate::$helper[$moduleId]->runHelper($this, $action, $parameters);
		}

		$className = Yii::import($moduleId.'.components.'.ucfirst($moduleId).'Helpers');

		ETemplate::$helper[$moduleId] = new $className($moduleId);

		return ETemplate::$helper[$name]->runHelper($this, $action, $parameters);	}
}
