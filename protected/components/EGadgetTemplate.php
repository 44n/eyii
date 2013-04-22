<?
class EGadgetTemplate extends ETemplate{
	public function render($assign=array(),$expire=null,$dependency=null){
		$this->fetch($assign,$expire,$dependency,true);
		$this->showContent();
	}
}