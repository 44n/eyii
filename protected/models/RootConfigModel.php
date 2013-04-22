<?

class RootConfigModel extends CFormModel{	public $login;
	public $password;
	public $repassword;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('login, password, repassword', 'required'),
			array('password, login', 'length', 'min'=>5),
			array('repassword', 'compare', 'compareAttribute'=>'password'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'login'=>Yii::t('eyii.defaults',"Login"),
			'password'=>Yii::t('eyii.defaults',"Password"),
			'repassword'=>Yii::t('eyii.defaults',"Repeat Password"),
		);
	}

	public function save(){		if($this->validate()){
			Yii::import('eyii.components.EUserIdentity');

			$key = EUserIdentity::createKey($this->login, $this->password);
			Yii::app()->settings->set('rootKey', $key);
			Yii::app()->user->setFlash('success',Yii::t('eyii.defaults',"The data has been successfully updated!"));
			return true;
		}
		return false;
	}


	public function formConstructor(){		return array(
			'title' => Yii::t('eyii.rootProfile',"Change Root Profile"),
			'showErrorSummary' => true,
			'elements' => array(
				'login' => array(
					'type' => 'text',
					'maxlength' => 32,
					'placeholder' => Yii::t('eyii.defaults',"Login"),
					'class' => 'input-large',
				),
				'password' => array(
					'type' => 'password',
					'maxlength' => 32,
					'placeholder' => Yii::t('eyii.defaults',"Password"),
					'class' => 'input-large',
				),
				'repassword' => array(
					'type' => 'password',
					'maxlength' => 32,
					'placeholder' => Yii::t('eyii.defaults',"Repeat Password"),
					'class' => 'input-large',
				)
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


