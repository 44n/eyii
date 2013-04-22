<?
class EFile extends CComponent{	private $_directory;
	public $name;
	public $extension;



	function __construct($fullName){
		$this->fullName = $fullName;
	}

	public function __toString(){
		return $this->fullName;
	}

	public function getFullName(){
		return $this->directory.DIRECTORY_SEPARATOR.$this->fileName;
	}

	public function setFullName($fullName){
		$this->directory = pathinfo($fullName,PATHINFO_DIRNAME);
		$this->name      = pathinfo($fullName,PATHINFO_FILENAME);
		$this->extension = pathinfo($fullName,PATHINFO_EXTENSION);
		return $this;
	}

	public function getFileName(){
		if(empty($this->extension))
			return $this->name;
		return $this->name.".".$this->extension;
	}

	public function setFileName($fileName){
		$this->name      = pathinfo($fileName,PATHINFO_FILENAME);
		$this->extension = pathinfo($fileName,PATHINFO_EXTENSION);
		return $this;
	}

	public function getDirectory(){
		return $this->_directory;
	}

	public function setDirectory($directory){
		if(!is_object($directory))
			$directory = Yii::app()->fileManager->getDirectory($directory);
		$this->_directory = $directory;
		return $this;
	}

	public function getExists(){
		return file_exists($this->fullName);
	}

	public function getReadable(){
		return is_readable($this->fullName);
	}

	public function getWriteable(){
		return is_writable($this->fullName);
	}

	public function getPermissions(){
		return $this->exists?substr(sprintf('%o', fileperms($this->fullName)),-4):null;
	}

	public function setPermissions($permissions){
		if($this->exists){
			$permissions = octdec(str_pad($permissions, 4, "0", STR_PAD_LEFT));
			if(@chmod($this->fullName, $permissions)){
				return $this;
			}
		}

		return false;
	}

	public function copy($fileDest){
		if ($this->readable){
			$newFile = Yii::app()->fileManager->getFile($fileDest);
			if($newFile->copyFrom($this->fullName)!==false){
				return $newFile;
			}
		}

		return false;
	}

	public function copyFrom($fileDest){
		if(@copy($fileDest, $this->fullName)){
			return $this;
		}

		return false;
	}

	public function rename($fileName){
		if($this->writeable){
			$oldFullName = $this->fullName;
			$oldFileName = $this->fileName;
			$this->fileName = $fileName;
			if(@rename($oldFileFullName, $this->fullName)){
				return $this;
			}

			$this->fileName = $oldFileName;
		}

		return false;
	}

	public function move($fileDest){
		$newFile = Yii::app()->fileManager->getFile($fileDest);
		if($newFile->copyFrom($this->fullName)!==false){
			$this->delete();
			$this->fullName = $newFile->fullName;
			return $this;
		}
		return false;
	}

	public function delete(){
		return @unlink($this->fullName);
	}

	public function getMimeType(){
		if ($this->readable){
			if (function_exists('finfo_open')){
				if(($info=@finfo_open(FILEINFO_MIME)) && ($result=finfo_file($info,$this->fullName))!==false){
					finfo_close($info);
					return $result;
				}else{
					finfo_close($info);
				}
			}

			if(function_exists('mime_content_type') && ($result=@mime_content_type($this->fullName))!==false)
				return $result;

			static $extensions;
			if($extensions===null)
				$extensions=require(Yii::getPathOfAlias('system.utils.mimeTypes').'.php');

			$extension = strtolower($this->extension);
			if(!empty($extension) && isset($extensions[$extension]))
				return $extensions[$extension];
		}

		return false;
	}

	public function getContent(){
		if ($this->readable){
			return @file_get_contents($this->fullName);
		}

		return false;
	}

	public function setContent($content){
		if($this->directory->create()!==false){
			if(@file_put_contents($this->fullName, $content)!==false){
				return $this;
			}
		}
		return false;
	}

	public function download($asName = "", $terminate=true){
		if(empty($asName)){
			$asName = $this->fileName;
		}

		Yii::app()->getRequest()->sendFile($asName, $this->content, 'application/octet-stream', $terminate);
	}

	public function show($asName = "", $terminate=true){
		if(empty($asName)){
			$asName = $this->fileName;
		}
		Yii::app()->getRequest()->sendFile($asName, $this->content, null, $terminate);
	}


	public function getFactory($type="text"){
		switch($type){
			case 'text': $type="eyii.base.components.fileFactories.EFileTextFactory"; break;
			case 'csv': $type="eyii.base.components.fileFactories.EFileCsvFactory"; break;
			case 'zip': $type="eyii.base.components.fileFactories.EFileZipFactory"; break;
		}

		$class = Yii::import($type);

		return new $class($this);
	}





}