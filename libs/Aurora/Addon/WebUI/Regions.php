<?php
//!	@file libs/Aurora/Addon/WebUI/Regions.php
//!	@brief Region-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

	use SeekableIterator;

	use Aurora\Addon\WebUI;
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

//!	Seekable iterator for instances of Aurora\Addon\WebUI GridRegion
	class GetRegions extends WORM implements SeekableIterator{

//!	object instance of Aurora::Addon::WebUI
		private $WebUI;

//!	integer Since we're allowing non-contiguous, delayed access to the region list, we need to pre-fetch the total size of the regions.
		private $total;

//!	integer Since we're allowing non-contiguous, delayed access to the region list, we need to store the Aurora::Framework::RegionFlags bitfield for future use.
		private $flags;

//!	mixed Since we're allowing non-contiguous, delayed access to the region list, we need to store the sort by region name flag for future use.
		private $sortRegionName;

//!	mixed Since we're allowing non-contiguous, delayed access to the region list, we need to store the sort by region name flag for future use.
		private $sortLocX;

//!	mixed Since we're allowing non-contiguous, delayed access to the region list, we need to store the sort by region name flag for future use.
		private $sortLocY;

//!	We're hiding this behind a registry method.
/**
*	@param object $WebUI instance of Aurora::Addon::WebUI. Used to get instances of Aurora::Addon::WebUI::GridRegion that the instance wasn't instantiated with.
*	@param integer $flags bitfield of Aurora::Framework::RegionFlags values
*	@param integer $start specifies the index that $regions starts at, if specified.
*	@param integer $total specifies the total number of regions in the grid.
*	@param mixed $sortRegionName NULL or boolean
*	@param mixed $sortLocX NULL or boolean
*	@param mixed $sortLocY NULL or boolean
*	@param mixed $regions Either NULL or an array of Aurora::Addon::WebUI::GridRegion instances.
*/
		protected function __construct(WebUI $WebUI, $flags=null, $start=0, $total=0, $sortRegionName=null, $sortLocX=null, $sortLocY=null, array $regions=null){
			if(is_string($start) === true && ctype_digit($start) === true){
				$start = (integer)$start;
			}
			if(is_string($total) === true && ctype_digit($total) === true){
				$total = (integer)$total;
			}
			if(is_string($flags) === true && ctype_digit($flags) === true){
				$flags = (integer)$flags;
			}

			if(is_integer($start) === false){
				throw new InvalidArgumentException('Start must be an integer.');
			}else if($start < 0){
				throw new InvalidArgumentException('Start must be greater than or equal to zero.');
			}else if(is_integer($total) === false){
				throw new InvalidArgumentException('Total must be an integer.');
			}else if($total < 0){
				throw new InvalidArgumentException('Total must be greater than or equal to zero.');
			}else if(is_integer($flags) === false){
				throw new InvalidArgumentException('Region Flags must be an integer.');
			}else if(RegionFlags::isValid($flags) === false){
				throw new InvalidArgumentException('Region Flags was not a valid bitfield.');
			}else if(isset($sortRegionName) === true && is_bool($sortRegionName) === false){
				throw new InvalidArgumentException('If set, the sort by region name flag must be a boolean.');
			}else if(isset($sortLocX) === true && is_bool($sortLocX) === false){
				throw new InvalidArgumentException('If set, the sort by x-axis flag must be a boolean.');
			}else if(isset($sortLocY) === true && is_bool($sortLocY) === false){
				throw new InvalidArgumentException('If set, the sort by y-axis flag must be a boolean.');
			}

			$this->WebUI          = $WebUI;
			$this->total          = $total;
			$this->flags          = $flags;
			$this->sortRegionName = $sortRegionName;
			$this->sortLocX       = $sortLocX;
			$this->sortLocY       = $sortLocY;

			$i = $start;
			if(isset($regions) === true){
				foreach($regions as $region){
					if($region instanceof GridRegion){
						$this->data[$i++] = $region;
					}else{
						throw new InvalidArgumentException('Values of instantiated regions array must be instances of Aurora::Addon::WebUI::GridRegion');
					}
				}
			}

			$this->pos = $start;
		}

//!	registry array.
		private static $registry = array();

//!	registry method
		public static function r(WebUI $WebUI, $flags, $start=0, $total=0, $sortRegionName=null, $sortLocX=null, $sortLocY=null, array $regions=null){
			if(RegionFlags::isValid($flags) === false){
				throw new InvalidArgumentException('Region Flags bitfield is invalid.');
			}else if(isset($sortRegionName) === true && is_bool($sortRegionName) === false){
				throw new InvalidArgumentException('If set, the sort by region name flag must be a boolean.');
			}else if(isset($sortLocX) === true && is_bool($sortLocX) === false){
				throw new InvalidArgumentException('If set, the sort by x-axis flag must be a boolean.');
			}else if(isset($sortLocY) === true && is_bool($sortLocY) === false){
				throw new InvalidArgumentException('If set, the sort by y-axis flag must be a boolean.');
			}
			$hash = spl_object_hash($WebUI);
			$srn = isset($sortRegionName) ? ((integer)$sortRegionName) + 1 : 0;
			$slx = isset($sortLocX) ? ((integer)$sortLocX) + 1 : 0;
			$sly = isset($sortLocY) ? ((integer)$sortLocY) + 1 : 0;
			if(isset(static::$registry[$hash]) === false){
				static::$registry[$hash] = array();
			}
			if(isset(static::$registry[$hash][$flags]) === false){
				static::$registry[$hash][$flags] = array();
			}
			if(isset(static::$registry[$hash][$flags][$srn]) === false){
				static::$registry[$hash][$flags][$srn] = array();
			}
			if(isset(static::$registry[$hash][$flags][$srn][$slx]) === false){
				static::$registry[$hash][$flags][$srn][$slx] = array();
			}

			if(isset(static::$registry[$hash][$flags][$srn][$slx][$sly]) === false){
				if(isset($total, $flags) === false){
					throw new BadMethodCallException('Cannot fetch instance of Aurora::Addon::WebUI::GetRegions from cache as it has not been created yet.');
				}
				static::$registry[$hash][$flags][$srn][$slx][$sly] = new static($WebUI, $flags, $start, $total, $sortRegionName, $sortLocX, $sortLocY, $regions);
			}

			if(is_integer($start) === false){
				throw new InvalidArgumentException('Start point must be an integer.');
			}
			static::$registry[$hash][$flags][$srn][$slx][$sly]->seek($start);
			return static::$registry[$hash][$flags][$srn][$slx][$sly];
		}

//!	Determines whether we have something in the registry or not.
/**
*	@param object $WebUI instance of Aurora::Addon::WebUI
*	@param integer $flags
*	@return boolean TRUE if we have populated the registry array, FALSE otherwise.
*/
		public static function hasInstance(WebUI $WebUI, $flags, $sortRegionName, $sortLocX, $sortLocY){
			$hash = spl_object_hash($WebUI);
			$srn = isset($sortRegionName) ? ((integer)$sortRegionName) + 1 : 0;
			$slx = isset($sortLocX) ? ((integer)$sortLocX) + 1 : 0;
			$sly = isset($sortLocY) ? ((integer)$sortLocY) + 1 : 0;

			return isset(
				static::$registry[$hash],
				static::$registry[$hash][$flags],
				static::$registry[$hash][$flags][$srn],
				static::$registry[$hash][$flags][$srn][$slx],
				static::$registry[$hash][$flags][$srn][$slx][$sly]				
			);
		}

//!	integer cursor position
		private $pos = 0;


		public function offsetSet($offset, $value){
			throw new BadMethodCallException('Instances of Aurora::Addon::WebUI::GetRegions cannot be modified from outside of the object scope.');
		}


		public function seek($to){
			if(is_string($to) === true && ctype_digit($to) === true){
				$to = (integer)$to;
			}
			if(is_integer($to) === true && $to < 0){
				$to = abs($to) % $this->count();
				$to = $this->count() - $to;
			}

			if(is_integer($to) === false){
				throw new InvalidArgumentException('Seek point must be an integer.');
			}else if($to >= $this->count()){
				throw new LengthException('Cannot seek past Aurora::Addon::WebUI::GetRegions::count()');
			}

			$this->pos = $to;
		}


		public function count(){
			return $this->total;
		}


		public function key(){
			return ($this->pos < $this->count()) ? $this->pos : null;
		}


		public function valid(){
			return ($this->key() !== null);
		}

//!	To avoid slowdowns due to an excessive amount of curl calls, we populate Aurora::Addon::WebUI::GetRegions::$data in batches of 10
/**
*	@return mixed either NULL or an instance of Aurora::Addon::WebUI::GridRegion
*/
		public function current(){
			if($this->valid() === false){
				return null;
			}else if(isset($this->data[$this->key()]) === false){
				$start   = $this->key();
				$results = $this->WebUI->GetRegions($this->flags, $start, 10, $this->sortRegionName, $this->sortLocX, $this->sortLocY, true);
				foreach($results as $region){
					$this->data[$start++] = $region;
				}
			}
			return $this->data[$this->key()];
		}

//!	advance the cursor
		public function next(){
			++$this->pos;
		}
	}
}
?>
