<?php
class EyiiInfoModel extends CFormModel{	public $supportEmail;
	public $backEndPreffix;

	public function initDefault(){		$this->backEndPreffix = Yii::app()->backEndPreffix;
		$this->supportEmail = Yii::app()->supportEmail;	}

	public function rules()
	{
		return array(
			// username and password are required
			array('backEndPreffix, supportEmail', 'required'),
			// password needs to be authenticated
			array('supportEmail', 'email'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'backEndPreffix'=>Yii::t('eyii.info',"BackEnd Preffix"),
			'supportEmail'=>Yii::t('eyii.info',"Technical Email"),
		);
	}

	public function save(){		if($this->validate()){			Yii::app()->backEndPreffix = $this->backEndPreffix;
			Yii::app()->supportEmail = $this->supportEmail;
			Yii::app()->user->setFlash('success',Yii::t('eyii.defaults',"The data has been successfully updated!"));
			return true;
		}
		return false;
	}

	public function formConstructor(){
		return array(
			'title' => Yii::t('eyii.menu',"Defaults"),
			'showErrorSummary' => true,
			'elements' => array(
				'backEndPreffix' => array(
					'type' => 'text',
					'maxlength' => 100,
					'placeholder' => Yii::t('eyii.info',"BackEnd Preffix"),
					'class' => 'input-large',
				),
				'supportEmail' => array(
					'type' => 'text',
					'maxlength' => 100,
					'placeholder' => Yii::t('eyii.info',"Technical Email"),
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