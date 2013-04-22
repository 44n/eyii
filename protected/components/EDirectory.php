<?
class EDirectory extends CComponent{	public $name;
	public $path;

	public function __construct($directory=""){
		$this->directory = $directory;
	}

	public function __toString(){
		return $this->directory;
	}

	public function getParent(){
		return Yii::app()->fileManager->getDirectory($this->path);
	}

	public function getDirectory(){
		return $this->path.DIRECTORY_SEPARATOR.$this->name;
	}

	public function setDirectory($directory){
		if(empty($directory))return $this;
		$directory = Yii::app()->fileManager->realpath($directory);
		$data = pathinfo($directory);
		$this->path = $data['dirname'];
		$this->name = $data['basename'];
		return $this;
	}

	public function getExists(){
		return is_dir($this->directory);
	}

	public function create(){
		if(!$this->exists){
			if(!@mkdir($this->directory, Yii::app()->fileManager->newDirectoryPermissions, true)){
				return false;
			}
		}

		return $this;
	}

	public function getPermissions(){
		return $this->exists?substr(sprintf('%o', fileperms($this->directory)),-4):null;
	}

	public function setPermissions($permissions){
		if($this->exists){
			$permissions = octdec(str_pad($permissions, 4, "0", STR_PAD_LEFT));
			if(@chmod($this->directory, $permissions)){
				return $this;
			}
		}

		return false;
	}

	public function delete(){
		if($this->clear()!==false){
			if($this->getExists())
				return rmdir($this->directory);
			return true;
		}

		return false;
	}

	public function clear(){
		$return = true;
		foreach($this->source() as $item){
			if(!$item->delete()){
				$return = false;
			}
		}

		return $return;
	}

	public function source($type="LIST"){

		switch(strtoupper($type)){
			case 'ASSOC': return $this->sourceAssoc; break;
			case 'FILES': return $this->files; break;
			case 'DIRECTORYS': return $this->directorys; break;
		}

		$return = array();
		if($this->exists){
			if ($handle = opendir($this->directory)) {
				while (false !== ($some = readdir($handle))) {
					if ($some != "." && $some != "..") {
						$item = $this->directory.DIRECTORY_SEPARATOR.$some;
						if(is_file($item)){
							$return[] = Yii::app()->fileManager->getFile($item);
						}else{
							$return[] = Yii::app()->fileManager->getDirectory($item);
						}
					}
				}
				closedir($handle);
			}
		}

		return $return;
	}

	public function getSourceAssoc(){
		$return = array(
			'files' => array(),
			'directorys' => array(),
		);

		if($this->exists){
			if ($handle = opendir($this->directory)) {
				while (false !== ($some = readdir($handle))) {
					if ($some != "." && $some != "..") {
						$item = $this->directory.DIRECTORY_SEPARATOR.$some;
						if(is_file($item)){
							$return['files'][] = Yii::app()->fileManager->getFile($item);
						}else{
							$return['directorys'][] = Yii::app()->fileManager->getDirectory($item);
						}
					}
				}
				closedir($handle);
			}
		}
		return $return;
	}

	public function getFiles(){
		return $this->sourceAssoc['files'];
	}

	public function getDirectorys(){
		return $this->sourceAssoc['directorys'];
	}


}