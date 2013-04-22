<?

/*
	SQLite: sqlite:/path/to/dbfile
	MySQL: mysql:host=localhost;dbname=testdb
	PostgreSQL: pgsql:host=localhost;port=5432;dbname=testdb
	SQL Server: mssql:host=localhost;dbname=testdb
	Oracle: oci:dbname=//localhost:1521/testdb


*/

class EyiiDbModel extends CFormModel{
	const SHEMA_SQLITE = 'sqlite';
	const SHEMA_MYSQL  = 'mysql';
	const SHEMA_PGSQL  = 'pgsql';
	const SHEMA_MSSQL  = 'mssql';
	const SHEMA_OCI    = 'oci';

	private $_shema = 'mysql';

	public $host = 'localhost';
	public $port = '';
	public $dbname = '';
	public $tablePrefix = 'tbl_';
	public $username = '';
	public $password = '';
	public $emulatePrepare = true;
	public $charset = 'utf8';


	function getShemaInfo(){		return array(
			EyiiDbModel::SHEMA_SQLITE => 'SQLite',
			EyiiDbModel::SHEMA_MYSQL  => 'MySQL',
			EyiiDbModel::SHEMA_PGSQL  => 'PostgreSQL',
			EyiiDbModel::SHEMA_MSSQL  => 'SQL Server',
			EyiiDbModel::SHEMA_OCI    => 'Oracle',
		);	}

	function getShema(){		return $this->_shema;	}

	function setShema($value){
		$value = strtolower($value);
		$this->_shema = $value;
		$this->setScenario($value);
	}

	public function rules()
	{
		return array(
			array('shema', 'in','range'=>array(
				EyiiDbModel::SHEMA_SQLITE, EyiiDbModel::SHEMA_MYSQL,
				EyiiDbModel::SHEMA_PGSQL, EyiiDbModel::SHEMA_MSSQL,
				EyiiDbModel::SHEMA_OCI
			)),
			array('host', 'required', 'on' => EyiiDbModel::SHEMA_SQLITE.",".EyiiDbModel::SHEMA_MYSQL.",".EyiiDbModel::SHEMA_PGSQL.",".EyiiDbModel::SHEMA_MSSQL.",".EyiiDbModel::SHEMA_OCI),
			array('dbname, charset, tablePrefix', 'required'),
			array('emulatePrepare','boolean'),
			array('password','checkConnect'),
			array('password, username, port', 'safe', 'on' => EyiiDbModel::SHEMA_MYSQL.",".EyiiDbModel::SHEMA_PGSQL.",".EyiiDbModel::SHEMA_MSSQL.",".EyiiDbModel::SHEMA_OCI),
		);
	}

	public function initDefault(){		$configs = Yii::app()->componentConfigurator->get('db');

		foreach($configs as $key=>$value)
			$this->$key = $value;	}

	public function setConnectionString($v){		$v = explode(":",$v, 2);
		$this->shema = $v[0];

		$data = array();
		switch($this->shema){
			case EyiiDbModel::SHEMA_SQLITE:
				$path = Yii::getPathOfAlias('private').DIRECTORY_SEPARATOR;
				$name = substr($v[1],strlen($path));
				$data['dbname'] = substr($name,0,-3);
			break;

			case EyiiDbModel::SHEMA_MYSQL:
			case EyiiDbModel::SHEMA_PGSQL:
			case EyiiDbModel::SHEMA_MSSQL:
				$v = str_replace(';', '&', $v[1]);
				parse_str($v, $data);
			break;

			case EyiiDbModel::SHEMA_OCI:
				$url = 'http:'.substr($v[1],7);
				$d = parse_url($url);
				$data['host'] = $d['host'];
				if(isset($d['port']))$data['port'] = $d['port'];
				$data['dbname'] = substr($d['path'],1);
			break;		}

		foreach($data as $key=>$value)
			$this->$key = $value;	}

	public function getConnectionString(){		switch($this->shema){
			case EyiiDbModel::SHEMA_SQLITE:
				$path = Yii::getPathOfAlias('private').DIRECTORY_SEPARATOR.$this->dbname.".db";
				return $this->shema.':'.$path;
			break;

			case EyiiDbModel::SHEMA_MYSQL:
			case EyiiDbModel::SHEMA_PGSQL:
			case EyiiDbModel::SHEMA_MSSQL:
				return $this->shema.':host='.$this->host.";dbname=".$this->dbname.((empty($this->port))?"":";port=".$this->port);
			break;

			case EyiiDbModel::SHEMA_OCI:
				return $this->shema.':dbname=//'.$this->host.((empty($this->port))?"":":".$this->port)."/".$this->dbname;
			break;		}

		return "";	}



	function createConfig(){		return array(
			'connectionString'=>$this->connectionString,
			'tablePrefix' => $this->tablePrefix,
			'username'=>$this->username,
			'password'=>$this->password,
			'emulatePrepare'=>($this->emulatePrepare)?true:false,
			'charset'=>$this->charset
		);	}

	public function checkConnect($a,$p){		$config = $this->createConfig();
		$config['class'] = 'CDbConnection';
		$config['autoConnect'] = false;
		$componentDb = Yii::createComponent($config);
		try{			$componentDb->active = true;
		}catch(Exception $e){
			$this->addError('password',$e->getMessage().var_export($config, true));
		}
	}

	public function save(){
		if($this->validate()){
			Yii::app()->componentConfigurator->set('db',$this->createConfig());
			Yii::app()->user->setFlash('success',Yii::t('eyii.defaults',"The data has been successfully updated!"));
			return true;
		}
		return false;
	}

	public function attributeLabels()
	{
		return array(
			'dbname'=> Yii::t('eyii.db',"DB Name"),
			'shema'=> Yii::t('eyii.db',"Scheme"),
			'emulatePrepare'=> Yii::t('eyii.db',"Emulate Prepare"),
			'tablePrefix'=> Yii::t('eyii.db',"Table Prefix"),
			'charset'=> Yii::t('eyii.db',"Charset"),
			'host'=> Yii::t('eyii.db',"Host"),
			'port'=> Yii::t('eyii.db',"Port"),
			'username'=> Yii::t('eyii.db',"User Name"),
			'password'=> Yii::t('eyii.db',"Password"),
		);
	}

	public function formConstructor(){
		return array(
			'title' => Yii::t('eyii.menu',"DB Connection"),
			'showErrorSummary' => true,
			'elements' => array(
				'shema' => array(
					'type' => 'radiolistinline',
					'items' => $this->getShemaInfo(),
				),
				'emulatePrepare' => array(
					'type' => 'checkbox',
				),
				'dbname' => array(
					'type' => 'text',
					'maxlength' => 100,
					'placeholder' => Yii::t('eyii.db',"DB Name"),
					'class' => 'input-large',
				),
				'tablePrefix' => array(
					'type' => 'text',
					'maxlength' => 50,
					'placeholder' => Yii::t('eyii.db',"Table Prefix"),
					'class' => 'input-large',
				),
				'charset' => array(
					'type' => 'text',
					'maxlength' => 100,
					'placeholder' => Yii::t('eyii.db',"Charset"),
					'class' => 'input-large',
				),
				'host' => array(
					'type' => 'text',
					'maxlength' => 100,
					'placeholder' => Yii::t('eyii.db',"Host"),
					'class' => 'input-large',
					'hint' => Yii::t('eyii.db','Not Need for SQLite')
				),
				'port' => array(
					'type' => 'text',
					'maxlength' => 50,
					'placeholder' => Yii::t('eyii.db',"Port"),
					'class' => 'input-small',
					'hint' => Yii::t('eyii.db','Not Need for SQLite')
				),
				'username' => array(
					'type' => 'text',
					'maxlength' => 100,
					'placeholder' => Yii::t('eyii.db',"User Name"),
					'class' => 'input-large',
					'hint' => Yii::t('eyii.db','Not Need for SQLite')
				),
				'password' => array(
					'type' => 'text',
					'maxlength' => 100,
					'placeholder' => Yii::t('eyii.db',"Password"),
					'class' => 'input-large',
					'hint' => Yii::t('eyii.db','Not Need for SQLite')
				),


			),

			'buttons' => array(
				'submit' => array(
					'type' => 'submit',
					'layoutType' => 'primary',
					'label' => Yii::t('eyii.defaults',"Save"),
				),
				'reset' => array(
					'type' => 'reset',
					'label' => Yii::t('eyii.defaults',"Reset"),
				),
			),
		);
	}
}