<?php
namespace Aurora\Addon{
	use RuntimeException;
	use InvalidArgumentException;
	use UnexpectedValueException;

	use Aurora\Framework\RegionFlags;

	class WebUI{
//!	This is protected because we're going to use a registry method to access it.
/**
*	The WIREDUX_PASSWORD constant is never used without being run through md5(), so we immediately do this on instantiation.
*	@param string $serviceURL WebUI API end point.
*	@param string $password WebUI API password
*/
		protected function __construct($serviceURL, $password){
			if(is_string($serviceURL) === false){
				throw new InvalidArgumentException('WebUI API end point must be a string');
			}else if(strpos($serviceURL, 'http://') === false && strpos($serviceURL, 'https://') === false){ // for now, we're not doing any paranoid regex-based validation.
				throw new InvalidArgumentException('WebUI API end point must begin with http:// or https://');
			}else if(is_string($password) === false){
				throw new InvalidArgumentException('WebUI API password should be a string');
			}
			$this->serviceURL = $serviceURL;
			$this->password   = md5($password); // I suppose we could be extremely paranoid and unset $password after this if we really wanted.
		}

//!	string WebUI API end point.
		protected $serviceURL;

//!	string WebUI API password
		protected $password;

//!	registry method. Sets & gets instances of Aurora::Addon::WebUI
/**
*	@param string $serviceURL
*	@param mixed $password should be NULL if getting, otherwise should be string. defaults to NULL.
*	@return Aurora::Addon::WebUI
*	@see Aurora::Addon::WebUI::__construct()
*/
		public static function r($serviceURL, $password=null){
			static $registry = array();
			if(isset($registry[$serviceURL]) === false){
				if(isset($password) === false){
					throw new InvalidArgumentException('Cannot create an instance of WebUI API interface without a password');
				}
				$instance = new static($serviceURL, $password); // we're assigning it to a local variable as a lazy means of avoiding doing valid type checks for array keys. any errors that would crop up about that would trigger InvalidArgumentException in Aurora::Addon::WebUI::__construct()
				$registry[$serviceURL] = $instance; // any child implementation of Aurora::Addon::WebUI that breaks this laziness is on their own at this point.
			}
			return $registry[$serviceURL];
		}

//!	makes a call to the WebUI API end point running on an instance of Aurora.
/**
*	@param string $method
*	@param array $arguments being lazy and future-proofing API methods that have no arguments.
*	@return mixed All instances of do_post_request() in Aurora-WebUI that act upon the result call json_decode() on the $result prior to acting on it, so we save ourselves some time and execute json_decode() here.
*/
		protected function makeCallToAPI($method, array $arguments=null){
			if(is_string($method) === false || ctype_graph($method) === false){
				throw new InvalidArgumentException('API method parameter was invalid.');
			}
			$arguments = isset($arguments) ? $arguments : array();
			$arguments = array_merge(array(
				'Method'      => $method,
				'WebPassword' => $this->password
			), $arguments);
			$ch = curl_init($this->serviceURL);
			curl_setopt_array($ch, array(
				CURLOPT_HEADER         => false,
				CURLOPT_POST           => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS     => implode(',', array(json_encode($arguments)))
			));
			$result = curl_exec($ch);
			curl_close($ch);
			if(is_string($result) === true){
				$result = json_decode($result);
				if(is_object($result) === false){
					throw new UnexpectedValueException('API result expected to be object, ' . gettype($result) . ' found.');
				}
				return $result;
			}
			throw new RuntimeException('API call failed to execute.'); // if this starts happening frequently, we'll add in some more debugging code.
		}

		public function OnlineStatus(){
			return $this->makeCallToAPI('OnlineStatus');
		}

		public function CheckIfUserExists($name){
			if(is_string($name) === false){
				throw new InvalidArgumentException('Name should be a string');
			}
			$result = $this->makeCallToAPI('CheckIfUserExists', array('Name'=>$name));
			if(isset($result->Verified) === false){
				throw new UnexpectedValueException('Verified property was not set on API result');
			}else if(is_bool($result->Verified) === false){
				if(is_string($result->Verified) === true){
					$result = strtolower($result->Verified);
					if($result === 'true' || $result === 'false'){
						return ($result === 'true');
					}
				}
				throw new UnexpectedValueException('Verified property from API result should be a boolean or boolean as string (true/false)');
			}
			return $result->Verified;
		}

		public function GetRegions($flags){ // this doesn't work at the moment, there's a bug in the c# on the php5 branch of Aurora-WebUI
			if(is_integer($flags) === false){
				throw new InvalidArgumentException('RegionFlags argument should be supplied as integer.');
			}else if($flags < 0){
				throw new InvalidArgumentException('RegionFlags cannot be less than zero');
			}else if(RegionFlags::isValid($flags) === false){ // Aurora::Framework::RegionFlags::isValid() does do a check for integerness, but we want to throw a different exception message if it is an integer.
				throw new InvalidArgumentException('RegionFlags value is invalid, aborting call to API');
			}
			return $this->makeCallToAPI('GetRegions', array('RegionFlags'=>$flags));
		}
	}
}
?>