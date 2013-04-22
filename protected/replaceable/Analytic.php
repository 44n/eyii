<?php

class DefaultAnalytic extends CApplicationComponent{	public function init(){
		parent::init();

		if(!Yii::app()->isInstall)return;

		$this->collectData();
	}

	protected function collectData(){		/**/	}}

if(!EYii::includeDefaultComponent('Analytic')){
	class Analytic extends DefaultAnalytic{}
}