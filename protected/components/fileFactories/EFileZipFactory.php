<?php
class EFileZipFactory extends CComponent{	protected $_file;
	protected $_zip;
	function __construct(&$file){
		$this->_file = $file;
	}

	function __destruct(){		$this->close();	}

	private $_handle;

	public function getHandle(){
		return $this->_handle;
	}

	public function open($mode = 'edit'){
		switch($mode){			case 'edit': $mode = ZipArchive::CREATE; break;
			case 'new': $mode = ZipArchive::OVERWRITE; break;		}

		$this->_file->directory->create();

		$this->_zip = new ZipArchive;
		$res = $this->_zip->open($this->_file->fullName, $mode);
		if($res !== true){			throw new ExceptionClass(
				Yii::t('eyii.errors','Error open zip {file}. Zip Error code {code}', array('{file}' => $this->_file->fullName, '{code}' => $res))
			);		}

		return $this;
	}



	public function close(){
		if($this->zip != null)
			$this->zip->close();

		return true;
	}

	public function add($fileName, $localName = NULL){
		if($this->zip === null){
			$this->open('edit');
		}

		if(!is_string($fileName))
			$fileName = strval($fileName);

		$this->zip->addFile($fileName, $localName);
		return $this;
	}

	public function addContent($localName, $content){		if($this->zip === null){
			$this->open('edit');
		}

		$this->zip->addFromString($localName, $content);
		return $this;	}

	public function addEmptyDir($directory){		if($this->zip === null){
			$this->open('edit');
		}


		$this->zip->addEmptyDir($directory);
		return $this;	}

	public function addDirectory($directory, $baseDirectory = ""){		if($directory instanceof EDirectory){			$directory = $directory->directory;		}else{			$directory = realpath($directory);		}

		$newFolder = $directory;
		if(!empty($baseDirectory)){
			$baseDirectory = realpath($baseDirectory);
			$newFolder = preg_replace("#^".$baseDirectory.DIRECTORY_SEPARATOR."#","",$directory, 1);		}

		if($baseDirectory != $directory)
			$this->addEmptyDir($newFolder."/");

		foreach(glob($directory . '/*') as $file){			if(is_dir($file))
			{
				$this->addDirectory($file, $baseDirectory);
			}
			else
			{
				$newFile = str_replace($base, '', $file);
				$newFile = preg_replace("#^".$baseDirectory.DIRECTORY_SEPARATOR."#","",$file, 1);
				$this->add($file, $newFile);
			}		}

		return $this;	}

	public function extractTo($directory){		if($this->zip === null){
			$this->open('edit');
		}

		if($directory instanceof EDirectory){
			$directory->create();
			$directory = $directory->directory;
		}else{
			$directory = realpath($directory);
		}

		$this->zip->extractTo($directory);	}

	function files(){		if($this->zip === null){
			$this->open('edit');
		}

		var_dump($this->zip);

		$info = array();
		for ($i = 0; $i < $this->zip->numFiles; $i++) {
			$info[] = $this->zip->getNameIndex($i);
		}

		return $info;	}

	public function getZip(){		return $this->_zip;	}}