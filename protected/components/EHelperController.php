<?

class EHelperAction extends CInlineAction{

	protected function runWithParamsInternal($object, $method, $params){
		$ps=array();
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

class EHelperController extends EController{	private $_actionParams;
	private $_result;

	public function __construct($moduleName){		$module = Yii::app()->getModule($moduleName);
		parent::__construct('helper',$module);
		$this->init();	}

	public function createAction($actionID)
	{
		if($actionID==='')
			$actionID=$this->defaultAction;
		if(method_exists($this,'action'.$actionID) && strcasecmp($actionID,'s')) // we have actions method
			return new EHelperAction($this,$actionID);
		else{
			$action=$this->createActionFromMap($this->actions(),$actionID,$actionID);
			if($action!==null && !method_exists($action,'run'))
				throw new CException(Yii::t('yii', 'Action class {class} must implement the "run" method.', array('{class}'=>get_class($action))));
			return $action;
		}
	}

	public function getTemplate(){
		$this->render();
	}

	public function render(){		throw new CException(Yii::t('eyii','This method denied for use in helpers.'));	}

	public function getActionParams(){
		return (empty($this->_actionParams))?array():$this->_actionParams;
	}

	public function result($data){		$this->_result = $data;	}

	function getResult(){		return $this->_result;	}

	function setResult($v){
		$this->_result = $v;
	}


	public function getResultAndClear(){		$r = $this->_result;
		$this->_result = null;
		return $r;	}

	public function runHelper(&$template, $actionID, $parameters){

		$this->_actionParams = $parameters;

		if(is_string($actionID)){			$this->run($actionID);
			return $this->getResultAndClear();		}else{			$this->missingAction('None');		}	}

	public function missingAction($actionID){
		throw new CException(Yii::t('eyii','The system is unable to find the helper "{action}".',array('{action}'=>get_class($this)."::action".$actionID.'()')));
	}}