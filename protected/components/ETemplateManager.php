<?php
class ETemplateManager extends CApplicationComponent{
	public function init(){		parent::init();
		Yii::import('application.components.ETemplate');
		Yii::import('application.components.ETemplateHeadContructor');
		Yii::import('application.components.EGadgetTemplate');
		Yii::import('application.components.EGadgetController');
		Yii::import('application.components.EHelperController');
	}

	public function get($templateName){
		$template = new ETemplate;
		$template->setTemplateName($templateName);

		return $template;
	}

	public function getGadget($templateName){
		$template = new EGadgetTemplate;
		$template->setTemplateName($templateName);

		return $template;
	}

	public function getCachePath(){		return Yii::getPathOfAlias("application.runtime.templates");
	}

	public function clearAll(){		return Yii::app()->fileManager->clearDirectory($this->getCachePath());
	}

	public function clear($name){
		return Yii::app()->fileManager->clearDirectory($this->getCachePath(). DIRECTORY_SEPARATOR. str_replace('.',DIRECTORY_SEPARATOR, $name));
	}}