<?php
class EUrlManager extends CUrlManager{
	public function createOriginUrl($route,$params=array(),$ampersand='&')
	{
		return $this->createPreffixUrl(Yii::app()->originBaseUrl, $route, $params,$ampersand);
	}

	public function createBackEndUrl($route, $params=array(),$ampersand='&'){
		return $this->createPreffixUrl(Yii::app()->backEndPreffix, $route, $params,$ampersand);
	}

	public function createPreffixUrl($preffix, $route, $params=array(),$ampersand='&'){		$baseUrl = $this->getBaseUrl();
		$this->setBaseUrl("/".$preffix);
		$url = Yii::app()->getOriginBaseUrl().$this->createUrl($route,$params,$ampersand);
		$this->setBaseUrl($baseUrl);
		return $url;	}



}