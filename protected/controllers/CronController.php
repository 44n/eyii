<?
class CronController extends EController{

	private function abortUser(){		$wall =  Yii::app()->getRequest()->getHostInfo().Yii::app()->getUrlManager()->createBackEndUrl('eyii/cron/wall');

		set_time_limit(0);
		ignore_user_abort(true);

		header( "Location: ".$wall ) ;
		ob_end_clean();
		header("Connection: close");
		ob_start();
		header("Content-Length: 0");
		ob_end_flush();
		flush();
		if(function_exists('fastcgi_finish_request'))
			fastcgi_finish_request();
		session_write_close();

		sleep(5);
	}

	public function backendRun(){
		Yii::app()->cronManager->runInActions = false;
		$this->abortUser();

		Yii::app()->cronManager->runAgents();
		Yii::app()->cronManager->restartExpiredAgents();
	}

	public function backendWall(){		Yii::app()->cronManager->runInActions = false;
	}

	public function backendRunAgent($id){		Yii::app()->cronManager->runInActions = false;
		$agent = Yii::app()->cronManager->getRunAgent($id);
		/*$agent = Yii::app()->cronManager->getAgent($id);*/
		if($agent == null)return;
		$this->abortUser();



		$agent->runAgent();	}

	public function backendList(){		Yii::app()->user->access('eyii.Install');
		/*Yii::app()->cronManager->createAgent('eyii','testAgent', '600');*/
		/*$agents = Yii::app()->cronManager->getModuleAgents('eyii');
		foreach($agents as $agent)
			$agent->delete();*/

		/*$agents = Yii::app()->cronManager->getModuleAgents('eyii');
		foreach($agents as $agent){
			$agent->interval = 60;
			$agent->restart();
		}*/


		/*Yii::app()->cronManager->runAgents();*/





		/*$agents = ECronAgent::model()->enable()->needStart()->findAll();
		var_export(count($agents));*/


		$model = new ECronAgent;
		$model->unsetAttributes();
		if(isset($_GET['ECronAgent']))
			$model->attributes=$_GET['ECronAgent'];

		$this->layoutData['breadcrumb'] = array('Cron Agents');
		$this->renderWidget($model);

	}}