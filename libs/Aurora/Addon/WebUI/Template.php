<?php



namespace Aurora\Addon\WebUI\Template{

	use Globals;

	function link($url){
		$queryArgs = isset($queryArgs) ? $queryArgs : array();

		$url = parse_url(Globals::i()->baseURI . $url);

		$output = './';

		if(substr($url['path'],-1) === '/'){
			$url['path'] = substr($url['path'],0,-1);
		}

		switch(Globals::i()->linkStyle){
			case 'mod_rewrite':
				$output .= substr($url['path'],1);
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
}
?>