<?php
class EClientScript extends CClientScript{	public $compressJs = false;
	public $compressCss = false;

	public $combineJs = true;
	public $combineCss = true;

	public $excludeFiles = array();

	public $ttlDays = 7;
	public $prefix = 'c_';

	public $jsMinClass = 'application.vendors.ExtendedClientScript.jsmin.JSMin';
	public $cssMinClass = 'application.vendors.ExtendedClientScript.cssmin.cssmin';

	public $publishedUrl;
	public $publishedPath;
	public $documentRoot;



	public $cssMinFilters = array
	(
        'ImportImports'                 => false,
        'RemoveComments'                => true,
        'RemoveEmptyRulesets'           => true,
        'RemoveEmptyAtBlocks'           => true,
        'ConvertLevel3AtKeyframes'      => false,
        'ConvertLevel3Properties'       => false,
        'Variables'                     => true,
        'RemoveLastDelarationSemiColon' => true
	);

	public $cssMinPlugins = array
	(
		'Variables'                => true,
		'ConvertFontWeight'        => true,
		'ConvertHslColors'         => true,
		'ConvertRgbColors'         => true,
		'ConvertNamedColors'       => true,
		'CompressColorValues'      => true,
		'CompressUnitValues'       => true,
		'CompressExpressionValues' => true,
	);

	protected $_changesHash = '';
	protected $_renewFile;

	public function init()
	{
		parent::init();

		Yii::import($this->jsMinClass);
		Yii::import($this->cssMinClass);


		$this->publishedUrl = Yii::app()->assetManager->getPublishedUrl(dirname(__FILE__));
		$this->publishedPath = Yii::app()->assetManager->getPublishedPath(dirname(__FILE__));
		if(!is_dir($this->publishedPath))
			mkdir($this->publishedPath,0777, true);
		$this->documentRoot = realpath($_SERVER['DOCUMENT_ROOT']);

		if(YII_DEBUG)
			$this->clean('all');
		elseif(rand(0, 10) == 0)
			$this->clean();
	}

	public function clean($type = ''){		$files = CFileHelper::findFiles($this->publishedPath, array('level'=> 0));

		foreach($files as $file)
		{
			if ($this->fileTTL($file) || $type == 'all')
				unlink($file);
		}
	}

	protected function fileTTL($file)
	{
		$ttl = $this->ttlDays * 60 * 60 * 24;
		return ((fileatime($file) + $ttl) < time()) ? true : false;
	}

	public function renderHead(&$output)
	{


		$this->renderJs(parent::POS_HEAD);
		$this->renderCss();

		parent::renderHead($output);
	}

	public function renderBodyBegin(&$output)
	{
		$this->renderJs(parent::POS_BEGIN);
		parent::renderBodyBegin($output);
	}


	public function renderBodyEnd(&$output)
	{
		$this->renderJs(parent::POS_END);
		parent::renderBodyEnd($output);
	}


	protected function renderJs($pos)
	{
		if (!$this->combineJs)return;

		if (isset($this->scriptFiles[$pos]) && count($this->scriptFiles[$pos]) !==  0)
		{
			$jsFiles = $this->scriptFiles[$pos];

			foreach ($jsFiles as &$fileName)
				(!empty($this->excludeFiles) && in_array($fileName, $this->excludeFiles) || $this->isRemoteFile($fileName)) AND $fileName = false;

			$jsFiles = array_filter($jsFiles);

			$this->combineJs($jsFiles);
		}
	}

	protected function renderCss(){		if (!$this->combineCss)return;

		$cssFiles = array();
		foreach ($this->cssFiles as $url => $media)
			if(! $this->isRemoteFile($url))
				$cssFiles[$media][] = $url;

		foreach ($cssFiles as $media => $urls)
			$this->combineCss($urls);

	}

	protected function isRemoteFile($file) {
		return (strpos($file, 'http://') === 0 || strpos($file, 'https://') === 0) ? true : false;
	}

	protected function getFileInfo($urls, $type){		sort($urls);
		reset($urls);
		$params['urls'] = $urls;
		$params['mtime'] = array();
		$params['excludeFiles'] = $this->excludeFiles;

		if($type == 'js'){
			$params['combineJs'] = $this->combineJs;
			$params['compressJs'] = $this->compressJs;
		}else{			$params['combineCss'] = $this->combineCss;
			$params['compressCss'] = $this->compressCss;		}

		foreach ($urls as $file){			$fileName = $this->documentRoot.DIRECTORY_SEPARATOR.$file;
			if(file_exists($fileName)){				$params['mtime'][] = filemtime($fileName);
			}
		}

		$fileName = $this->prefix.md5(serialize($params)).".".$type;

		return array(
			'name'=> $fileName,
			'url' => $this->publishedUrl."/".$fileName,
			'path'=> $this->publishedPath.DIRECTORY_SEPARATOR.$fileName,
			'exists'=>file_exists($this->publishedPath.DIRECTORY_SEPARATOR.$fileName),
		);
	}

	protected function  combineJs($urls){		$fileInfo = $this->getFileInfo($urls, 'js');

		if(!$fileInfo['exists']){			$combinedFile = '';
			foreach ($urls as $file){
				if(file_exists($this->documentRoot.DIRECTORY_SEPARATOR.$file))
					$combinedFile .= "\n".file_get_contents($this->documentRoot.DIRECTORY_SEPARATOR.$file);
				else
					$combinedFile .= "\n /* Not Found: ".$file."*/";
			}

			if ($this->compressJs)
				$combinedFile = $this->minifyJs($combinedFile);

			file_put_contents($fileInfo['path'], $combinedFile);
		}

		foreach ($urls as $url)
			$this->scriptMap[basename($url)] = $fileInfo['url'];

		$this->remapScripts();
	}

	protected function combineCss($urls){		$fileInfo = $this->getFileInfo($urls, 'css');

		if(!$fileInfo['exists']){
			$combinedFile = '';
			foreach ($urls as $file)
				$combinedFile .= "\n".$this->getCssContent($this->documentRoot.DIRECTORY_SEPARATOR.$file);

			if ($this->compressCss)
				$combinedFile = $this->minifyCss($combinedFile);

			file_put_contents($fileInfo['path'], $combinedFile);
		}

		foreach ($urls as $url)
			$this->scriptMap[basename($url)] = $fileInfo['url'];

		$this->remapScripts();
	}

	static function replaceCssUrl($url, $cssPath){
		if(strpos($url, ":")!==false)
			return $url;
		$url = trim(stripslashes($url), "'\" \r\n\t");
		if(substr($url, 0, 1) == "/")
			return $url;
		return "'".$cssPath.'/'.$url."'";
	}

	protected function getCssContent($fileName){		$file = realpath($fileName);
		if($file == false)return '/*Not Found: '.basename($fileName).'*/';
		$dir = dirname($file);

		$cssPath = substr($dir, strlen($this->documentRoot));
		$cssPath = str_replace(DIRECTORY_SEPARATOR, '/', $cssPath);
		$cssPath = '/'.trim($cssPath,'/');
		$content = file_get_contents($file);
		return preg_replace('#([;\s:]+url\s*\(\s*)([^\)]+)\)#sie', "'\\1'.EClientScript::replaceCssUrl('\\2', '".AddSlashes($cssPath)."').')'", $content);
	}



	protected function minifyJs($js)
	{
		return JSMin::minify($js);
	}

	protected function minifyCss($css)
	{
		return cssmin::minify($css, $this->cssMinFilters, $this->cssMinPlugins);
	}

	public function render(&$output){
		Yii::app()->firewall->render($output);
		parent::render($output);
	}
}