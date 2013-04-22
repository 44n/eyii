<?php
class EActiveArrayRecord extends CFormModel{	private static $_models=array();
	protected $_oldKey;
	protected $_key;

	public function __construct($scenario='', $key=null, $value=array())
	{
		$this->setScenario($scenario);
		$this->_key = $this->_oldKey = $key;
		if(!empty($value)){
			$attributeNames = $this->attributeNames();
			foreach($attributeNames as $name){				if(isset($value[$name])){					$this->$name = $value[$name];				}			}
		}

		$this->init($key, $value);
		$this->attachBehaviors($this->behaviors());
		$this->afterConstruct();
	}

	public static function model($className=__CLASS__)
	{
		if(isset(self::$_models[$className]))
			return self::$_models[$className];
		else
		{
			$model=self::$_models[$className]=new $className(null);
			return $model;
		}
	}

	private $_filter;

	public function getArrayFilter(){
		if($this->_filter === null){
			$this->_filter = new EArrayFilter;
			$this->_filter->indexKey = $this->indexKey();
		}

		return $this->_filter;
	}

	public function fileName()
	{
		return "";
	}

	public function indexKey()
	{
		return "";
	}

	public function loadRecords(){		return require($this->fileName());	}

	public function saveRecords($records){
		return file_put_contents($this->fileName(),'<?php return '.var_export($records,true).';');
	}

	public function save($runValidation=true,$attributes=null)
	{
		if(!$runValidation || $this->validate($attributes))
			return $this->insert($attributes);
		else
			return false;
	}


	public function renderKey(){		$indexKey = $this->indexKey();
		if($indexKey != ""){			return $this->$indexKey;		}elseif($this->_key == null){			$this->_key = microtime(true);		}
		return $this->_key;	}

	public function renderValue($attributes=null){
		return $this->getAttributes($attributes);
	}

	public function onBeforeSave($event)
	{
		$this->raiseEvent('onBeforeSave',$event);
	}

	public function onAfterSave($event)
	{
		$this->raiseEvent('onAfterSave',$event);
	}

	public function onBeforeDelete($event)
	{
		$this->raiseEvent('onBeforeDelete',$event);
	}

	public function onAfterDelete($event)
	{
		$this->raiseEvent('onAfterDelete',$event);
	}

	protected function beforeSave()
	{
		if($this->hasEventHandler('onBeforeSave'))
		{
			$event=new CModelEvent($this);
			$this->onBeforeSave($event);
			return $event->isValid;
		}
		else
			return true;
	}


	protected function afterSave()
	{
		if($this->hasEventHandler('onAfterSave'))
			$this->onAfterSave(new CEvent($this));
	}

	protected function beforeDelete()
	{
		if($this->hasEventHandler('onBeforeDelete'))
		{
			$event=new CModelEvent($this);
			$this->onBeforeDelete($event);
			return $event->isValid;
		}
		else
			return true;
	}

	protected function afterDelete()
	{
		if($this->hasEventHandler('onAfterDelete'))
			$this->onAfterDelete(new CEvent($this));
	}



	public function insert($attributes=null)
	{
		if($this->beforeSave())
		{
			Yii::trace(get_class($this).'.insert()','eyii.base.EActiveArrayRecord');
			$records = $this->loadRecords();
			$this->_key = $this->renderKey();
			if($this->_oldKey !== null && $this->_oldKey !== $this->_key){
				unset($records[$this->_oldKey]);
				$this->_oldKey = $this->_key;
			}

			$records[$this->_key] = $this->renderValue($attributes);
			$this->saveRecords($records);
			$this->afterSave();

			return true;
		}
		return false;
	}

	public function delete()
	{
			Yii::trace(get_class($this).'.delete()','eyii.base.EActiveArrayRecord');
			if($this->beforeDelete())
			{
				$result=$this->deleteByKey($this->_oldKey);
				$this->afterDelete();
				return $result;
			}
			else
			return false;
	}

	public function deleteByKey($key)
	{
		Yii::trace(get_class($this).'.deleteByPk()','eyii.base.EActiveArrayRecord');
		return $this->deleteAll(array(
			array(
				'key' => '',
				'value'=>$key
			)
		));
	}

	public function deleteAll($condition=array())
	{
		Yii::trace(get_class($this).'.deleteAll()','eyii.base.EActiveArrayRecord');

		foreach($condition as $key=>$value){			if(is_int($key)){				$this->arrayFilter->addFilter($value);			}else{				$this->arrayFilter->addFilter($key, $value);			}		}

		$records = $this->loadRecords();
		$deleteRecords = $this->arrayFilter->applyFilter($records);
		$this->arrayFilter->clear();

		foreach($deleteRecords as $key=>$value){			unset($records[$key]);		}

		return $this->saveRecords($records);
	}

	public function find($condition=array())
	{
		Yii::trace(get_class($this).'.find()','eyii.base.EActiveArrayRecord');
		foreach($condition as $key=>$value){
			if(is_int($key)){
				$this->arrayFilter->addFilter($value);
			}else{
				$this->arrayFilter->addFilter($key, $value);
			}
		}

		$this->arrayFilter->limit = 1;
		$this->arrayFilter->offset = 0;

		$records = $this->loadRecords();
		$records = $this->arrayFilter->applyFilter($records);
		$this->arrayFilter->clear();
		if(empty($records))return null;
		list($key, $value) = each($records);
		$className = get_class($this);
		return new $className('',$key, $value);
	}

	public function findByKey($key){		Yii::trace(get_class($this).'.findByKey()','eyii.base.EActiveArrayRecord');
		return $this->find(array(
			array(
				'key' => '',
				'value'=>$key
			)
		));	}

	public function findAll($condition=array()){		Yii::trace(get_class($this).'.findAll()','eyii.base.EActiveArrayRecord');
		foreach($condition as $key=>$value){
			if(is_int($key)){
				$this->arrayFilter->addFilter($value);
			}else{
				$this->arrayFilter->addFilter($key, $value);
			}
		}

		$records = $this->loadRecords();
		$records = $this->arrayFilter->applyFilter($records);
		$this->arrayFilter->clear();
		if(empty($records))return array();
		$result = array();
		$className = get_class($this);
		foreach($records as $key=>$value){			$result[] = new $className('',$key, $value);		}
		return $result;	}
}