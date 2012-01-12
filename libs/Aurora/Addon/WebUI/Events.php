<?php
//!	@file libs/Aurora/Addon/WebUI/Events.php
//!	@brief Event-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

	use DateTime;

	use OpenMetaverse\Vector3;

	use Aurora\Framework;
	use Aurora\Addon\WebUI;

//!	Implementation of Aurora::Framework::EventData
	class EventData implements Framework\EventData{

//!	integer Event ID
//!	@see Aurora::Addon::WebUI::EventData::eventID()
		protected $eventID;
//!	@see Aurora::Addon::WebUI::EventData::eventID
		public function eventID(){
			return $this->eventID;
		}

//!	string Creator UUID
//!	@see Aurora::Addon::WebUI::EventData::creator()
		protected $creator;
//!	@see Aurora::Addon::WebUI::EventData::creator
		public function creator(){
			return $this->creator;
		}

//!	string Event Subject
//!	@see Aurora::Addon::WebUI::EventData::name()
		protected $name;
//!	@see Aurora::Addon::WebUI::EventData::name
		public function name(){
			return $this->name;
		}

//!	string Event Category
//!	@see Aurora::Addon::WebUI::EventData::category()
		protected $category;
//!	@see Aurora::Addon::WebUI::EventData::category
		public function category(){
			return $this->category;
		}

//!	string Event description
//!	@see Aurora::Addon::WebUI::EventData::description()
		protected $description;
//!	@see Aurora::Addon::WebUI::EventData::description
		public function description(){
			return $this->description;
		}

//!	object instance of DateTime indicating when event started
//!	@see Aurora::Addon::WebUI::EventData::date()
		protected $date;
//!	@see Aurora::Addon::WebUI::EventData::date
		public function date(){
			return $this->date;
		}

//!	integer number of minutes the events lasts
//!	@see Aurora::Addon::WebUI::EventData::duration()
		protected $duration;
//!	@see Aurora::Addon::WebUI::EventData::duration
		public function duration(){
			return $this->duration;
		}

//!	integer cover charge
//!	@see Aurora::Addon::WebUI::EventData::cover()
		protected $cover;
//!	@see Aurora::Addon::WebUI::EventData::cover
		public function cover(){
			return $this->cover;
		}

//!	string Name of the region that the event is held in.
//!	@see Aurora::Addon::WebUI::EventData::simName()
		protected $simName;
//!	@see Aurora::Addon::WebUI::EventData::simName
		public function simName(){
			return $this->simName;
		}

//!	object instance of OpenMetaverse::Vector3 indicating the grid coordinates for the event
//!	@see Aurora::Addon::WebUI::EventData::globalPos()
		protected $globalPos;
//!	@see Aurora::Addon::WebUI::EventData::globalPos
		public function globalPos(){
			return $this->globalPos;
		}

//!	integer Event Flags bitfield
//!	@see Aurora::Addon::WebUI::EventData::eventFlags()
		protected $eventFlags;
//!	@see Aurora::Addon::WebUI::EventData::eventFlags
		public function eventFlags(){
			return $this->eventFlags;
		}

//!	integer Content Rating of event
//!	@see Aurora::Addon::WebUI::EventData::maturity()
		protected $maturity;
//!	@see Aurora::Addon::WebUI::EventData::maturity
		public function maturity(){
			return $this->maturity;
		}

//!	We hide this behind a registry method
/**
*	@param integer $eventID Event ID
*	@param string $creator Creator UUID
*	@param string $name Event Subject
*	@param string $description Event description
*	@param string $category Event Category
*	@param object $date instance of DateTime indicating when event started
*	@param integer $duration number of minutes the events lasts
*	@param integer $cover cover charge
*	@param string $simName Name of the region that the event is held in.
*	@param object $globalPos instance of OpenMetaverse::Vector3 indicating the grid coordinates for the event
*	@param integer $eventFlags Event Flags bitfield
*	@param integer $maturity Content Rating of event
*/
		protected function __construct($eventID, $creator, $name, $description, $category, DateTime $date, $duration, $cover, $simName, Vector3 $globalPos, $eventFlags, $maturity){
			if(is_string($eventID) === true && ctype_digit($eventID) === true){
				$eventID = (integer)$eventID;
			}
			if(is_string($name) === true){
				$name = trim($name);
			}
			if(is_string($description) === true){
				$description = trim($description);
			}
			if(is_string($category) === true){
				$category = trim($category);
			}
			if(is_string($duration) === true && ctype_digit($duration) === true){
				$duration = (integer)$duration;
			}
			if(is_string($cover) === true && ctype_digit($cover) === true){
				$cover = (integer)$cover;
			}
			if(is_string($simName) === true){
				$simName = trim($simName);
			}
			if(is_string($eventFlags) === true && ctype_digit($eventFlags) === true){
				$eventFlags = (integer)$eventFlags;
			}
			if(is_string($maturity) === true && ctype_digit($maturity) === true){
				$maturity = (integer)$maturity;
			}

			if(is_integer($eventID) === false){
				throw new InvalidArgumentException('Event ID must be specified as integer.');
			}else if($eventID <= 0){
				throw new InvalidArgumentException('Event ID must be greater than zero.');
			}else if(is_string($creator) === false){
				throw new InvalidArgumentException('Creator must be specified as string.');
			}else if(preg_match(WebUI::regex_UUID, $creator) != 1){
				throw new InvalidArgumentException('Creator must be valid UUID.');
			}else if(is_string($name) === false){
				throw new InvalidArgumentException('Event name must be specified as string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Event name must be non-empty string.');
			}else if(is_string($description) === false){
				throw new InvalidArgumentException('Event description be specified as string.');
			}else if($description === ''){
				throw new InvalidArgumentException('Event description must be non-empty string.');
			}else if(is_string($category) === false){
				throw new InvalidArgumentException('Event category must be specified as string.');
			}else if($category === ''){
				throw new InvalidArgumentException('Event category must be non-empty string.');
			}else if(is_integer($cover) === false){
				throw new InvalidArgumentException('Event cover charge must be specified as integer.');
			}else if($cover < 0){
				throw new InvalidArgumentException('Event cover charge must be greater than or equal to zero.');
			}else if(is_string($simName) === false){
				throw new InvalidArgumentException('Event simName must be specified as string.');
			}else if($simName === ''){
				throw new InvalidArgumentException('Event simName must be non-empty string.');
			}else if(is_integer($eventFlags) === false){
				throw new InvalidArgumentException('Event flags must be specified as integer.');
			}else if($eventFlags < 0){
				throw new InvalidArgumentException('Event flags must be greater than or equal to zero.');
			}else if(is_integer($maturity) === false){
				throw new InvalidArgumentException('Event maturity must be specified as integer.');
			}else if($maturity < 0){
				throw new InvalidArgumentException('Event maturity must be greater than or equal to zero.');
			}

			$this->eventID     = $eventID;
			$this->creator     = strtolower($creator);
			$this->name        = $name;
			$this->description = $description;
			$this->category    = $category;
			$this->date        = $date;
			$this->duration    = $duration;
			$this->cover       = $cover;
			$this->simName     = $simName;
			$this->globalPos   = $globalPos;
			$this->eventFlags  = $eventFlags;
			$this->maturity    = $maturity;
		}

//!	Registry method!
		public static function r($eventID, $creator=null, $name=null, $description=null, $category=null, DateTime $date=null, $duration=null, $cover=null, $simName=null, Vector3 $globalPos=null, $eventFlags=null, $maturity=null){
			if(is_string($eventID) === true && ctype_digit($eventID) === true){
				$eventID = (integer)$eventID;
			}else if(is_integer($eventID) === false){
				throw new InvalidArgumentException('Event ID must be specified as integer.');
			}else if($eventID <= 0){
				throw new InvalidArgumentException('Event ID must be greater than zero.');
			}

			$creator = strtolower($creator);

			static $registry = array();

			$create = isset($registry[$eventID]) === false;

			if($create === true && isset($creator, $name, $description, $category, $date, $duration, $cover, $simName, $globalPos, $eventFlags, $maturity) === false){
				throw new InvalidArgumentException('Cannot return cached event object, none has been created.');
			}else if($create === false){
				$Event = $registry[$eventID];
				$create = (
					$Event->creator()                       !== $creator     ||
					$Event->name()                          !== $name        ||
					$Event->description()                   !== $description ||
					$Event->category()                      !== $category    ||
					$Event->date()->diff($date)->s          !== 0            ||
					$Event->duration()                      !== $duration    ||
					$Event->cover()                         !== $cover       ||
					$Event->simName()                       !== $simName     ||
					$Event->globalPos()->equals($globalPos) !== true         ||
					$Event->eventFlags()                    !== $eventFlags  ||
					$Event->maturity()                      !== $maturity
				);
			}

			if($create === true){
				$registry[$eventID] = new static($eventID, $creator, $name, $description, $category, $date, $duration, $cover, $simName, $globalPos, $eventFlags, $maturity);
			}

			return $registry[$eventID];
		}
	}

//!	Abstract iterator for instances of Aurora::Addon::WebUI::EventData
	abstract class abstractSeekableEventDataIterator extends abstractSeekableIterator{

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of EventData results according to child-class filters
*	@param array $parcels array of Aurora::Addon::WebUI::EventData objects
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, array $parcels=null){
			parent::__construct($WebUI, $start, $total);
			if(isset($parcels) === true){
				$i = $start;
				foreach($parcels as $parcel){
					if($parcel instanceof EventData){
						$this->data[$i++] = $parcel;
					}else{
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::EventData should be passed to Aurora::Addon::WebUI::abstractSeekableEventDataIterator::__construct()');
					}
				}
			}
		}
	}


	class GetEvents extends abstractSeekableEventDataIterator{

//!	array filter argument
		protected $filter;

//!	array sort argument
		protected $sort;

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instanceof Aurora::Addon::WebUI
*	@param integer $start start point
*	@param integer $total total number of EventData results according to child-class filters
*	@param array $filter filter argument used with the API
*	@param array $sort sort argument used with the API
*	@param array $parcels array of Aurora::Addon::WebUI::EventData objects
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, array $filter=null, array $sort=null, array $parcels=null){
			$this->filter = $filter;
			$this->sort   = $sort;
			parent::__construct($WebUI, $start, $total, $parcels);
		}


		public static function r(WebUI $WebUI, $start=0, $total=0, array $filter=null, array $sort=null, array $parcels=null){
		
			static $registry = array();
		
			$hash1 = spl_object_hash($WebUI);
			$hash2 = md5(print_r($filter, true));
			$hash3 = md5(print_r($sort, true));

			if(isset($registry[$hash1]) === false){
				$registry[$hash1] = array();
			}
			if(isset($registry[$hash1][$hash2]) === false){
				$registry[$hash1][$hash2] = array();
			}

			$create = (
				isset($registry[$hash1][$hash2][$hash3]) === false ||
				$registry[$hash1][$hash2][$hash3]->count() !== $total
			);

			if($create === true){
				$registry[$hash1][$hash2][$hash3] = new static($WebUI, $start, $total, $filter, $sort, $parcels);
			}

			$registry[$hash1][$hash2][$hash3]->seek($start);
			return $registry[$hash1][$hash2][$hash3];
		}

//!	To avoid slowdowns due to an excessive amount of curl calls, we populate Aurora::Addon::WebUI::GetEvents::$data in batches of 10
/**
*	@return mixed either NULL or an instance of Aurora::Addon::WebUI::LandData
*/
		public function current(){
			if($this->valid() === false){
				return null;
			}else if(isset($this->data[$this->key()]) === false){
				$start   = $this->key();
				$results = $this->WebUI->GetEvents($start, 10, $this->filter, $this->sort, true);
				foreach($results as $event){
					$this->data[$start++] = $event;
				}
			}
			return $this->data[$this->key()];
		}
	}
}
?>