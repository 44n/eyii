<?php
class EAuthManager extends CDbAuthManager{	public $itemTable='{{auth_item}}';
	public $itemChildTable='{{auth_item_child}}';
	public $assignmentTable='{{auth_assignment}}';

	public $defaultRoles=array('Guest');

	public function checkAccess($itemName,$userId,$params=array()){
		if(!Yii::app()->isInstall){
			return false;
		}

		return parent::checkAccess($itemName,$userId,$params);
	}
}