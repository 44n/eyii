<?

class DefaultFireWall extends CApplicationComponent{

	public $enableIpCnetProtection = true;
	public $banTime = 300;

	public function init(){
		parent::init();

		$this->checkIsBlock();
	}

	public function render(&$content){	}

	function getUserIp(){
		return Yii::app()->request->userHostAddress;
	}

	private $_ip;

	function getIpForCheck(){
		if($this->_ip === null){
			$this->_ip = $this->userIp;
			if($this->enableIpCnetProtection)
				$this->_ip = substr($this->_ip,0,strrpos($this->_ip,'.')+1);
		}

		return $this->_ip;
	}

	protected function checkIsBlock(){
		if(!Yii::app()->isBackEnd && Yii::app()->isBlockFrontEnd)
			$this->blockMessage(503);

		if(Yii::app()->cache->get($this->getCacheKey()))
			$this->blockMessage();
	}

	public function getCacheKey($fullIp = ""){
		if(empty($fullIp))
			return "banIP_".$this->ipForCheck;

		if($this->enableIpCnetProtection)
			$ipCnet = substr($fullIp,0,strrpos($fullIp,'.')+1);

		return "banIP_".$ipCnet;
	}

	public function blockIP($fullIp){
		Yii::app()->cache->set($this->getCacheKey($fullIp), true, $this->banTime);
	}

	function blockCurentUser(){
		Yii::app()->cache->set($this->getCacheKey(), true, $this->banTime);
	}

	public function blockMessage($status = 403 ){
		switch($status){
			case 503:
				throw new CHttpException(503,'Service Temporarily Unavailable');
			break;
			default:
				throw new CHttpException(403,'Forbidden');
			break;
		}
	}
}

if(!EYii::includeDefaultComponent('FireWall')){	class FireWall extends DefaultFireWall{}}

