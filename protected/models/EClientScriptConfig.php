<?php

class EClientScriptConfig extends CFormModel{	public $compressJs = false;
	public $compressCss = false;
	public $combineJs = true;
	public $combineCss = true;


	public function rules()
	{
		return array(
			array('compressJs, compressCss, combineJs, combineCss','boolean'),
		);
	}

	public function initDefault(){
		$configs = Yii::app()->componentConfigurator->get('clientScript');

		foreach($configs as $key=>$value)
			$this->$key = $value;
	}

	function createConfig(){
		return array(
			'compressJs'=>$this->compressJs,
			'compressCss' => $this->compressCss,
			'combineJs'=>$this->combineJs,
			'combineCss'=>$this->combineCss,
		);
	}

	public function save(){
		if($this->validate()){
			Yii::app()->componentConfigurator->set('clientScript',$this->createConfig());
			Yii::app()->user->setFlash('success',Yii::t('eyii.defaults',"The data has been successfully updated!"));
			return true;
		}
		return false;
	}

	public function attributeLabels()
	{
		return array(
			'combineJs'=> Yii::t('eyii.cs',"Combine Js"),
			'combineCss'=> Yii::t('eyii.cs',"Combine Css"),
			'compressJs'=> Yii::t('eyii.cs',"Compress Js"),
			'compressCss'=> Yii::t('eyii.cs',"Compress Css"),
		);
	}

	public function formConstructor(){
		return array(
			'title' => Yii::t('eyii.menu',"Client Script"),
			'showErrorSummary' => true,
			'elements' => array(

				'combineJs' => array(
					'type' => 'checkbox',
				),
				'combineCss' => array(
					'type' => 'checkbox',
				),
				'compressJs' => array(
					'type' => 'checkbox',
				),
				'compressCss' => array(
					'type' => 'checkbox',
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
	}}