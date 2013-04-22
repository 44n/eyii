<?
class SettingsController extends EController{	public function init(){
		Yii::import('eyii.models.*');
	}

	public function backendInfo(){
		Yii::app()->user->access('eyii.Install');
		$model = new EyiiInfoModel;
		if(isset($_POST['EyiiInfoModel'])){
			$model->attributes=$_POST['EyiiInfoModel'];
			if($model->save())
				$this->redirect(array('info'));
		}else{
			$model->initDefault();
		}

		$this->layoutData['breadcrumb'] = array(Yii::t('eyii.menu',"Defaults"));

		$this->renderForm($model);
	}

	public function backendDB(){
		Yii::app()->user->access('eyii.Install');
		$model = new EyiiDbModel;
		if(isset($_POST['EyiiDbModel'])){
			$model->shema = $_POST['EyiiDbModel']['shema'];
			$model->attributes=$_POST['EyiiDbModel'];
			if($model->save())
				$this->redirect(array('db'));
		}else{
			$model->initDefault();
		}

		$this->layoutData['breadcrumb'] = array(Yii::t('eyii.menu',"DB Connection"));

		$this->renderForm($model);
	}

	public function backendClientScript(){
		Yii::app()->user->access('eyii.Settings');
		$model = new EClientScriptConfig;
		if(isset($_POST['EClientScriptConfig'])){
			$model->attributes=$_POST['EClientScriptConfig'];
			if($model->save())
				$this->redirect(array('clientScript'));
		}else{
			$model->initDefault();
		}

		$this->layoutData['breadcrumb'] = array(Yii::t('eyii.menu',"Client Script"));

		$this->renderForm($model);
	}

	public function backendRootProfile(){
		Yii::app()->user->accessRoot();

		$model = new RootConfigModel;
		if(isset($_POST['RootConfigModel'])){
			$model->attributes=$_POST['RootConfigModel'];
			if($model->save())
				$this->redirect(array('rootProfile'));
		}

		$this->layoutData['breadcrumb'] = array(Yii::t('eyii.rootProfile',"Change Root Profile"));

		$this->renderForm($model);
	}}