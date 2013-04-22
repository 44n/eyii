<?php
class EUploadProjectLocal extends CFormModel{	public $selectFile;

	public function rules()
	{
		return array(
			// username and password are required
			array('selectFile', 'required'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'selectFile'=>'Select File',
		);
	}

	function getFiles(){		$path = Yii::getPathOfAlias('private.backup');
		$files = CFileHelper::findFiles($path, array('level'=> 0, 'fileTypes' => array('zip')));
		$return = array();
		foreach($files as $file){			$file = substr($file, strlen($path));
			$return[$file] = $file;		}

		return $return;	}

	public function formConstructor(){
		return array(
			'title' => 'Uplaod From BackUp Directory',
			'showErrorSummary' => true,
			'elements' => array(
				'selectFile' => array(
					'type' => 'dropdownlist',
					'items' => $this->getFiles()
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