<?php
//!	@file libs/Aurora/Addon/WebUI/Regions.php
//!	@brief Region-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

	use SeekableIterator;

	use Aurora\Addon\WebUI;
	use Aurora\Framework;
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

//!	Implementation of Aurora::Framework::EstateSettings	
	class EstateSettings implements Framework\EstateSettings{

//!	integer EstateID
//!	@see Aurora::Addon::WebUI::EstateID()
		protected $EstateID;
//!	@see Aurora::Addon::WebUI::EstateID
		public function EstateID(){
			return $this->EstateID;
		}

//!	string Name of estate
//!	@see Aurora::Addon::WebUI::EstateName()
		protected $EstateName;
//!	@see Aurora::Addon::WebUI::EstateName
		public function EstateName(){
			return $this->EstateName;
		}

//!	boolean TRUE if abuse reports should be emailed to the estate owner, FALSE otherwise
//!	@see Aurora::Addon::WebUI::AbuseEmailToEstateOwner()
		protected $AbuseEmailToEstateOwner;
//!	@see Aurora::Addon::WebUI::AbuseEmailToEstateOwner
		public function AbuseEmailToEstateOwner(){
			return $this->AbuseEmailToEstateOwner;
		}

//!	boolean TRUE if anonymous users should be denid access, FALSE otherwise
//!	@see Aurora::Addon::WebUI::DenyAnonymous()
		protected $DenyAnonymous;
//!	@see Aurora::Addon::WebUI::DenyAnonymous
		public function DenyAnonymous(){
			return $this->DenyAnonymous;
		}

//!	boolean TRUE if teleporting out of the estate resets the home location, FALSE otherwise
//!	@see Aurora::Addon::WebUI::ResetHomeOnTeleport()
		protected $ResetHomeOnTeleport;
//!	@see Aurora::Addon::WebUI::ResetHomeOnTeleport
		public function ResetHomeOnTeleport(){
			return $this->ResetHomeOnTeleport;
		}

//!	boolean TRUE if the sun should be fixed within the estate, FALSE otherwise
//!	@see Aurora::Addon::WebUI::FixedSun()
		protected $FixedSun;
//!	@see Aurora::Addon::WebUI::FixedSun
		public function FixedSun(){
			return $this->FixedSun;
		}

//!	boolean TRUE if non-transacted users should be denied?
//!	@see Aurora::Addon::WebUI::DenyTransacted()
		protected $DenyTransacted;
//!	@see Aurora::Addon::WebUI::DenyTransacted
		public function DenyTransacted(){
			return $this->DenyTransacted;
		}

//!	boolean TRUE to block dwell calculations ?
//!	@see Aurora::Addon::WebUI::BlockDwell()
		protected $BlockDwell;
//!	@see Aurora::Addon::WebUI::BlockDwell
		public function BlockDwell(){
			return $this->BlockDwell;
		}

//!	boolean TRUE if unverified users should be denied ?
//!	@see Aurora::Addon::WebUI::DenyIdentified()
		protected $DenyIdentified;
//!	@see Aurora::Addon::WebUI::DenyIdentified
		public function DenyIdentified(){
			return $this->DenyIdentified;
		}

//!	boolean TRUE to allow voice within the estate, FALSE otherwise
//!	@see Aurora::Addon::WebUI::AllowVoice()
		protected $AllowVoice;
//!	@see Aurora::Addon::WebUI::AllowVoice
		public function AllowVoice(){
			return $this->AllowVoice;
		}

//!	boolean TRUE to use global time, FALSE otherwise
//!	@see Aurora::Addon::WebUI::UseGlobalTime()
		protected $UseGlobalTime;
//!	@see Aurora::Addon::WebUI::UseGlobalTime
		public function UseGlobalTime(){
			return $this->UseGlobalTime;
		}

//!	integer grid currency price per meter
//!	@see Aurora::Addon::WebUI::PricePerMeter()
		protected $PricePerMeter;
//!	@see Aurora::Addon::WebUI::PricePerMeter
		public function PricePerMeter(){
			return $this->PricePerMeter;
		}

//!	boolean TRUE if land within estate is tax-free, FALSE otherwise
//!	@see Aurora::Addon::WebUI::TaxFree()
		protected $TaxFree;
//!	@see Aurora::Addon::WebUI::TaxFree
		public function TaxFree(){
			return $this->TaxFree;
		}

//!	boolean TRUE to enable direct teleport within the estate, FALSE otherwise
//!	@see Aurora::Addon::WebUI::AllowDirectTeleport()
		protected $AllowDirectTeleport;
//!	@see Aurora::Addon::WebUI::AllowDirectTeleport
		public function AllowDirectTeleport(){
			return $this->AllowDirectTeleport;
		}

//!	mixed NULL or redirect grid position x-axis integer
//!	@see Aurora::Addon::WebUI::RedirectGridX()
		protected $RedirectGridX;
//!	@see Aurora::Addon::WebUI::RedirectGridX
		public function RedirectGridX(){
			return $this->RedirectGridX;
		}

//!	mixed NULL or redirect grid position y-axis integer
//!	@see Aurora::Addon::WebUI::RedirectGridY()
		protected $RedirectGridY;
//!	@see Aurora::Addon::WebUI::RedirectGridY
		public function RedirectGridY(){
			return $this->RedirectGridY;
		}

//!	integer Parent Estate ID
//!	@see Aurora::Addon::WebUI::ParentEstateID()
		protected $ParentEstateID;
//!	@see Aurora::Addon::WebUI::ParentEstateID
		public function ParentEstateID(){
			return $this->ParentEstateID;
		}

//!	float Sun Position
//!	@see Aurora::Addon::WebUI::SunPosition()
		protected $SunPosition;
//!	@see Aurora::Addon::WebUI::SunPosition
		public function SunPosition(){
			return $this->SunPosition;
		}

//!	boolean ??
//!	@see Aurora::Addon::WebUI::EstateSkipScripts()
		protected $EstateSkipScripts;
//!	@see Aurora::Addon::WebUI::EstateSkipScripts
		public function EstateSkipScripts(){
			return $this->EstateSkipScripts;
		}

//!	float ??
//!	@see Aurora::Addon::WebUI::BillableFactor()
		protected $BillableFactor;
//!	@see Aurora::Addon::WebUI::BillableFactor
		public function BillableFactor(){
			return $this->BillableFactor;
		}

//!	boolean TRUE if access to the land within the estate is implicit, FALSE otherwise
//!	@see Aurora::Addon::WebUI::PublicAccess()
		protected $PublicAccess;
//!	@see Aurora::Addon::WebUI::PublicAccess
		public function PublicAccess(){
			return $this->PublicAccess;
		}

//!	string abuse report email address
//!	@see Aurora::Addon::WebUI::AbuseEmail()
		protected $AbuseEmail;
//!	@see Aurora::Addon::WebUI::AbuseEmail
		public function AbuseEmail(){
			return $this->AbuseEmail;
		}

//!	string Estate owner UUID
//!	@see Aurora::Addon::WebUI::EstateOwner()
		protected $EstateOwner;
//!	@see Aurora::Addon::WebUI::EstateOwner
		public function EstateOwner(){
			return $this->EstateOwner;
		}

//!	boolean TRUE if underage users are denied access
//!	@see Aurora::Addon::WebUI::DenyMinors()
		protected $DenyMinors;
//!	@see Aurora::Addon::WebUI::DenyMinors
		public function DenyMinors(){
			return $this->DenyMinors;
		}

//!	boolean TRUE to enable landmarks within the estate, FALSE otherwise
//!	@see Aurora::Addon::WebUI::AllowLandmark()
		protected $AllowLandmark;
//!	@see Aurora::Addon::WebUI::AllowLandmark
		public function AllowLandmark(){
			return $this->AllowLandmark;
		}

//!	boolean TRUE if changes can be made to parcels, FALSE otherwise
//!	@see Aurora::Addon::WebUI::AllowParcelChanges()
		protected $AllowParcelChanges;
//!	@see Aurora::Addon::WebUI::AllowParcelChanges
		public function AllowParcelChanges(){
			return $this->AllowParcelChanges;
		}

//!	boolean TRUE if a user can set their home location within the estate, FALSE otherwise.
//!	@see Aurora::Addon::WebUI::AllowSetHome()
		protected $AllowSetHome;
//!	@see Aurora::Addon::WebUI::AllowSetHome
		public function AllowSetHome(){
			return $this->AllowSetHome;
		}

//!	array Array of banned user UUIDs
//!	@see Aurora::Addon::WebUI::EstateBans()
		protected $EstateBans;
//!	@see Aurora::Addon::WebUI::EstateBans
		public function EstateBans(){
			return $this->EstateBans;
		}

//!	array Array of estate manager user UUIDs
//!	@see Aurora::Addon::WebUI::EstateManagers()
		protected $EstateManagers;
//!	@see Aurora::Addon::WebUI::EstateManagers
		public function EstateManagers(){
			return $this->EstateManagers;
		}

//!	array Array of UUIDs for groups that have explicit access to the estate
//!	@see Aurora::Addon::WebUI::EstateGroups()
		protected $EstateGroups;
//!	@see Aurora::Addon::WebUI::EstateGroups
		public function EstateGroups(){
			return $this->EstateGroups;
		}

//!	array Array of UUIDs for users that have explicit access to the estate
//!	@see Aurora::Addon::WebUI::EstateAccess()
		protected $EstateAccess;
//!	@see Aurora::Addon::WebUI::EstateAccess
		public function EstateAccess(){
			return $this->EstateAccess;
		}

//!	constructor is protected as we hide it behind a registry method
/**
*	@param integer $EstateID Estate ID
*	@param string $EstateName Name of estate
*	@param boolean $AbuseEmailToEstateOwner TRUE if abuse reports should be emailed to the estate owner, FALSE otherwise
*	@param boolean $DenyAnonymous TRUE if anonymous users should be denid access, FALSE otherwise
*	@param boolean $ResetHomeOnTeleport TRUE if teleporting out of the estate resets the home location, FALSE otherwise
*	@param boolean $FixedSun TRUE if the sun should be fixed within the estate, FALSE otherwise
*	@param boolean $DenyTransacted TRUE if non-transacted users should be denied?
*	@param boolean $BlockDwell TRUE to block dwell calculations ?
*	@param boolean $DenyIdentified TRUE if unverified users should be denied ?
*	@param boolean $AllowVoice TRUE to allow voice within the estate, FALSE otherwise
*	@param boolean $UseGlobalTime TRUE to use global time, FALSE otherwise
*	@param integer $PricePerMeter grid currency price per meter
*	@param boolean $TaxFree TRUE if land within estate is tax-free, FALSE otherwise
*	@param boolean $AllowDirectTeleport TRUE to enable direct teleport within the estate, FALSE otherwise
*	@param mixed $RedirectGridX NULL or redirect grid position x-axis integer
*	@param mixed $RedirectGridY NULL or redirect grid position y-axis integer
*	@param integer $ParentEstateID Parent Estate ID
*	@param float $SunPosition Sun Position
*	@param boolean $EstateSkipScripts ??
*	@param float $BillableFactor ??
*	@param boolean $PublicAccess TRUE if access to the land within the estate is implicit, FALSE otherwise
*	@param string $AbuseEmail abuse report email address
*	@param string $EstateOwner Estate owner UUID
*	@param boolean $DenyMinors TRUE if underage users are denied access
*	@param boolean $AllowLandmark TRUE to enable landmarks within the estate, FALSE otherwise
*	@param boolean $AllowParcelChanges TRUE if changes can be made to parcels, FALSE otherwise
*	@param boolean $AllowSetHome TRUE if a user can set their home location within the estate, FALSE otherwise.
*	@param array $EstateBans Array of banned user UUIDs
*	@param array $EstateManagers Array of estate manager user UUIDs
*	@param array $EstateGroups Array of UUIDs for groups that have explicit access to the estate
*	@param array $EstateAccess Array of UUIDs for users that have explicit access to the estate
*/
		protected function __construct($EstateID, $EstateName, $AbuseEmailToEstateOwner, $DenyAnonymous, $ResetHomeOnTeleport, $FixedSun, $DenyTransacted, $BlockDwell, $DenyIdentified, $AllowVoice, $UseGlobalTime, $PricePerMeter, $TaxFree, $AllowDirectTeleport, $RedirectGridX, $RedirectGridY, $ParentEstateID, $SunPosition, $EstateSkipScripts, $BillableFactor, $PublicAccess, $AbuseEmail, $EstateOwner, $DenyMinors, $AllowLandmark, $AllowParcelChanges, $AllowSetHome, array $EstateBans, array $EstateManagers, array $EstateGroups, array $EstateAccess){
			if(is_string($EstateID) === true && ctype_digit($EstateID) === true){
				$EstateID = (integer)$EstateID;
			}
			if(is_string($EstateName) === true){
				$EstateName = trim($EstateName);
			}
			if(is_string($AbuseEmail) === true){
				$AbuseEmail = trim($AbuseEmail);
			}
			if(is_string($PricePerMeter) === true && ctype_digit($PricePerMeter) === true){
				$PricePerMeter = (integer)$PricePerMeter;
			}
			if(is_string($RedirectGridX) === true && ctype_digit($RedirectGridX) === true){
				$RedirectGridX = (integer)$RedirectGridX;
			}
			if(is_string($RedirectGridY) === true && ctype_digit($RedirectGridY) === true){
				$RedirectGridY = (integer)$RedirectGridY;
			}
			if(is_string($ParentEstateID) === true && ctype_digit($ParentEstateID) === true){
				$ParentEstateID = (integer)$ParentEstateID;
			}
			if(is_string($SunPosition) === true && (ctype_digit($SunPosition) === true || preg_match('/^\d*\.\d+$/', $SunPosition) == 1)){
				$SunPosition = (float)$SunPosition;
			}
			if(is_string($BillableFactor) === true && (ctype_digit($BillableFactor) === true || preg_match('/^\d*\.\d+$/', $BillableFactor) == 1)){
				$BillableFactor = (float)$BillableFactor;
			}

			if(is_integer($EstateID) === false){
				throw new InvalidArgumentException('Estate ID must be specified as integer.');
			}else if(is_string($EstateName) === false){
				throw new InvalidArgumentException('Estate Name must be specified as string.');
			}else if($EstateName === ''){
				throw new InvalidArgumentException('Estate Name must be non-empty string.');
			}else if(is_bool($AbuseEmailToEstateOwner) === false){
				throw new InvalidArgumentException('AbuseEmailToEstateOwner flag must be specified as boolean.');
			}else if(is_bool($DenyAnonymous) === false){
				throw new InvalidArgumentException('DenyAnonymous flag must be specified as boolean.');
			}else if(is_bool($ResetHomeOnTeleport) === false){
				throw new InvalidArgumentException('ResetHomeOnTeleport flag must be specified as boolean.');
			}else if(is_bool($FixedSun) === false){
				throw new InvalidArgumentException('FixedSun flag must be specified as boolean.');
			}else if(is_bool($DenyTransacted) === false){
				throw new InvalidArgumentException('DenyTransacted flag must be specified as boolean.');
			}else if(is_bool($BlockDwell) === false){
				throw new InvalidArgumentException('BlockDwell flag must be specified as boolean.');
			}else if(is_bool($DenyIdentified) === false){
				throw new InvalidArgumentException('DenyIdentified flag must be specified as boolean.');
			}else if(is_bool($AllowVoice) === false){
				throw new InvalidArgumentException('AllowVoice flag must be specified as boolean.');
			}else if(is_bool($UseGlobalTime) === false){
				throw new InvalidArgumentException('UseGlobalTime flag must be specified as boolean.');
			}else if(is_integer($PricePerMeter) === false){
				throw new InvalidArgumentException('Price-per-meter must be specified as integer.');
			}else if(is_bool($TaxFree) === false){
				throw new InvalidArgumentException('TaxFree flag must be specified as boolean.');
			}else if(is_bool($AllowDirectTeleport) === false){
				throw new InvalidArgumentException('AllowDirectTeleport flag must be specified as boolean.');
			}else if(is_null($RedirectGridX) === false && is_integer($RedirectGridX) === false){
				throw new InvalidArgumentException('X-axis redirection co-ordinate must be specified as null or integer.');
			}else if(is_null($RedirectGridY) === false && is_integer($RedirectGridY) === false){
				throw new InvalidArgumentException('Y-axis redirection co-ordinate must be specified as null or integer.');
			}else if(is_integer($ParentEstateID) === false){
				throw new InvalidArgumentException('Parent Estate ID must be specified as integer.');
			}else if(is_float($SunPosition) === false){
				throw new InvalidArgumentException('Sun Position must be specified as float.');
			}else if(is_bool($EstateSkipScripts) === false){
				throw new InvalidArgumentException('EstateSkipScripts flag must be specified as boolean.');
			}else if(is_float($BillableFactor) === false){
				throw new InvalidArgumentException('Billable Factor must be specified as float.');
			}else if(is_bool($PublicAccess) === false){
				throw new InvalidArgumentException('PublicAccess flag must be specified as boolean.');
			}else if(is_string($AbuseEmail) === false){
				throw new InvalidArgumentException('Abuse Report email address must be specified as string.');
			}else if($AbuseEmail !== '' && is_email($AbuseEmail) === false){
				throw new InvalidArgumentException('Abuse report email address must be valid if not an empty string.');
			}else if(is_string($EstateOwner) === false){
				throw new InvalidArgumentException('Estate Owner UUID must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $EstateOwner) != 1){
				throw new InvalidArgumentException('Estate Owner UUID must be valid UUID.');
			}else if(is_bool($DenyMinors) === false){
				throw new InvalidArgumentException('DenyMinors flag must be specified as boolean.');
			}else if(is_bool($AllowLandmark) === false){
				throw new InvalidArgumentException('AllowLandmark flag must be specified as boolean.');
			}else if(is_bool($AllowParcelChanges) === false){
				throw new InvalidArgumentException('AllowParcelChanges flag must be specified as boolean.');
			}else if(is_bool($AllowSetHome) === false){
				throw new InvalidArgumentException('AllowSetHome flag must be specified as boolean.');
			}
			foreach($EstateBans as $k=>$UUID){
				if(is_string($UUID) === false){
					throw new InvalidArgumentException('Estate Ban UUID must be specified as string.');
				}else if(preg_match(WebUI::regex_UUID, $UUID) != 1){
					throw new InvalidArgumentException('Estate Ban UUID must be valid UUID.');
				}
				$EstateBans[$k] = strtolower($UUID);
			}
			foreach($EstateManagers as $k=>$UUID){
				if(is_string($UUID) === false){
					throw new InvalidArgumentException('Estate Manager UUID must be specified as string.');
				}else if(preg_match(WebUI::regex_UUID, $UUID) != 1){
					throw new InvalidArgumentException('Estate Manager UUID must be valid UUID.');
				}
				$EstateManagers[$k] = strtolower($UUID);
			}
			foreach($EstateGroups as $k=>$UUID){
				if(is_string($UUID) === false){
					throw new InvalidArgumentException('Estate Group UUID must be specified as string.');
				}else if(preg_match(WebUI::regex_UUID, $UUID) != 1){
					throw new InvalidArgumentException('Estate Group UUID must be valid UUID.');
				}
				$EstateGroups[$k] = strtolower($UUID);
			}
			foreach($EstateAccess as $k=>$UUID){
				if(is_string($UUID) === false){
					throw new InvalidArgumentException('Estate Access UUID must be specified as string.');
				}else if(preg_match(WebUI::regex_UUID, $UUID) != 1){
					throw new InvalidArgumentException('Estate Access UUID must be valid UUID.');
				}
				$EstateAccess[$k] = strtolower($UUID);
			}

			$this->EstateID                = $EstateID;
			$this->EstateName              = $EstateName;
			$this->AbuseEmailToEstateOwner = $AbuseEmailToEstateOwner;
			$this->DenyAnonymous           = $DenyAnonymous;
			$this->ResetHomeOnTeleport     = $ResetHomeOnTeleport;
			$this->FixedSun                = $FixedSun;
			$this->DenyTransacted          = $DenyTransacted;
			$this->BlockDwell              = $BlockDwell;
			$this->DenyIdentified          = $DenyIdentified;
			$this->AllowVoice              = $AllowVoice;
			$this->UseGlobalTime           = $UseGlobalTime;
			$this->PricePerMeter           = $PricePerMeter;
			$this->TaxFree                 = $TaxFree;
			$this->AllowDirectTeleport     = $AllowDirectTeleport;
			$this->RedirectGridX           = $RedirectGridX;
			$this->RedirectGridY           = $RedirectGridY;
			$this->ParentEstateID          = $ParentEstateID;
			$this->SunPosition             = $SunPosition;
			$this->EstateSkipScripts       = $EstateSkipScripts;
			$this->BillableFactor          = $BillableFactor;
			$this->PublicAccess            = $PublicAccess;
			$this->AbuseEmail              = $AbuseEmail;
			$this->EstateOwner             = strtolower($EstateOwner);
			$this->DenyMinors              = $DenyMinors;
			$this->AllowLandmark           = $AllowLandmark;
			$this->AllowParcelChanges      = $AllowParcelChanges;
			$this->AllowSetHome            = $AllowSetHome;
			$this->EstateBans              = $EstateBans;
			$this->EstateManagers          = $EstateManagers;
			$this->EstateGroups            = $EstateGroups;
			$this->EstateAccess            = $EstateAccess;
		}

//!	registry method
/**
*	@param integer $EstateID Estate ID
*	@param string $EstateName Name of estate
*	@param boolean $AbuseEmailToEstateOwner TRUE if abuse reports should be emailed to the estate owner, FALSE otherwise
*	@param boolean $DenyAnonymous TRUE if anonymous users should be denid access, FALSE otherwise
*	@param boolean $ResetHomeOnTeleport TRUE if teleporting out of the estate resets the home location, FALSE otherwise
*	@param boolean $FixedSun TRUE if the sun should be fixed within the estate, FALSE otherwise
*	@param boolean $DenyTransacted TRUE if non-transacted users should be denied?
*	@param boolean $BlockDwell TRUE to block dwell calculations ?
*	@param boolean $DenyIdentified TRUE if unverified users should be denied ?
*	@param boolean $AllowVoice TRUE to allow voice within the estate, FALSE otherwise
*	@param boolean $UseGlobalTime TRUE to use global time, FALSE otherwise
*	@param integer $PricePerMeter grid currency price per meter
*	@param boolean $TaxFree TRUE if land within estate is tax-free, FALSE otherwise
*	@param boolean $AllowDirectTeleport TRUE to enable direct teleport within the estate, FALSE otherwise
*	@param mixed $RedirectGridX NULL or redirect grid position x-axis integer
*	@param mixed $RedirectGridY NULL or redirect grid position y-axis integer
*	@param integer $ParentEstateID Parent Estate ID
*	@param float $SunPosition Sun Position
*	@param boolean $EstateSkipScripts ??
*	@param float $BillableFactor ??
*	@param boolean $PublicAccess TRUE if access to the land within the estate is implicit, FALSE otherwise
*	@param string $AbuseEmail abuse report email address
*	@param string $EstateOwner Estate owner UUID
*	@param boolean $DenyMinors TRUE if underage users are denied access
*	@param boolean $AllowLandmark TRUE to enable landmarks within the estate, FALSE otherwise
*	@param boolean $AllowParcelChanges TRUE if changes can be made to parcels, FALSE otherwise
*	@param boolean $AllowSetHome TRUE if a user can set their home location within the estate, FALSE otherwise.
*	@param array $EstateBans Array of banned user UUIDs
*	@param array $EstateManagers Array of estate manager user UUIDs
*	@param array $EstateGroups Array of UUIDs for groups that have explicit access to the estate
*	@param array $EstateAccess Array of UUIDs for users that have explicit access to the estate
*	@return object instance of Aurora::Addon::WebUI::EstateSettings
*/
		public static function r($EstateID, $EstateName=null, $AbuseEmailToEstateOwner=null, $DenyAnonymous=null, $ResetHomeOnTeleport=null, $FixedSun=null, $DenyTransacted=null, $BlockDwell=null, $DenyIdentified=null, $AllowVoice=null, $UseGlobalTime=null, $PricePerMeter=null, $TaxFree=null, $AllowDirectTeleport=null, $RedirectGridX=null, $RedirectGridY=null, $ParentEstateID=null, $SunPosition=null, $EstateSkipScripts=null, $BillableFactor=null, $PublicAccess=null, $AbuseEmail=null, $EstateOwner=null, $DenyMinors=null, $AllowLandmark=null, $AllowParcelChanges=null, $AllowSetHome=null, array $EstateBans=null, array $EstateManagers=null, array $EstateGroups=null, array $EstateAccess=null){
			if(is_string($EstateID) === true && ctype_digit($EstateID) === true){
				$EstateID = (integer)$EstateID;
			}
			if(is_integer($EstateID) === false){
				throw new InvalidArgumentException('Estate ID must be specified as integer.');
			}

			static $registry = array();

			$create = isset($registry[$EstateID]) === false;

			if($create === true && isset($EstateName, $AbuseEmailToEstateOwner, $DenyAnonymous, $ResetHomeOnTeleport, $FixedSun, $DenyTransacted, $BlockDwell, $DenyIdentified, $AllowVoice, $UseGlobalTime, $PricePerMeter, $TaxFree, $AllowDirectTeleport, $RedirectGridX, $RedirectGridY, $ParentEstateID, $SunPosition, $EstateSkipScripts, $BillableFactor, $PublicAccess, $AbuseEmail, $EstateOwner, $DenyMinors, $AllowLandmark, $AllowParcelChanges, $AllowSetHome, $EstateBans, $EstateManagers, $EstateGroups, $EstateAccess) === false){
				throw new InvalidArgumentException('Cannot return cached EstateSettings object, none has been created.');
			}else if($create === false){
				$ES = $registry[$EstateID];
				$create = (
					$ES->EstateName()                    !== $EstateName                    ||
					$ES->AbuseEmailToEstateOwner()       !== $AbuseEmailToEstateOwner       ||
					$ES->DenyAnonymous()                 !== $DenyAnonymous                 ||
					$ES->ResetHomeOnTeleport()           !== $ResetHomeOnTeleport           ||
					$ES->FixedSun()                      !== $FixedSun                      ||
					$ES->DenyTransacted()                !== $DenyTransacted                ||
					$ES->BlockDwell()                    !== $BlockDwell                    ||
					$ES->DenyIdentified()                !== $DenyIdentified                ||
					$ES->AllowVoice()                    !== $AllowVoice                    ||
					$ES->UseGlobalTime()                 !== $UseGlobalTime                 ||
					$ES->PricePerMeter()                 !== $PricePerMeter                 ||
					$ES->TaxFree()                       !== $TaxFree                       ||
					$ES->AllowDirectTeleport()           !== $AllowDirectTeleport           ||
					$ES->RedirectGridX()                 !== $RedirectGridX                 ||
					$ES->RedirectGridY()                 !== $RedirectGridY                 ||
					$ES->ParentEstateID()                !== $ParentEstateID                ||
					$ES->SunPosition()                   !== $SunPosition                   ||
					$ES->EstateSkipScripts()             !== $EstateSkipScripts             ||
					$ES->BillableFactor()                !== $BillableFactor                ||
					$ES->PublicAccess()                  !== $PublicAccess                  ||
					$ES->AbuseEmail()                    !== $AbuseEmail                    ||
					$ES->EstateOwner()                   !== $EstateOwner                   ||
					$ES->DenyMinors()                    !== $DenyMinors                    ||
					$ES->AllowLandmark()                 !== $AllowLandmark                 ||
					$ES->AllowParcelChanges()            !== $AllowParcelChanges            ||
					$ES->AllowSetHome()                  !== $AllowSetHome                  ||
					print_r($ES->EstateBans(), true)     !== print_r($EstateBans, true)     ||
					print_r($ES->EstateManagers(), true) !== print_r($EstateManagers, true) ||
					print_r($ES->EstateGroups(), true)   !== print_r($EstateGroups, true)   ||
					print_r($ES->EstateAccess(), true)   !== print_r($EstateAccess, true)
				);
			}

			if($create === true){
				$registry[$EstateID] = new static($EstateID, $EstateName, $AbuseEmailToEstateOwner, $DenyAnonymous, $ResetHomeOnTeleport, $FixedSun, $DenyTransacted, $BlockDwell, $DenyIdentified, $AllowVoice, $UseGlobalTime, $PricePerMeter, $TaxFree, $AllowDirectTeleport, $RedirectGridX, $RedirectGridY, $ParentEstateID, $SunPosition, $EstateSkipScripts, $BillableFactor, $PublicAccess, $AbuseEmail, $EstateOwner, $DenyMinors, $AllowLandmark, $AllowParcelChanges, $AllowSetHome, $EstateBans, $EstateManagers, $EstateGroups, $EstateAccess);
			}

			return $registry[$EstateID];
		}
	}

//!	Iterator for EstateSettings
	class EstateSettingsIterator extends WORM{

//!	We just want a strongly-typed array here.
		public function __construct(array $Estates){
			foreach($Estates as $Estate){
				if(($Estate instanceof EstateSettings) === false){
					throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::EstateSettings can be appended to instances of Aurora::Addon::WebUI::EstateSettingsIterator');
				}
			}
			$this->data=$Estates;
		}


		public function offsetSet($offset, $value){
			throw new BadMethodCallException('Instances of Aurora::Addon::WebUI::EstateSettingsIterator cannot be modified from outside of the object scope.');
		}
	}
}
?>
