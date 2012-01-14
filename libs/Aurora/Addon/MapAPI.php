<?php
//!	@file libs/Aurora/Addon/MapAPI.php
//!	@brief MapAPI code
//!	@author SignpostMarv

//	Defining exception classes in the top of the file for purposes of clarity.
namespace Aurora\Addon\MapAPI{

	use Aurora\Addon;

//!	This interface exists purely to give client code the ability to detect all MapAPI-specific exception classes in one go.
//!	The purpose of this behaviour is that instances of Aurora::Addon::MapAPI::Exception will be more or less "safe" for public consumption.
	interface Exception extends Addon\Exception{
	}

//!	MapAPI-specific RuntimeException
	class RuntimeException extends Addon\RuntimeException implements Exception{
	}

//!	MapAPI-specific InvalidArgumentException
	class InvalidArgumentException extends Addon\InvalidArgumentException implements Exception{
	}

//!	MapAPI-specific UnexpectedValueException
	class UnexpectedValueException extends Addon\UnexpectedValueException implements Exception{
	}

//!	MapAPI-specific LengthException
	class LengthException extends Addon\LengthException implements Exception{
	}

//!	MapAPI-specific BadMethodCallException
	class BadMethodCallException extends Addon\BadMethodCallException implements Exception{
	}
}

//!	Mimicking the layout of code in Aurora Sim here.
namespace Aurora\Addon{

//!	Acts as an interface to the mapapi.cs Aurora-Sim module
	class MapAPI extends abstractAPI{

//!	string Map API end point.
		protected $serviceURL;

//!	This is protected because we're going to use a registry method to access it.
/**
*	@param string $serviceURL Map API end point.
*/
		protected function __construct($serviceURL){
			$this->serviceURL = $serviceURL;
		}

//!	registry method. Sets & gets instances of Aurora::Addon::MapAPI
/**
*	@param string $serviceURL
*	@return Aurora::Addon::MapAPI
*	@see Aurora::Addon::MapAPI::__construct()
*/
		public static function r($serviceURL){
			static $registry = array();
			if(isset($registry[$serviceURL]) === false){
				$registry[$serviceURL] = new static($serviceURL);
			}
			return $registry[$serviceURL];
		}

//!	makes a call to the MapAPI API end point running on an instance of Aurora.
/**
*	Unlike the WebUI API, the Map API will not return a map of keys & values, it may only return values.
*	@param string $method
*	@param array $arguments being lazy and future-proofing API methods that have no arguments.
*	@return mixed
*/
		protected function makeCallToAPI($method, array $arguments=null, array $expectedResponse){
			if(is_string($method) === false || ctype_graph($method) === false){
				throw new InvalidArgumentException('API method parameter was invalid.');
			}
			$arguments = isset($arguments) ? $arguments : array();
			$ch = curl_init($this->serviceURL . '/' . $method . '?' . http_build_query($arguments));
			curl_setopt_array($ch, array(
				CURLOPT_HEADER         => false,
				CURLOPT_RETURNTRANSFER => true
			));
			$result = curl_exec($ch);
			curl_close($ch);
			if(is_string($result) === true){
				$result = json_decode($result);
				return $result;
			}
			throw new RuntimeException('API call failed to execute.'); // if this starts happening frequently, we'll add in some more debugging code.
		}


		public function MonolithicRegionLookup(){
			return $this->makeCallToAPI(__FUNCTION__, array(), array());
		}


		public function mapTextureURL(){
			return $this->makeCallToAPI(__FUNCTION__, array(), array());
		}
	}
}
?>
