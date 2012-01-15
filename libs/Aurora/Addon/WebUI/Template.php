<?php



namespace Aurora\Addon\WebUI\Template{

	use Globals;

	use Aurora\Addon;
	use Aurora\Addon\WORM;
	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\InvalidArgumentException;


	function link($url){
		if(is_object($url) === true){
			$args = func_get_args();
			$extra = '';
			if(count($args) > 1){
				$extra = $args[1];
			}

			if($url instanceof WebUI\abstractUser){
				return link('/world/user/' . urlencode($url->Name()) . $extra);
			}else if($url instanceof WebUI\EstateSettings){
				return link('/world/place/' . urlencode($url->EstateName()) . $extra);
			}else if($url instanceof WebUI\GridRegion){
				$Estate = Globals::i()->WebUI->GetEstate($url->EstateID());
				return link('/world/place/' . urlencode($Estate->EstateName()) . '/' . urlencode($url->RegionName()) . $extra);
			}else if($url instanceof WebUI\GroupRecord){
				return link('/world/group/' . urlencode($url->GroupName()));
			}else if($url instanceof WebUI\LandData){
				$region = Globals::i()->WebUI->GetRegion($url->RegionID());
				$estate = Globals::i()->WebUI->GetEstate($region->EstateID());
				return link('/world/place/' . urlencode($estate->EstateName()) . '/' . urlencode($region->RegionName()) . '/' . urlencode($url->Name()) . '/' . urlencode(preg_replace_callback('/0{3,}/',function($matches){return 'g' . strlen($matches[0]);}, rtrim(str_replace('-','',$url->InfoUUID()),'0'))));
			}
		}
		
		$baseURI = parse_url(Globals::i()->baseURI);
		if(substr($baseURI['path'],0,1) === '/'){
			$baseURI['path'] = substr($baseURI['path'],1);
		}
		if(substr($baseURI['path'],-1) === '/'){
			$baseURI['path'] = substr($baseURI['path'],0,-1);
		}
		
		if(substr($url,0,1) === '/'){
			$url = substr($url,1);
		}

		$url = parse_url(Globals::i()->baseURI . $url);

		if(substr($url['path'],0,1) === '/'){
			$url['path'] = substr($url['path'],1);
		}
		if(substr($url['path'],-1) === '/'){
			$url['path'] = substr($url['path'],0,-1);
		}
		
		$output = '';
		$pos = strpos($url['path'], $baseURI['path']);
		if($pos !== false && $pos >= 0){
			$output = '.';
			$url['path'] = substr($url['path'], strlen($baseURI['path']));
		}


		switch(Globals::i()->linkStyle){
			case 'mod_rewrite':
				$output .= $url['path'];
			break;
			case 'query':
			default:
				$query = '';
				if($url['path'] !== '/'){
					$query = 'path=' . urlencode($url['path']);
				}
				$url['query'] = empty($url['query']) ? $query : $url['query'] . '&amp;' . $query;
			break;
		}

		if(empty($url['query']) === false){
			$output .= '?' . $url['query'];
		}
		if(empty($url['fragment']) === false){
			$output .= '#' . $url['fragment'];
		}

		return $output;
	}

	function squishUUID($uuid){
		if(Addon\is_uuid($uuid) === false){
			throw new InvalidArgumentException('Input value must be a valid UUID');
		}
		return base_convert(str_replace('-', '', $uuid), 16, 36);
	}

	function unsquishUUID($string){
		$string = str_split(str_pad(base_convert($string, 36, 16), 32, '0', STR_PAD_LEFT), 4);
		$uuid   =
			$string[0] . $string[1] . '-' .
			$string[2] . '-' .
			$string[3] . '-' .
			$string[4] . '-' .
			$string[5] . $string[6] . $string[7]
		;
		if(Addon\is_uuid($uuid) === false){
			throw new InvalidArgumentException('Input value was not a valid squished UUID');
		}
		return $uuid;
	}

	class FormProblem extends WORM{


		public function offsetSet($offset, $value){
			if(is_string($value) === true){
				$value = trim($value);
			}

			if(is_string($offset) === false){
				throw new InvalidArgumentException('FormProblem offsets must be strings.');
			}else if(preg_match('/^[a-z][a-z0-9\-]+$/S', $offset) !== 1){
				throw new InvalidArgumentException('FormProblem offset was invalid.');
			}else if(is_string($value) === false){
				throw new InvalidArgumentException('FormProblem value must be a string.');
			}else if($value === ''){
				throw new InvalidArgumentException('FormProblem value cannot be an empty string.');
			}

			$this->data[$offset] = $value;
		}


		public static function i(){
			static $instance;
			if(isset($instance) === false){
				$instance = new static;
			}
			return $instance;
		}
	}
}
?>