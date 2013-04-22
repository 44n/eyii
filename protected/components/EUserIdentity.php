<?php
class EUserIdentity extends CUserIdentity{	protected $_id;

	static function createKey($username, $password){
		return Yii::app()->user->createRootKey($username, $password);
	}

	static function getKey($defaultKey){
		return Yii::app()->settings->get('rootKey', $defaultKey);
	}

	public function getId(){
		return $this->_id;
	}

	public function authenticate(){
		$key = $this->createKey($this->username, $this->password);
		if($this->getKey($key) == $key){
			$this->username = 'Root';
			$this->_id = 0;
			$this->errorCode=self::ERROR_NONE;
		}else{
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}

		return !$this->errorCode;
	}
}