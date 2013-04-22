<?

Yii::import('eyii.components.EUserIdentity');

class RootLoginFormModel extends CFormModel{	public $username;
	public $password;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password', 'required'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=> Yii::t('eyii.login',"User Name"),
			'password'=> Yii::t('eyii.login',"Password"),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new EUserIdentity($this->username,$this->password);
			if(!$this->_identity->authenticate()){
				$this->addError('username_password',Yii::t('eyii.login',"Incorrect username or password."));
				$this->addError('username','');
				$this->addError('password','');
			}
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new EUserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===EUserIdentity::ERROR_NONE)
		{
			Yii::app()->user->login($this->_identity, 0);
			return true;
		}
		else
			return false;
	}

	public function formConstructor(){		return array(
			'title' => Yii::t('eyii.login',"Authorization Form"),
			'showErrorSummary' => true,
			'elements' => array(
				'username' => array(
					'type' => 'text',
					'maxlength' => 32,
					'placeholder' => Yii::t('eyii.login',"User Name"),
					'class' => 'input-large',
				),
				'password' => array(
					'type' => 'password',
					'maxlength' => 32,
					'placeholder' => Yii::t('eyii.login',"Password"),
					'class' => 'input-large',
				)
			),

			'buttons' => array(
				'submit' => array(
					'type' => 'submit',
					'layoutType' => 'primary',
					'label' => Yii::t('eyii.login',"Login"),
				),
				'reset' => array(
					'type' => 'reset',
					'label' => Yii::t('eyii.defaults',"Reset"),
				),
			),
		);	}}


