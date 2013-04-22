<?php
define('YII_BEGIN_TIME',microtime(true));
define('YII_DEBUG', true);
define('YII_TRACE_LEVEL',3);

defined('EYII_BACKEND') or define('EYII_BACKEND', false);

define('EYII_DOCUMENT_ROOT',dirname(__FILE__));
define('YII_BASE_PATH',EYII_DOCUMENT_ROOT.DIRECTORY_SEPARATOR.'protected');

if(!isset($configs)){	$configs = null;}



require_once(YII_BASE_PATH.DIRECTORY_SEPARATOR.'eyii.php');

EYii::createWebApplication($configs)->run();