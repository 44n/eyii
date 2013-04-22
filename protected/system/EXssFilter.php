<?
class EXssFilter extends CFilter{	public  $clean   = '*';
	public  $type    = 'strict';
	public  $actions = '*';

	protected function preFilter($filterChain){		$this->actions = trim(strtoupper($this->actions));
		if($this->actions != '*' && $this->actions != 'ALL' && !in_array($filterChain->action->id,explode(',',$this->actions)))
		{
			return true;
		}

		$this->clean  = trim(strtoupper($this->clean));
		$this->type   = trim(strtoupper($this->type));

		if($this->clean === 'ALL' || $this->clean === '*')
		{
			$this->clean = 'GET,POST,REQUEST,COOKIE,FILES';
		}

		$cleanData = explode(',',$this->clean);
		foreach($cleanData as $item){			$item = strtoupper(trim($item));

			if($item == 'GET'){				$_GET = $this->clean($_GET, $this->type);
			}elseif($item == 'POST'){
				$_POST = $this->clean($_POST, $this->type);
			}elseif($item == 'REQUEST'){
				$_REQUEST = $this->clean($_REQUEST, $this->type);
			}elseif($item == 'COOKIE'){
				$_COOKIE = $this->clean($_COOKIE, $this->type);
			}elseif($item == 'FILES'){
				$_FILES = $this->clean($_FILES, $this->type);
			}
		}
		return true;
	}

	public static function clean($data, $type = 'STRICT')
	{
		if(is_array($data) && count($data))
		{
			foreach($data as $k => $v)
			{
				$data[$k] = self::clean($v, $type);
			}
			return $data;
		}elseif(is_array($data)){			return $data;		}



		if(trim($data) === '')
		{
			return $data;
		}


		switch ($type)
		{
			case 'SOFT':
				$data = htmlentities($data,ENT_QUOTES,'UTF-8');
			break;
			case 'NONE':

			break;
			default:/*strict*/
				$data = strip_tags($data);
		}

		// xss_clean function from Kohana framework 2.3.4
		$data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
		$data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
		$data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
		$data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
		// Remove any attribute starting with "on" or xmlns
		$data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
		// Remove javascript: and vbscript: protocols
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
		$data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
		// Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
		$data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
		// Remove namespaced elements (we do not need them)
		$data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
		do
		{
			// Remove really unwanted tags
			$old_data = $data;
			$data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
		}
		while ($old_data !== $data);
		return $data;
	}
}