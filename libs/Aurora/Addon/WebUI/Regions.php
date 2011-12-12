<?php
//!	@file libs/Aurora/Addon/WebUI/Regions.php
//!	@brief Region-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

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
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $RegionID) !== 1){
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
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $EstateOwner) !== 1){
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
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $SessionID) !== 1){
				throw new InvalidArgumentException('SessionID was not a valid UUID');
			}

			$this->RegionID    = $RegionID;
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
				throw new InvalidArgumentException('Missing required properties: ' . implode(', ', $missing));
			}
			return new static($result->uuid, $result->serverHttpPort, $result->serverURI, $result->regionName, $result->regionType, $result->locX, $result->locY, $result->locZ, $result->EstateOwner, $result->sizeX, $result->sizeY, $result-> sizeZ, $result->Flags, $result->SessionID);
		}
	}
}
?>