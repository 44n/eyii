<?php
class LoginController extends EController{
	public function backendIndex(){
		$model = new RootLoginFormModel;
		if(isset($_POST['RootLoginFormModel'])){
			$model->attributes=$_POST['RootLoginFormModel'];
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}


		$this->renderForm($model);
	}

	public function backendOut(){
		$this->redirect(Yii::app()->getOriginBaseUrl()."/");
	}
}