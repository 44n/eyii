<?php
class EUploadProjectFile extends CFormModel{	public $file;

	public function rules()
	{
		return array(
			// username and password are required
			array('file', 'file', 'types'=> 'zip',),
		);
	}

	public function attributeLabels()
	{
		return array(
			'file'=>'File',
		);
	}

	public function formConstructor(){
		return array(
			'title' => 'Uplaod By File Archive',
			'showErrorSummary' => true,
			'elements' => array(
				'file' => array(
					'type' => 'file',
					'maxlength' => 32,
					'placeholder' => 'Choose File',
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

	public function upload(){
		if($this->validate()){
			return true;
		}
		return false;
	}}