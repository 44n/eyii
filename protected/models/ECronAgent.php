<?
class ECronAgent extends CActiveRecord{	const STATUS_DISABLE = 0;
	const STATUS_ENABLE = 1;
	const STATUS_PROGRESS = 2;


	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{cron_agent}}';
	}

	public function rules()
	{
		return array(
			array('module, function', 'required'),
			array('module, function', 'length', 'max'=> 100),
			array('interval', 'numerical', 'integerOnly' => true),
			array('nextStart, lastStart', 'date', 'format'=>'yyyy-M-d H:m:s'),
			array('status', 'numerical', 'integerOnly' => true),
			array('data', 'safe'),
		);
	}

	function getParams(){		return unserialize($this->data);	}

	function setParams($v){
		$this->data = serialize($v);
	}

	public function scopes()
	{
		return array(
			'enable'=>array(
				'condition'=>'t.status='.self::STATUS_ENABLE,
			),
			'disable'=>array(
				'condition'=>'t.status='.self::STATUS_DISABLE,
			),
			'progress'=>array(
				'condition'=>'t.status='.self::STATUS_PROGRESS,
			),
			'needStart'=>array(
				'condition'=>'t.nextStart<NOW() OR t.nextStart IS NULL'
			),
			'expired'=>array(
				'condition'=>"t.nextStart<'".date('Y-m-d H:i:s', strtotime('-1hour'))."'"
			),
		);
	}

	public function preStart(){
		$this->status = self::STATUS_PROGRESS;
		$this->lastStart = date('Y-m-d H:i:s');
		$this->save();

	}

	public function start(){		if($this->status == self::STATUS_PROGRESS)
			Yii::app()->cronManager->runAgent($this->id);	}

	public function reStart(){		$this->status = self::STATUS_ENABLE;
		$this->nextStart = date('Y-m-d H:i:s', strtotime('+'.$this->interval.'seconds'));
		$this->save();	}

	public function runAgent(){
		$module = Yii::app()->getModule($this->module);
		if($module == null){			return $this->delete();		}

		if(!method_exists($module, $this->function)){			return $this->delete();		}

		$result = call_user_func(array($module, $this->function), $this->params);

		if(is_array($result)){			if(isset($result['interval']))
				$this->interval = $result['interval'];
			$this->params = $result['params'];
			$result = true;		}

		if($result === true){			if($this->interval > 0){				$this->reStart();			}else{				$this->delete();			}		}elseif($result === false){			$this->reStart();		}	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('module',$this->module,true);
		$criteria->compare('function',$this->function,true);
		$criteria->compare('nextStart',$this->nextStart);
		$criteria->compare('lastStart',$this->lastStart);
		$criteria->compare('interval',$this->interval);
		$criteria->compare('status',$this->status);


		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function widgetConstructor(){		return array(
			'title' => 'Cron Agents',
			'description' => '',
			'class' => 'bootstrap.widgets.TbGridView',
			'options' => array(
				'type'=>'bordered',
				'dataProvider'=>$this->search(),
				'template'=>"{items}",
				'columns'=>array(
					'module',
					'function',
					'nextStart',
					'lastStart',
					'interval',
					'status',
				),
			),
		);	}}