<?php
//!	@file libs/Aurora/Addon/WebUI/AbuseReports.php
//!	@brief Abuse Report-related WebUI code
//!	@author SignpostMarv

namespace Aurora\Addon\WebUI{

//!	AbuseReport class. Included in result returned by Aurora::Addon::WebUI::GetAbuseReports()
	class AbuseReport{

//!	protected constructor, we hide it behind a registry method.
/**
*	@param integer $number looks like the primary key according to IAbuseReports.GetAbuseReport
*	@param string $details abuse report details
*	@param string $location abuse report location
*	@param string $username name of the account being reported
*	@param string $summary summary of abuse report
*	@param boolean $active whether or not the abuse report is open.
*	@param string $assignedTo name of admin the abuse report is assigned to.
*	@param string $category category of abuse
*	@param boolean $checked not entirely sure what this does.
*	@param string $notes notes on the abuse report.
*	@param string $objectName Name of object being reported (if applicable)
*	@param string $objectPosition Position of object being reported (if applicable)
*	@param string $objectUUID UUID of object being reported (if applicable)
*	@param string $regionName region the report originated from
*	@param string $reporterName account name of the user who filed the abuse report
*	@parama string $screenshot asset UUID of screenshot attached to the abuse report
*/
		protected function __construct($number, $details, $location, $userName, $summary, $active, $assignedTo, $category, $checked, $notes, $objectName, $objectPosition, $objectUUID, $regionName, $reporterName, $screenshot){
			if(is_integer($number) === false){
				throw new InvalidArgumentException('AR Number must be an integer.');
			}else if(is_string($details) === false){
				throw new InvalidArgumentException('AR Details must be a string.');
			}else if(is_string($location) === false){
				throw new InvalidArgumentException('AR Location must be a string.');
			}else if(is_string($username) === false){
				throw new InvalidArgumentException('AR accused name must be a string.');
			}else if(is_string($summary) === false){
				throw new InvalidArgumentException('AR summary must be a string.');
			}else if(is_bool($active) === false){
				throw new InvalidArgumentException('AR activity flag must be a boolean.');
			}else if(is_string($assignedTo) === false){
				throw new InvalidArgumentException('AR admin assignee name must be a string.');
			}else if(is_string($category) === false){
				throw new InvalidArgumentException('AR category must be a string.');
			}else if(is_bool($checked) === false){
				throw new InvalidArgumentException('AR checked flag must be a boolean.');
			}else if(is_string($notes) === false){
				throw new InvalidArgumentException('AR notes must be a string.');
			}else if(is_string($objectName) === false){
				throw new InvalidArgumentException('AR object name must be a string.');
			}else if(is_string($objectPosition) === false){
				throw new InvalidArgumentException('AR object position must be a string.');
			}else if(is_string($objectUUID) === false){
				throw new InvalidArgumentException('AR object UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $objectUUID) !== 1){
				throw new InvalidArgumentException('AR object UUID must be a valid UUID.');
			}else if(is_string($regionName) === false){
				throw new InvalidArgumentException('AR region of origin must be a string.');
			}else if(is_string($reporterName) === false){
				throw new InvalidArgumentException('AR reporter name must be as string.');
			}else if(is_string($screenshot) === false){
				throw new InvalidArgumentException('AR screenshot UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $screenshot) === false){
				throw new InvalidArgumentException('AR screenshot UUID must be a valid UUID.');
			}

			$this->Number         = $number;
			$this->Details        = trim($details);
			$this->Location       = trim($location);
			$this->UserName       = trim($userName);
			$this->Summary        = trim($summary);
			$this->Active         = $active;
			$this->AssignedTo     = trim($assignedTo);
			$this->Category       = trim($category);
			$this->Checked        = $checked;
			$this->Notes          = trim($notes);
			$this->ObjectName     = trim($objectName);
			$this->ObjectPosition = trim($objectPosition);
			$this->ObjectUUID     = $objectUUID;
			$this->RegionName     = trim($regionName);
			$this->ReporterName   = trim($reporterName);
			$this->Screenshot     = $screenshot;
		}

//!	registry method
/**
*	@param integer $number looks like the primary key according to IAbuseReports.GetAbuseReport
*	@param string $details abuse report details
*	@param string $location abuse report location
*	@param string $username name of the account being reported
*	@param string $summary summary of abuse report
*	@param boolean $active whether or not the abuse report is open.
*	@param string $assignedTo name of admin the abuse report is assigned to.
*	@param string $category category of abuse
*	@param boolean $checked not entirely sure what this does.
*	@param string $notes notes on the abuse report.
*	@param string $objectName Name of object being reported (if applicable)
*	@param string $objectPosition Position of object being reported (if applicable)
*	@param string $objectUUID UUID of object being reported (if applicable)
*	@param string $regionName region the report originated from
*	@param string $reporterName account name of the user who filed the abuse report
*	@parama string $screenshot asset UUID of screenshot attached to the abuse report
*/
		public static function r($number, $details=null, $location=null, $userName=null, $summary=null, $active=null, $assignedTo=null, $category=null, $checked=null, $notes=null, $objectName=null, $objectPosition=null, $objectUUID=null, $regionName=null, $reporterName=null, $screenshot=null){
			if(is_integer($number) === false){
				throw new InvalidArgumentException('AR number must be an integer.');
			}

			static $registry = array();
			$create = (isset($registry[$number]) === false);

			if($create === true && isset($details, $location, $userName, $summary, $active, $assignedTo, $category, $checked, $notes, $objectName, $objectPosition, $objectUUID, $regionName, $reporterName, $screenshot) === false){
				throw new InvalidArgumentException('Cannot return cached AbuseReport object as it hasn\'t been created.');
			}else if($create === false){
				$details        = trim($details);
				$location       = trim($location);
				$userName       = trim($userName);
				$summary        = trim($summary);
				$assignedTo     = trim($assignedTo);
				$category       = trim($category);
				$notes          = trim($notes);
				$objectName     = trim($objectName);
				$objectPosition = trim($objectPosition);
				$regionName     = trim($regionName);
				$reporterName   = trim($reporterName);

				$AR = $registry[$number];
				$create = (
					$AR->Details()        !== $details        ||
					$AR->Location()       !== $location       ||
					$AR->UserName()       !== $userName       ||
					$AR->Summary()        !== $summary        ||
					$AR->Active()         !== $active         ||
					$AR->AssignedTo()     !== $assignedTo     ||
					$AR->Category()       !== $category       ||
					$AR->Checked()        !== $checked        ||
					$AR->Notes()          !== $notes          ||
					$AR->ObjectName()     !== $objectName     ||
					$AR->ObjectPosition() !== $objectPosition ||
					$AR->ObjectUUID()     !== $objectUUID     ||
					$AR->RegionName()     !== $regionName     ||
					$AR->ReporterName()   !== $reporterName   ||
					$AR->Screenshot()     !== $screenshot
				);
			}

			if($create === true){
				$registry[$number] = new static($number, $details, $location, $userName, $summary, $active, $assignedTo, $category, $checked, $notes, $objectName, $objectPosition, $objectUUID, $regionName, $reporterName, $screenshot);
			}

			return $registry[$number];
		}

//!	integer looks like the primary key according to IAbuseReports.GetAbuseReport
//!	@see Aurora::Addon::WebUI::AbuseReport::Number()
		protected $Number;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Number
		public function Number(){
			return $this->Number;
		}

//!	string abuse report details
//!	@see Aurora::Addon::WebUI::AbuseReport::Details()
		protected $Details;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Details
		public function Details(){
			return $this->Details;
		}

//!	string abuse report location
//!	@see Aurora::Addon::WebUI::AbuseReport::Location()
		protected $Location;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Location
		public function Location(){
			return $this->Location;
		}

//!	string name of the account being reported
//!	@see Aurora::Addon::WebUI::AbuseReport::UserName()
		protected $UserName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$UserName
		public function UserName(){
			return $this->UserName;
		}

//!	string summary of abuse report
//!	@see Aurora::Addon::WebUI::AbuseReport::Summary()
		protected $Summary;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Summary
		public function Summary(){
			return $this->Summary;
		}

//!	boolean whether or not the abuse report is open.
//!	@see Aurora::Addon::WebUI::AbuseReport::Active()
		protected $Active;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Active
		public function Active(){
			return $this->Active;
		}

//!	string name of admin the abuse report is assigned to.
//!	@see Aurora::Addon::WebUI::AbuseReport::AssignedTo()
		protected $AssignedTo;
//!	@see Aurora::Addon::WebUI::AbuseReport::$AssignedTo
		public function AssignedTo(){
			return $this->AssignedTo;
		}

//!	string category of abuse
//!	@see Aurora::Addon::WebUI::AbuseReport::Category()
		protected $Category;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Category
		public function Category(){
			return $this->Category;
		}

//!	boolean not entirely sure what this does.
//!	@see Aurora::Addon::WebUI::AbuseReport::Checked()
		protected $Checked;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Checked
		public function Checked(){
			return $this->Checked;
		}

//!	string notes on the abuse report.
//!	@see Aurora::Addon::WebUI::AbuseReport::Notes()
		protected $Notes;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Notes
		public function Notes(){
			return $this->Notes;
		}

//!	string Name of object being reported (if applicable)
//!	@see Aurora::Addon::WebUI::AbuseReport::ObjectName()
		protected $ObjectName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ObjectName
		public function ObjectName(){
			return $this->ObjectName;
		}

//!	string Position of object being reported (if applicable)
//!	@see Aurora::Addon::WebUI::AbuseReport::ObjectPosition()
		protected $ObjectPosition;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ObjectPosition
		public function ObjectPosition(){
			return $this->ObjectPosition;
		}

//!	string UUID of object being reported (if applicable)
//!	@see Aurora::Addon::WebUI::AbuseReport::ObjectUUID()
		protected $ObjectUUID;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ObjectUUID
		public function ObjectUUID(){
			return $this->ObjectUUID;
		}

//!	string region the report originated from
//!	@see Aurora::Addon::WebUI::AbuseReport::RegionName()
		protected $RegionName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$RegionName
		public function RegionName(){
			return $this->RegionName;
		}

//!	string account name of the user who filed the abuse report
//!	@see Aurora::Addon::WebUI::AbuseReport::ReporterName()
		protected $ReporterName;
//!	@see Aurora::Addon::WebUI::AbuseReport::$ReporterName
		public function ReporterName(){
			return $this->ReporterName;
		}

//!	string asset UUID of screenshot attached to the abuse report
//!	@see Aurora::Addon::WebUI::AbuseReport::Screenshot()
		protected $Screenshot;
//!	@see Aurora::Addon::WebUI::AbuseReport::$Screenshot
		public function Screenshot(){
			return $this->Screenshot;
		}
	}

//!	AbuseReports iterator. Returned by Aurora::Addon::WebUI::GetAbuseReports()
	class AbuseReports extends abstractIterator{

//!	public constructor
		public function __construct(array $ARs=null){
			if(isset($ARs) === true){
				foreach($ARs as $AR){
					if(($v instanceof AbuseReport) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::AbuseReport should be included in the array passed to Aurora::Addon::WebUI::AbuseReports::__construct()');
					}
				}
				reset($ARs);
				$this->data = $ARs;
			}
		}
	}
}
?>