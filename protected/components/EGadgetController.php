<?
class EGadgetAction extends CInlineAction{

	protected function runWithParamsInternal($object, $method, $params){		$ps=array();
		foreach($method->getParameters() as $i=>$param){
			$name=$param->getName();
			if(isset($params[$name]))
				$ps[]=$params[$name];
			else if($param->isDefaultValueAvailable())
				$ps[]=$param->getDefaultValue();
			else
				return false;
		}

		$method->invokeArgs($object,$ps);
		return true;
	}
}

class EGadgetController extends EController{	private $_actionParams;
	public $parentTemplate;


	public function __construct($moduleName){		$module = Yii::app()->getModule($moduleName);
		parent::__construct('gadget',$module);
		$this->init();	}

	public function createAction($actionID)
	{
		if($actionID==='')
			$actionID=$this->defaultAction;
		if(method_exists($this,'action'.$actionID) && strcasecmp($actionID,'s')) // we have actions method
			return new EGadgetAction($this,$actionID);
		else{
			$action=$this->createActionFromMap($this->actions(),$actionID,$actionID);
			if($action!==null && !method_exists($action,'run'))
				throw new CException(Yii::t('yii', 'Action class {class} must implement the "run" method.', array('{class}'=>get_class($action))));
			return $action;
		}
	}

	public function getTemplate(){		if($this->_template === null){			$this->_template = Yii::app()->template->getGadget($this->module->id.".".$this->id.".".$this->action->id);
			$this->_template->assign($this->actionParams);
		}
		return $this->_template;
	}

	public function getActionParams(){
		return (empty($this->_actionParams))?array():$this->_actionParams;
	}

	public function runGadget(&$template, $actionID, $parameters=array()){
		$this->parentTemplate = $template;


		$this->_actionParams = $parameters;
		$this->_template = null;

		if(is_string($actionID)){			$this->run($actionID);		}else{			$this->missingAction('None');		}
	}

	public function missingAction($actionID)
	{
		throw new CException(Yii::t('eyii','The system is unable to find the gadget "{action}".',array('{action}'=>get_class($this)."::action".$actionID.'()')));
	}}