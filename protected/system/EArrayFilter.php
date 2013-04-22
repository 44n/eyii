<?php
class EArrayFilter{	public $filters = array();
	public $limit = 0;
	public $offset = 0;
	public $indexKey = "";


	public function addFilter($key, $value="", $like = false){		if(is_array($key)){			if(isset($key['key'], $key['value'])){				$value = $key['value'];
				$like = (isset($key['like']))?$key['like']:false;
				$key = $key['key'];			}		}
		$this->filters[] = array('key' => $key, 'value' => $value, 'like' => (bool)$like);
	}

	function applyFilter($data){		foreach($data AS $rowIndex => $row) {
			foreach($this->filters AS $filter) {

				if($filter['key'] == ""){					if($this->indexKey == "")
						$check = $rowIndex;
					else
						$check = $row[$this->indexKey];				}else{					$check = $row[$filter['key']];				}

				if($filter['like']){					if(stripos($check, $filter['value']) === false)
						unset($data[$rowIndex]);				}else{					if($check != $filter['value'])
						unset($data[$rowIndex]);				}
			}
		}

		if($this->limit != 0 || $this->offset != 0){			$data = array_slice($data, $this->offset, $this->limit, true);		}

		return $data;	}

	public function clear(){		$this->limit = 0;
		$this->offset = 0;
		$this->filters = array();
	}}