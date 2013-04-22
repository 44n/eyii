<?php
class EFileTextFactory extends CComponent{	protected $_file;
	public function __construct(&$file){
		$this->_file = $file;
	}

	public function __destruct(){		$this->close();	}

	private $_handle;

	public function getHandle(){
		return $this->_handle;
	}

	public function open($mode){
		switch($mode){
			case 'read': $mode = 'r+'; break;
			case 'write': $mode = 'w+'; break;
			case 'add': $mode = 'a+'; break;
		}

		$this->_handle = fopen($this->_file->fullName, $mode);
		return $this;
	}



	public function close(){
		return @fclose($this->_handle);
	}

	public function add($content){
		if($this->handle === null){
			$this->open('add');
		}

		fwrite($this->handle, $content);
		return $this;
	}

	public function fetch(&$line, $length = 1024){		if($this->handle === null){
			$this->open('add');
		}

		if($this->handle === null){			return false;		}

		if(!feof($this->handle)) {
		  $line = fgets($this->handle, $length);
		  return true;
		}

		return false;	}

}