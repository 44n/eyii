<?php
class DefaultFileManager extends CApplicationComponent{

	private $_newDirectoryPermissions = "777";
	private $_newFilePermissions = "666";

	public function init(){
		parent::init();

		Yii::import('application.components.EFile');
		Yii::import('application.components.ETmpFile');
		Yii::import('application.components.EDirectory');
	}

	public function getFile($fileName = ""){
		return new EFile($fileName);
	}

	public function getTmpFile(){
		return new ETmpFile;
	}

	public function getDirectory($dirName = ""){
		return new EDirectory($dirName);
	}

	public function getNewDirectoryPermissions(){
		return octdec(str_pad($this->_newDirectoryPermissions, 4, "0", STR_PAD_LEFT));
	}

	public function setNewDirectoryPermissions($v){
		$this->_newDirectoryPermissions = $v;
	}

	public function getNewFilePermissions(){
		return octdec(str_pad($this->_newFilePermissions, 4, "0", STR_PAD_LEFT));
	}

	public function setNewFilePermissions($v){
		$this->_newFilePermissions = $v;
	}

	public function realpath($path){
		// whether $path is unix or not
		$unipath=strlen($path)==0 || $path{0}!='/';
		// attempts to detect if path is relative in which case, add cwd
		if(strpos($path,':')===false && $unipath)
			$path=getcwd().DIRECTORY_SEPARATOR.$path;
		// resolve path parts (single dot, double dot and double delimiters)
		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		$absolutes = array();
		foreach ($parts as $part) {
			if ('.'  == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		$path=implode(DIRECTORY_SEPARATOR, $absolutes);
		// put initial separator that could have been lost
		$path=!$unipath ? '/'.$path : $path;
		return $path;
	}

	public function clearDirectory($path){
		return $this->getDirectory($path)->clear();
	}

	public function deleteDirectory($path){
		return $this->getDirectory($path)->delete();
	}
}

if(!EYii::includeDefaultComponent('FileManager')){
	class FileManager extends DefaultFileManager{}
}

