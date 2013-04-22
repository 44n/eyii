<?php
class LoginController extends EController{	public function init(){		Yii::import('eyii.models.*');	}
	public function backendIndex(){		$this->layoutData['title'] = Yii::t('eyii.login',"Authorization Form");
		$model = new RootLoginFormModel;
		if(isset($_POST['RootLoginFormModel'])){
			$model->attributes=$_POST['RootLoginFormModel'];
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}


		$this->renderForm($model);
	}

	public function backendOut(){		Yii::app()->user->logout();
		$this->redirect(Yii::app()->getOriginBaseUrl()."/");
	}
}