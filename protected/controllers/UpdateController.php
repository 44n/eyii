<?php
class UpdateController extends EController{
	function getModuleInfo(){		$info = array();
		$info[] = array(
			'id'=>'eyii',
			'lastModif' => EYii::VERSION_DATE,
			'systemId' => 'eyii',
			'version' => EYii::VERSION,
			'installedVersion'=> Yii::app()->install->installedEyiiVersion,
			'info' => ''
		);
		foreach(Yii::app()->modules as $id => $module){			$m = Yii::app()->getModule($id);
			if($m !== null){
				$i = $m->moduleInformation();
				$i['id'] = $id;
				$i['installedVersion'] = $m->installedVersion;
				$info[] = $i;
			}
		}

		return $info;
	}

	public function backendIndex(){		Yii::app()->user->access('eyii.Install');
		$this->layoutData['breadcrumb'] = array(Yii::t('eyii.menu','Site Update'));
		$this->layoutData['operations'] = array(
			 array('label'=>Yii::t('eyii.defaults','Update'), 'url' => Yii::app()->createUrl('update/install'))
		);
		$this->render('modules',array('list'=>$this->getModuleInfo()));
	}

	public function backendInstall(){		Yii::app()->user->access('eyii.Install');
		if(Yii::app()->install->getNeedEyiiUpdate())
			Yii::app()->install->completeStep('re');
		else
			Yii::app()->install->completeStep('reAuto');
	}



}