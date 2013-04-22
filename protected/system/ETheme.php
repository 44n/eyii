<?php
class ETheme extends CTheme{
	public function getTemplateFile($viewName){		$viewFile = $this->getViewPath().DIRECTORY_SEPARATOR.$viewName.".php";
		if(!file_exists($viewFile)){
			if(strpos($viewName,'.')){
				$data = explode('.',$viewName);
				$module = "";
				if(count($data)>2){					$module = $data[0];
				}

				if(empty($module)){					$viewFile=Yii::getPathOfAlias("eyii.views").DIRECTORY_SEPARATOR.$viewName.".php";				}else{					$viewFile=Yii::getPathOfAlias($module.".views").DIRECTORY_SEPARATOR.$viewName.".php";				}
			}
		}

		return $viewFile;
	}

	public function getCssUrl($file = ""){		if(empty($file))
			return $this->getBaseUrl()."/css";
		return $this->getBaseUrl()."/css/".$file;	}

	public function getJsUrl($file = ""){
		if(empty($file))
			return $this->getBaseUrl()."/js";
		return $this->getBaseUrl()."/js/".$file;
	}

	public function getImgUrl($file = ""){
		if(empty($file))
			return $this->getBaseUrl()."/img";
		return $this->getBaseUrl()."/img/".$file;
	}
}