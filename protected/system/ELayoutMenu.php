<?
class ELayoutMenu extends CApplicationComponent{

	protected function renderMenu($menu){		$newMenu = array();

		foreach($menu as $item){			if(is_array($item)){
				if(!isset($item['url']))
					$item['url'] = "#";

				if(isset($item['items'])){					$item['items'] = $this->renderMenu($item['items']);
					if(!empty($item['items']))
						$newMenu[] = $item;
				}else{					$newMenu[] = $item;
				}
			}else{				$newMenu[] = $item;
			}
		}

		return $newMenu;
	}

	public function get(){		$menu = array(
			'CONTENT' => array('label'=>Yii::t('eyii.menu',"Content"), 'items' => array()),
			'SERVICES' => array('label'=> Yii::t('eyii.menu',"Services"), 'items' => array()),
			'SHOP' => array('label'=> Yii::t('eyii.menu',"Shop"), 'items' => array()),

			'ANALYTICS' => array('label'=> Yii::t('eyii.menu',"Analytics"), 'items' => array()),
			'SETTINGS' => array('label'=> Yii::t('eyii.menu',"Settings"), 'items' => array()),
		);

		$eyiiSettings['label'] = "EYII";
		$eyiiSettings['items'] = array();

		if(Yii::app()->user->checkAccess('eyii.Install')){
			$eyiiSettings['items']['siteUpdate'] = array('label'=> Yii::t('eyii.menu',"Site Update"), 'url' => Yii::app()->createUrl('update'));
			$eyiiSettings['items']['info'] = array('label'=> Yii::t('eyii.menu',"Defaults"), 'url' => Yii::app()->createUrl('settings/info'));
			$eyiiSettings['items']['db'] = array('label'=>Yii::t('eyii.menu',"DB Connection"), 'url' => Yii::app()->createUrl('settings/db'));
		}

		if(Yii::app()->user->checkAccess('eyii.Settings')){
			$eyiiSettings['items']['clientScript'] = array('label'=>Yii::t('eyii.menu',"Client Script"), 'url' => Yii::app()->createUrl('settings/clientScript'));
		}

		if(Yii::app()->user->isRoot){
			$eyiiSettings['items']['rootProfile'] = array('label'=>Yii::t('eyii.menu',"Root Profile"), 'url' => Yii::app()->createUrl('settings/rootProfile'));
		}

		$menu['SETTINGS']['items']['eyii'] = $eyiiSettings;

		foreach(Yii::app()->modules as $id => $module){
			if($id != 'eyii'){
				$m = Yii::app()->getModule($id);
				if($m !== null){
					$menu = CMap::mergeArray($menu, $m->getLayoutMenu());
				}
			}
		}

		return $this->renderMenu($menu);	}}