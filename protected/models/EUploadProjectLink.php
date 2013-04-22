<?php
class EUploadProjectLink extends CFormModel{	public $url;

	public function rules()
	{
		return array(
			// username and password are required
			array('url', 'required'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'url'=>'File Url',
		);
	}

	public function formConstructor(){
		return array(
			'title' => 'Uplaod By Url',
			'showErrorSummary' => true,
			'elements' => array(
				'url' => array(
					'type' => 'text',
					'maxlength' => 32,
					'placeholder' => 'File Url',
					'class' => 'input-large',
				),
			),

			'buttons' => array(
				'submit' => array(
					'type' => 'submit',
					'layoutType' => 'primary',
					'label' => 'Upload',
				),
				'reset' => array(
					'type' => 'reset',
					'label' => 'Reset',
				),
			),
		);
	}

	public function upload(){		if($this->validate()){			return true;		}
		return false;	}}