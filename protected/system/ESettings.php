<?
class ESettings extends CFileCache{	protected function getValue($key)
	{
		$cacheFile=$this->getCacheFile($key);
		if(file_exists($cacheFile))
			return @file_get_contents($cacheFile);
		return false;
	}

	protected function setValue($key,$value, $expire=0)
	{
		$cacheFile=$this->getCacheFile($key);
		if($this->directoryLevel>0)
			@mkdir(dirname($cacheFile),0777,true);
		if(@file_put_contents($cacheFile,$value,LOCK_EX)!==false){
			@chmod($cacheFile,0777);
			return @touch($cacheFile);
		}
		else
			return false;
	}

	public function set($id,$value,$expire=0,$dependency=0){
		Yii::trace('Saving "'.$id.'" to Settings',get_class($this));

		if ($this->serializer === null)
			$value = serialize($value);
		elseif ($this->serializer !== false)
			$value = call_user_func($this->serializer[0], $value);

		return $this->setValue($this->generateUniqueKey($id), $value);
	}

	public function get($id,$default=false)
	{
		$value = $this->getValue($this->generateUniqueKey($id));
		if($value===false || $this->serializer===false)
			return $default;
		if($this->serializer===null)
			$value=unserialize($value);
		else
			$value=call_user_func($this->serializer[1], $value);
		return $value;
	}

	function clear(){
		Yii::app()->file->clearDirectory($this->cachePath);	}}