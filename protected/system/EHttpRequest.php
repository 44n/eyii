<?php
class EHttpRequest extends CHttpRequest{
		'HTTP_CLIENT_IP',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_FORWARDED',
		'HTTP_X_CLUSTER_CLIENT_IP',
		'HTTP_FORWARDED_FOR',
		'HTTP_FORWARDED',
		'REMOTE_ADDR'
	);

	private $_userHostAddress;
	public function getUserHostAddress(){
		if($this->_userHostAddress !== null)return $this->_userHostAddress;
		foreach ($this->ipSerchIn as $key){
			if (array_key_exists($key, $_SERVER) === true) {
				foreach (explode(',', $_SERVER[$key]) as $ip) {
					if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
						$this->_userHostAddress = $ip;
						return $ip;
					}
				}
			}
		}

		$this->_userHostAddress = '127.0.0.1';
		return $this->_userHostAddress;
	}


	function getCookie($name, $default = false){
		return Yii::app()->request->cookies[$name]->value;
	}

	function setCookie($name, $value, $expire = 0, $path = '/', $options=array()){
		$options['path'] = $path;
		Yii::app()->request->cookies[$name] = new CHttpCookie($name, $value, $options);
	}

	public function getServerName(){
	}
}