<?php
class DefaultWebUser extends CWebUser{

	public $loginUrl = array('login');

	public function getLanguage(){
		if(EYII_BACKEND){			return 'ru';
			return Yii::app()->request->getCookie('backEndLanguage', Yii::app()->sourceLanguage);		}

		return Yii::app()->sourceLanguage;
	}


	public function checkAccess($operation,$params=array(),$allowCaching=true){		if($this->getIsRoot()){
			return true;
		}

		if(!Yii::app()->IsInstall){			return false;		}

		return parent::checkAccess($operation,$params,$allowCaching);
	}

	public function getIsRoot(){		if(!$this->isGuest && $this->id == 0){
			return true;
		}
		return false;
	}

	public function access($operation,$params=array(),$allowCaching=true){
		if($this->checkAccess($operation,$params,$allowCaching)){
			return true;
		}

		if($this->isGuest)
			$this->loginRequired();

		throw new CHttpException(403,Yii::t('eyii','Access denied.'));
	}

	public function accessRoot(){		if($this->getIsRoot())
			return true;

		if($this->isGuest)
			$this->loginRequired();

		throw new CHttpException(403,Yii::t('eyii','Access denied.'));	}

	private $_rights;
	public function getRights(){
		if($this->isGuest){
			return array('Guest');
		}elseif($this->getIsRoot()){			return array('Root Administrator');
		}elseif($this->_rights === null){
			$this->_rights = Yii::app()->authManager->getAuthItems(null,$this->id);
			if(!isset($this->_rights['User']))$this->_rights['User'] = "";
			$this->_rights = array_keys($this->_rights);
			sort($this->_rights);
			reset($this->_rights);
		}

		return $this->_rights;
	}

	private $_rightsString;
	public function getRightsString(){

		if($this->_rightsString === null){
			$this->_rightsString = implode("|",$this->rights);
		}

		return $this->_rightsString;
	}

	public function createRootKey($username, $password){		return md5($username.Yii::app()->secretKey.$password);	}
}

if(!EYii::includeDefaultComponent('WebUser','user')){
	class WebUser extends DefaultWebUser{}
}