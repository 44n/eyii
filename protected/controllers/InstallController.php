<?php
class InstallController extends EController{
	public function init(){
		Yii::import('eyii.models.*');
	}

	public function backendIndex(){
		Yii::app()->user->access('eyii.Install');

		Yii::app()->install->completeStep();
	}


	public function backendInfo(){
		$model = new EyiiInfoModel;
		if(isset($_POST['EyiiInfoModel'])){
			if($model->save())
				Yii::app()->install->completeStep('info');
		}else{
		}

		$this->renderForm($model);
	}

	public function backendDB(){
		$model = new EyiiDbModel;
		if(isset($_POST['EyiiDbModel'])){
			$model->shema = $_POST['EyiiDbModel']['shema'];
			$model->attributes=$_POST['EyiiDbModel'];
			if($model->save())
				Yii::app()->install->completeStep('db');
		}else{
			$model->initDefault();
		}

		$this->renderForm($model);
	}

	public function backendAuto(){

		if(Yii::app()->install->installModules())
			Yii::app()->install->completeStep('auto');

		throw new CHttpException(500,'Internal Server Error');
	}

	public function backendComplete(){
	}
}