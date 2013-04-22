<?php
class SiteController extends EController{	public function actions()
	{
		return array(
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
		);
	}

	public function actionIndex(){

		if($this->template->notCached('index')){
			$this->template->render(array(), EExpire::DAY);
		}
	}

	public function backendIndex(){
		Yii::app()->user->access('eyii.BackEnd');
		if(!Yii::app()->isInstall){
			$this->redirect(array('install/index'));
		}


		$this->render('index',array('time' => microtime(true) - YII_BEGIN_TIME));
	}

	public function actionError(){
		if($error=Yii::app()->errorHandler->error)
		{
			echo $error['code'].":".$error['message'];

			/*
			if(Yii::app()->request->isAjaxRequest){
				echo $error['code'].":".$error['message'];
			}else{
				$this->template->render(array('errorCode' => $error['code'], 'errorMessage' => $error['message']));
			}*/
		}
	}}