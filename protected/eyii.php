<?php
defined('EYII_BACKEND') or define('EYII_BACKEND', false);

defined('EYII_DOCUMENT_ROOT') or exit ('EYII_DOCUMENT_ROOT is not installed');

define('YII_BASE_PATH',dirname(__FILE__));

defined('YII_PATH') or define('YII_PATH',EYII_DOCUMENT_ROOT.DIRECTORY_SEPARATOR.'framework');

defined('PRIVATE_PATH') or define('PRIVATE_PATH',YII_BASE_PATH.DIRECTORY_SEPARATOR.'data');

defined('PUBLIC_PATH') or define('PUBLIC_PATH',EYII_DOCUMENT_ROOT.DIRECTORY_SEPARATOR.'files');

require_once(YII_PATH.DIRECTORY_SEPARATOR.'yii.php');

Yii::setPathOfAlias('public', realpath(PUBLIC_PATH));
Yii::setPathOfAlias('private', realpath(PRIVATE_PATH));
Yii::setPathOfAlias('eyii', realpath(YII_BASE_PATH));

class EYii{	const VERSION = '1.0.2';
	const VERSION_DATE = '2013-04-17';

	static function createWebApplication($config=null){
		Yii::import('eyii.system.*');
		return new EWebApplication($config, EYII_BACKEND);
	}

	static function includeDefaultComponent($name, $module = ""){
		if(empty($module))$module = strtolower($name);

		$path = Yii::getPathOfAlias($module.'.components.'.$name).".php";
		if(file_exists($path)){
			require_once($path);
			return true;
		}
		return false;
	}

	static function powered(){		return Yii::t('yii','Powered by {yii}.', array('{yii}'=>'
<a href="http://www.yiiframework.com" target="_blank">Yii PHP framework</a>
/
<a href="http://yii-booster.clevertech.biz" target="_blank">YiiBooster</a>
/
<a href="http://www.eyii.ru" target="_blank">EASY Yii</a>'));	}

	static function copyright(){
		return Yii::t('yii','Copyright &copy; {Y} by {set4web}', array('{set4web}'=>'<a href="http://www.set4web.ru" target="_blank">Set4Web</a>', '{Y}' => date('Y')));
	}}