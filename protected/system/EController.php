<?class EController extends CController{

	public $layoutData;

	protected function actionsFilters(){		$filters = $this->filters();

		$found = false;
		foreach($filters as $key=>$filter){			if(stripos($filter[0],'EXssFilter') !== false){				$found = true;
			}
		}

		if(!$found){			$filters[] = array('EXssFilter');
		}
		return $filters;
	}

	public function run($actionID)
	{
		if(($action=$this->createAction($actionID))!==null)
		{
			if(($parent=$this->getModule())===null)
				$parent=Yii::app();
			if($parent->beforeControllerAction($this,$action))
			{
				$this->runActionWithFilters($action,$this->actionsFilters());
				$parent->afterControllerAction($this,$action);
			}
		}
		else
			$this->missingAction($actionID);
	}


	public function createBackEndAction($actionID){


		if($actionID==='')
			$actionID=$this->defaultAction;
		if(method_exists($this,'backend'.$actionID) && strcasecmp($actionID,'s')) // we have actions method
			return new EBackEndAction($this,$actionID);

		return false;
	}

	public function createAction($actionID){
		if(Yii::app()->isBackEnd){
			$action = $this->createBackEndAction($actionID);
			if(!empty($action)){
				return $action;
			}
		}

		return parent::createAction($actionID);
	}

	function renderForm($model, $return=false){		return $this->render('eyii.views.constructors.form',array('model'=>$model), $return);
	}

	function renderPartForm($model, $return=false){
		return $this->renderPartial('eyii.views.constructors.partForm',array('model'=>$model), $return);
	}

	function renderWidget($model, $return=false){
		return $this->render('eyii.views.constructors.widget',array('model'=>$model), $return);
	}

	function renderPartWidget($model, $return=false){
		return $this->renderPartial('eyii.views.constructors.partWidget',array('model'=>$model), $return);
	}

	protected $_template;
	public function getTemplate(){		if($this->_template == null){			$templateName = $this->id.".".$this->action->id;

			if(!empty($this->module->id)){
				$templateName = $this->module->id.".".$templateName;
			}
			$this->_template = Yii::app()->templateManager->get($templateName);
		}

		return $this->_template;
	}

}