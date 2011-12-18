<?php



namespace Aurora\Addon\WebUI\Template{

	use Globals;

	use Aurora\Addon\WebUI;
	use Aurora\Addon\WebUI\InvalidArgumentException;


	function link($url){
		$queryArgs = isset($queryArgs) ? $queryArgs : array();

		$url = parse_url(Globals::i()->baseURI . $url);

		$output = './';

		if(substr($url['path'],0,1) === '/'){
			$url['path'] = substr($url['path'],1);
		}
		if(substr($url['path'],-1) === '/'){
			$url['path'] = substr($url['path'],0,-1);
		}

		switch(Globals::i()->linkStyle){
			case 'mod_rewrite':
				$output .= $url['path'];
			break;
			case 'query':
			default:
				$query = '';
				if($url['path'] !== '/'){
					$query = 'path=' . urlencode(substr($url['path'],1));
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


	class FormProblem extends WebUI\WORM{


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