<?
class ETmpFile extends EFile{
	private $_fileForDelete;

	function __construct(){
		$this->_fileForDelete = tempnam(Yii::app()->runtimePath,'tmpFile_');
		$this->fullName = $this->_fileForDelete;
	}

	function __destruct(){
		@unlink($this->_fileForDelete);	}

}