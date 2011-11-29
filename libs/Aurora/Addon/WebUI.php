<?php
//!	Mimicking the layout of code in Aurora Sim here.
namespace Aurora\Addon{
	use RuntimeException;
	use InvalidArgumentException;
	use UnexpectedValueException;

	use Aurora\Framework\RegionFlags;

//!	Now you might think this class should be a singleton loading config values from constants instead of a registry method, but Marv has plans. MUAHAHAHAHA.
	class WebUI{
//!	string Regular expression for validating UUIDs (put here until this operation gets performed elsewhere.
		const regex_UUID = '/^[a-fA-F0-9]{8}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{4}\-[a-fA-F0-9]{12}$/';

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

//!	Determines whether the specified username exists in the AuroraSim database.
/**
*	@param string $name the username we want to check exists
*	@return boolean TRUE if the user exists, FALSE otherwise.
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*/
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

//!	Determines the online status of the grid and whether logins are enabled.
/**
*	@return Aurora::Addon::WebUI::OnlineStatus
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*/
		public function OnlineStatus(){
			$result = $this->makeCallToAPI('OnlineStatus');
			if(isset($result->Online, $result->LoginEnabled) === false){
				$missing = array();
				if(isset($result->Online) === false){
					$missing[] = 'Online';
				}
				if(isset($result->LoginEnabled) === false){
					$missing[] = 'LoginEnabled';
				}
				throw new UnexpectedValueException('API result missing required properties: ' . implode(', ', $missing));
			}
			return new WebUI\OnlineStatus($result->Online, $result->LoginEnabled);
		}

//!	Attempt to set the WebLoginKey for the specified user
/**
*	@param string $for UUID of the desired user to specify a WebLoginKey for.
*	@return string the WebLoginKey generated by the server.
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*/
		public function SetWebLoginKey($for){
			if(is_string($for) === false){
				throw new InvalidArgumentException('UUID of user must be specified as a string');
			}else if(preg_match(self::regex_UUID, $for) !== 1){
				throw new InvalidArgumentException('Specified string was not a valid UUID');
			}
			$result = $this->makeCallToAPI('SetWebLoginKey', array('PrincipalID'=>$for));
			if(isset($result->WebLoginKey) === false){
				throw new UnexpectedValueException('WebLoginKey value not present on API result, API call was made but not successful.');
			}else if(is_string($result->WebLoginKey) === false){
				throw new UnexpectedValueException('WebLoginKey value present on API result, but value was not a string as expected.');
			}else if(preg_match(self::regex_UUID, $result->WebLoginKey) !== 1){
				throw new UnexpectedValueException('WebLoginKey value present on API result, but value was not a valid UUID.');
			}
			return $result->WebLoginKey;
		}

//!	Get a list of regions in the AuroraSim install that match the specified flags.
/**
*	@param integer A bitfield corresponding to constants in Aurora::Framework::RegionFlags
*	@return array Currently returns an array, although may return a interface-specific Iterator in the future.
*	@see Aurora::Addon::WebUI::makeCallToAPI()
*	@see Aurora::Addon::WebUI::fromEndPointResult()
*/
		public function GetRegions($flags){ // this doesn't work at the moment, there's a bug in the c# on the php5 branch of Aurora-WebUI
			if(is_integer($flags) === false){
				throw new InvalidArgumentException('RegionFlags argument should be supplied as integer.');
			}else if($flags < 0){
				throw new InvalidArgumentException('RegionFlags cannot be less than zero');
			}else if(RegionFlags::isValid($flags) === false){ // Aurora::Framework::RegionFlags::isValid() does do a check for integerness, but we want to throw a different exception message if it is an integer.
				throw new InvalidArgumentException('RegionFlags value is invalid, aborting call to API');
			}
			$result = $this->makeCallToAPI('GetRegions', array('RegionFlags'=>$flags));
			$response = array();
			foreach($result as $val){
				$response[] = WebUI\GridRegion::fromEndPointResult($val);
			}
			return $response;
		}
	}
}


//!	Code specific to the WebUI
namespace Aurora\Addon\WebUI{
	use InvalidArgumentException;

	use Aurora\Services\Interfaces;
	use Aurora\Framework\RegionFlags;

//!	Implementation of Aurora::Services::Interfaces::GridRegion
	class GridRegion implements Interfaces\GridRegion{

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::RegionID()
		protected $RegionID;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionID
		public function RegionID(){
			return $this->RegionID;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::HttpPort()
		protected $HttpPort;
//!	@see Aurora::Addon::WebUI::GridRegion::$HttpPort
		public function HttpPort(){
			return $this->HttpPort;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::ServerURI()
		protected $ServerURI;
//!	@see Aurora::Addon::WebUI::GridRegion::$ServerURI
		public function ServerURI(){
			return $this->ServerURI;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::RegionName()
		protected $RegionName;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionName
		public function RegionName(){
			return $this->RegionName;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::RegionType()
		protected $RegionType;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionType
		public function RegionType(){
			return $this->RegionType;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionLocX()
		protected $RegionLocX;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionLocX
		public function RegionLocX(){
			return $this->RegionLocX;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionLocY()
		protected $RegionLocY;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionLocY
		public function RegionLocY(){
			return $this->RegionLocY;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionLocZ()
		protected $RegionLocZ;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionLocZ
		public function RegionLocZ(){
			return $this->RegionLocZ;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::EstateOwner()
		protected $EstateOwner;
//!	@see Aurora::Addon::WebUI::GridRegion::$EstateOwner
		public function EstateOwner(){
			return $this->EstateOwner;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionSizeX()
		protected $RegionSizeX;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionSizeX
		public function RegionSizeX(){
			return $this->RegionSizeX;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionSizeY()
		protected $RegionSizeY;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionSizeY
		public function RegionSizeY(){
			return $this->RegionSizeY;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::RegionSizeZ()
		protected $RegionSizeZ;
//!	@see Aurora::Addon::WebUI::GridRegion::$RegionSizeZ
		public function RegionSizeZ(){
			return $this->RegionSizeZ;
		}

//!	integer
//!	@see Aurora::Addon::WebUI::GridRegion::Flags()
		protected $Flags;
//!	@see Aurora::Addon::WebUI::GridRegion::$Flags
		public function Flags(){
			return $this->Flags;
		}

//!	string
//!	@see Aurora::Addon::WebUI::GridRegion::SessionID()
		protected $SessionID;
//!	@see Aurora::Addon::WebUI::GridRegion::$SessionID
		public function SessionID(){
			return $this->SessionID;
		}

//!	Converts integer-as-string to integer
		final protected static function stringMaybe2Integer(& $val){
			if(is_string($val) === true && ctype_digit($val) === true){
				$val = (integer)$val;
			}
		}

//!	We're making this a protected method because we're going to be using at least one public static method to deserialise some data for this class.
/**
*	@param integer $HttpPort
*	@param string $ServerURI
*	@param string $RegionName
*	@param string $RegionType
*	@param integer $RegionLocX
*	@param integer $RegionLocY
*	@param integer $RegionLocZ
*	@param string $EstateOwner
*	@param integer $RegionSizeX
*	@param integer $RegionSizeY
*	@param integer $RegionSizeZ
*	@param integer $Flags
*	@param string $SessionID
*/
		protected function __construct($RegionID, $HttpPort, $ServerURI, $RegionName, $RegionType, $RegionLocX, $RegionLocY, $RegionLocZ=0, $EstateOwner='00000000-0000-0000-0000-000000000000', $RegionSizeX=256, $RegionSizeY=256, $RegionSizeZ=256, $Flags=0, $SessionID='00000000-0000-0000-0000-000000000000'){
			self::stringMaybe2Integer($HttpPort);
			self::stringMaybe2Integer($RegionLocX);
			self::stringMaybe2Integer($RegionLocY);
			self::stringMaybe2Integer($RegionLocZ);
			self::stringMaybe2Integer($RegionSizeX);
			self::stringMaybe2Integer($RegionSizeY);
			self::stringMaybe2Integer($RegionSizeZ);
			self::stringMaybe2Integer($Flags);

			if(is_string($RegionID) === false){
				throw new InvalidArgumentException('RegionID should be a string');
			}else if(preg_match(Aurora\Addon\WebUI::regex_UUID, $RegionID) !== 1){
				throw new InvalidArgumentException('RegionID was not a valid UUID');
			}else if(is_integer($HttpPort) === false){
				throw new InvalidArgumentException('HttpPort should be an integer');
			}else if($HttpPort < 0){
				throw new InvalidArgumentException('HttpPort should be greater than zero'); 
			}else if(is_string($ServerURI) === false){
				throw new InvalidArgumentException('ServerURI should be a string');
			}else if(strpos($ServerURI, 'http://') !== 0 && strpos($ServerURI, 'https://') !== 0){
				throw new InvalidArgumentException('ServerURI was not http or https');
			}else if(is_integer($RegionLocX) === false){
				throw new InvalidArgumentException('RegionLocX was not an integer');
			}else if(is_integer($RegionLocY) === false){
				throw new InvalidArgumentException('RegionLocY was not an integer');
			}else if(is_integer($RegionLocZ) === false){
				throw new InvalidArgumentException('RegionLocZ was not an integer');
			}else if(is_string($EstateOwner) === false){
				throw new InvalidArgumentException('EstateOwner was not a string');
			}else if(preg_match(Aurora\Addon\WebUI::regex_UUID, $EstateOwner) !== 1){
				throw new InvalidArgumentException('EstateOwner was not a valid UUID');
			}else if(is_integer($RegionSizeX) === false){
				throw new InvalidArgumentException('RegionSizeX was not an integer');
			}else if(is_integer($RegionSizeY) === false){
				throw new InvalidArgumentException('RegionSizeY was not an integer');
			}else if(is_integer($RegionSizeZ) === false){
				throw new InvalidArgumentException('RegionSizeZ was not an integer');
			}else if(is_integer($Flags) === false){
				throw new InvalidArgumentException('Flags was not an integer');
			}else if(RegionFlags::isValid($Flags) === false){
				throw new InvalidArgumentException('Flags was not a valid RegionFlags bitfield');
			}else if(is_string($SessionID) === false){
				throw new InvalidArgumentException('SessionID was not a string');
			}else if(preg_match(Aurora\Addon\WebUI::regex_UUID, $SessionID) !== 1){
				throw new InvalidArgumentException('SessionID was not a valid UUID');
			}

			$this->HttpPort    = $HttpPort;
			$this->ServerURI   = $ServerURI;
			$this->RegionName  = $RegionName;
			$this->RegionType  = $RegionType;
			$this->RegionLocX  = $RegionLocX;
			$this->RegionLocY  = $RegionLocY;
			$this->RegionLocZ  = $RegionLocZ;
			$this->EstateOwner = $EstateOwner;
			$this->RegionSizeX = $RegionSizeX;
			$this->RegionSizeY = $RegionSizeY;
			$this->RegionSizeZ = $RegionSizeZ;
			$this->Flags       = $Flags;
			$this->SessionID   = $SessionID;
		}

//!	For converting WebUI API end point results from json_decode()'d objects to instances of Aurora::Addon::WebUI::GridRegion
//!	@param object $result
//!	@return object instance of Aurora::Addon::WebUI::GridRegion
		public static function fromEndPointResult($result){
			if(is_object($result) === false){
				throw new InvalidArgumentException('result should be object');
			}else if(isset($result->uuid, $result->serverHttpPort, $result->serverURI, $result->regionName, $result->regionType, $result->locX, $result->locY, $result->locZ, $result->EstateOwner, $result->sizeX, $result->sizeY, $result->sizeZ, $result->Flags, $result->SessionID) === false){
				$missing = array();
				if(isset($result->uuid) === false){
					$missing[] = 'uuid';
				}
				if(isset($result->serverHttpPort) === false){
					$missing[] = 'serverHttpPort';
				}
				if(isset($result->serverURI) === false){
					$missing[] = 'serverURI';
				}
				if(isset($result->regionName) === false){
					$missing[] = 'regionName';
				}
				if(isset($result->regionType) === false){
					$missing[] = 'regionType';
				}
				if(isset($result->locX) === false){
					$missing[] = 'locX';
				}
				if(isset($result->locY) === false){
					$missing[] = 'locY';
				}
				if(isset($result->locZ) === false){
					$missing[] = 'locZ';
				}
				if(isset($result->EstateOwner) === false){
					$missing[] = 'EstateOwner';
				}
				if(isset($result->sizeX) === false){
					$missing[] = 'sizeX';
				}
				if(isset($result->sizeY) === false){
					$missing[] = 'sizeY';
				}
				if(isset($result->sizeZ) === false){
					$missing[] = 'sizeZ';
				}
				if(isset($result->Flags) === false){
					$missing[] = 'Flags';
				}
				if(isset($result->SessionID) === false){
					$missing[] = 'SessionID';
				}
				var_dump($result);
				throw new InvalidArgumentException('Missing required properties: ' . implode(', ', $missing));
			}
			return new static($result->uuid, $result->serverHttpPort, $result->serverURI, $result->regionName, $result->regionType, $result->locX, $result->locY, $result->locZ, $result->EstateOwner, $result->sizeX, $result->sizeY, $result-> sizeZ, $result->Flags, $result->SessionID);
		}
	}

//! class for representing the online status result of an API query.
/**
*	Now the simplest approach would just be to return the object that from json_decode() in Aurora::Addon::WebUI::makeCallToAPI(), but stdClass doesn't prevent properties being removed or overwritten.
*/
	class OnlineStatus{
//!	Constructor used by Aurora::Addon::WebUI::OnlineStatus()
/**
*	We could- if we wanted to be really paranoid- examine the backtrace to make sure that the constructor is online called from within Aurora::Addon::WebUI::OnlineStatus(), but that would be a waste of resources.
*	@param boolean $Online TRUE means the grid is online, FALSE otherwise.
*	@param boolean $LoginEnabled TRUE means the grid has logins enabled, FALSE otherwise.
*	@see Aurora::Addon::WebUI::OnlineStatus::maybe2bool()
*	@see Aurora::Addon::WebUI::OnlineStatus::$Online
*	@see Aurora::Addon::WebUI::OnlineStatus::$LoginEnabled
*/
		public function __construct($Online, $LoginEnabled){
			self::maybe2bool($Online);
			self::maybe2bool($LoginEnabled);

			if(is_bool($Online) === false){
				throw new InvalidArgumentException('Online should be boolean');
			}else if(is_bool($LoginEnabled) === false){
				throw new InvalidArgumentException('LoginEnabled should be boolean');
			}

			$this->Online       = $Online;
			$this->LoginEnabled = $LoginEnabled;
		}

//!	boolean
//!	@see Aurora::Addon::WebUI::OnlineStatus::Online()
		protected $Online;
//!	@see Aurora::Addon::WebUI::OnlineStatus::$Online
//!	@return boolean TRUE if grid is online, FALSE otherwise.
		public function Online(){
			return $this->Online;
		}

//!	boolean
//!	@see Aurora::Addon::WebUI::OnlineStatus::LoginEnabled()
		protected $LoginEnabled;
//!	@return boolean TRUE if logins are enabled, FALSE otherwise.
//!	@see Aurora::Addon::WebUI::OnlineStatus::$LoginEnabled
		public function LoginEnabled(){
			return $this->LoginEnabled;
		}

//!	deduplication of code to convert arguments to Aurora::Addon::WebUI::OnlineStatus::__construct() to boolean.
//!	@param mixed $val passed by reference
		final protected static function maybe2bool(& $val){
			if(is_integer($val) === true){
				$val = ($val !== 0);
			}
		}
	}
}
?>