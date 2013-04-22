<?php

Yii::import('eyii.base.components.fileFactories.EFileTextFactory');

class EFileCsvFactory extends EFileTextFactory{	private $_format = false;

	public function fetch(&$row, $length = 1024, $delimiter=";", $enclosure = '"'){
		if($this->handle === null){
			$this->open('read');
		}

		$data = fgetcsv($this->handle, $length, $delimiter, $enclosure);

		if($data === false){
			$row = array();
			return false;
		}

		$row = $this->reversFormat($data);
		return true;
	}


	public function addRow($row, $delimiter = ";", $enclosure = '"'){
		if($this->handle === null){
			$this->open('write');
		}
		$row = $this->format($row);
		return fputcsv($this->handle, $row, $delimiter);
	}

	function setFormat($format){
		if(is_string($format))$format = explode("|",$format);
		$this->_format = $format;
	}

	private function format($data){
		if($this->_format == false)return $data;
		$ret = array();
		foreach($this->_format as $key){
			$ret[] = (isset($data[$key]))?$data[$key]:"";
		}
		return $ret;
	}

	private function reversFormat($data){
		if($this->_format == false)return $data;
		$ret = array();
		foreach($this->_format as $index=>$key){
			$ret[$key] = (isset($data[$index]))?$data[$index]:"";
		}
		return $ret;
	}}