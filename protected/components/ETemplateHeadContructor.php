<?class ETemplateHeadContructor extends CApplicationComponent{
	protected $t;
	public $title='';

	public function templateCall(&$template){		$this->t = $template;
		return $this;	}

	public function setTitle($content){
		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setTitle('.var_export($content,true).');?>';
			return $this;
		}
		$this->title = $content;

		return $this;
	}

	public function setKeywords($content){
		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setKeywords('.var_export($content,true).');?>';
			return $this;
		}

		Yii::app()->clientScript->registerMetaTag($content, 'keywords');
		return $this;
	}

	public function setDescription($content){
		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setDescription('.var_export($content,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($content, 'description');
		return $this;
	}

	public function setAuthor($content){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setAuthor('.var_export($content,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($content,'author');
		return $this;	}

	public function setRobots ($content){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setRobots('.var_export($content,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($content,'robots');
		return $this;	}

	public function setCopyright ($content){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setCopyright('.var_export($content,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($content,'copyright');
		return $this;	}

	public function setCharset($charset, $contentType = 'text/html'){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setCharset('.var_export($charset,true).', '.var_export($contentType,true).');?>';
			return $this;
		}
		$content = $contentType."; charset=".$charset;
		Yii::app()->clientScript->registerMetaTag($content, null, 'Content-Type');
		return $this;	}

	public function setDocState($content=''){		$content = (strtolower($content) == 'static')? 'Static': 'Dynamic';
		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setDocState('.var_export($content,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($content,'document-state');
		return $this;	}

	public function setDocStateDynamic(){		$this->setDocState('Dynamic');
		return $this;	}

	public function setDocStateStatic(){
		$this->setDocState('Static');
		return $this;
	}

	public function setRevisit($days){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setRevisit('.var_export($days,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($days,'revisit');
		return $this;	}

	public function setUrl($url,$schema='http'){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setUrl('.var_export($url,true).','.var_export($schema,true).');?>';
			return $this;
		}
		if(is_array($url)){
			$params = $url;
			$route = array_shift($params);
			$url = Yii::app()->createAbsoluteUrl($route,$params,$schema);
		}
		Yii::app()->clientScript->registerMetaTag($url,'url');
		return $this;	}

	public function setLanguage($lang){
		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setLanguage('.var_export($lang,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($lang, null, 'content-language');
		return $this;
	}

	public function setContentType($type){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setContentType('.var_export($type,true).');?>';
			return $this;
		}
		$this->contentType = $type;
		return $this;	}

	public function setExpires($sec=60){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setExpires('.var_export($sec,true).');?>';
			return $this;
		}
		$expires = gmdate("D, d M Y H:i:s", time() + $sec) . " GMT";
		Yii::app()->clientScript->registerMetaTag($expires, null, 'Expires');
		return $this;	}

	public function setPragma($pragma){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setPragma('.var_export($pragma,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($pragma, null, 'Pragma');
		return $this;	}

	public function setRefresh($content){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setRefresh('.var_export($content,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerMetaTag($content, null, 'refresh');
		return $this;	}

	public function setFavicon($url, $type='image/x-icon'){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setFavicon('.var_export($url,true).', '.var_export($type,true).');?>';
			return $this;
		}
		Yii::app()->clientScript->registerLinkTag('icon', $type, $url);
		Yii::app()->clientScript->registerLinkTag('shortcut icon', $type, $url);
		return $this;	}

	public function setCanonical($url,$schema='http'){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->setCanonical('.var_export($url,true).','.var_export($schema,true).');?>';
			return $this;
		}
		if(is_array($url)){			$params = $url;
			$route = array_shift($params);
			$url = Yii::app()->createAbsoluteUrl($route,$params,$schema);		}

		Yii::app()->clientScript->registerLinkTag('canonical',null, $url);
		return $this;	}

	function addRss($title,$url,$schema='http'){		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->addRss('.var_export($title,true).', '.var_export($url,true).','.var_export($schema,true).');?>';
			return;
		}
		if(is_array($url)){
			$params = $url;
			$route = array_shift($params);
			$url = Yii::app()->createAbsoluteUrl($route,$params,$schema);
		}
		Yii::app()->clientScript->registerLinkTag('alternate','application/rss+xml', $url,null,array('title' => $title));
		return $this;	}

	function addAtomFeed($title,$url,$schema='http'){
		if(!$this->t->isCached()&&$this->t->enableCaching){
			echo '<?$t->head()->addAtomFeed('.var_export($title,true).', '.var_export($url,true).','.var_export($schema,true).');?>';
			return $this;
		}
		if(is_array($url)){
			$params = $url;
			$route = array_shift($params);
			$url = Yii::app()->createAbsoluteUrl($route,$params,$schema);
		}
		Yii::app()->clientScript->registerLinkTag('alternate','application/atom+xml', $url,null,array('title' => $title));
		return $this;
	}

	function show(){		echo '<###title###>';
	}

	function render(&$content){		$content = str_replace('<###title###>','<title>'.CHtml::encode($this->title).'</title>',$content);
		Yii::app()->clientScript->render($content);	}}