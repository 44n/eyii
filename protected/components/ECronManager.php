<?php

class ECronManager extends CApplicationComponent{
	public $runInActions = true;

	public function init(){		parent::init();
		Yii::import('eyii.models.ECronAgent');	}

	public function createAgent($module, $function, $interval, $params = array()){		$agent = new ECronAgent;
		$agent->module = $module;
		$agent->function = $function;
		$agent->interval = $interval;
		$agent->params = $params;
		if($agent->save()){			return $agent->id;		}else{			return false;		}
	}

	public function restartExpiredAgents(){		$agents = ECronAgent::model()->progress()->expired()->findAll();
		foreach($agents as $agent)
			$agent->reStart();	}

	public function getAgent($id){		return ECronAgent::model()->findByPk($id);	}

	public function getRunAgent($id){
		return ECronAgent::model()->progress()->findByPk($id);
	}

	public function getModuleAgents($module, $function = ""){		if(!empty($function))
			return ECronAgent::model()->findAllByAttributes(array('module'=>$module, 'function' => $function));		return ECronAgent::model()->findAllByAttributes(array('module'=>$module));	}

	public function run(){		if(!$this->runInActions)return;
		if(!Yii::app()->isInstall)return;
		if(!Yii::app()->cache->get('cronManagerStop'))return;
		Yii::app()->cache->set('cronManagerStop', true, 60);

		$url =  Yii::app()->getRequest()->getHostInfo().Yii::app()->getUrlManager()->createBackEndUrl('eyii/cron/run');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		$r = curl_exec($ch);
		curl_close($ch);
		unset($r);	}

	public function runAgents(){		$this->runInActions = true;
		$agents = ECronAgent::model()->enable()->needStart()->findAll();
		foreach($agents as $agent)
			$agent->start();	}

	public function runAgent($id){		$url =  Yii::app()->getRequest()->getHostInfo().Yii::app()->getUrlManager()->createBackEndUrl('eyii/cron/runagent', array('id'=>$id));
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		$r = curl_exec($ch);
		curl_close($ch);
		unset($r);	}

}