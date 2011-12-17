<?php
//!	@file libs/Aurora/Addon/WebUI/Group.php
//!	@brief Group-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

	use SeekableIterator;

	use Aurora\Addon\WebUI;
	use Aurora\Framework;


	class GroupRecord implements Framework\GroupRecord{


		protected function __construct($uuid, $name, $charter, $insignia, $founder, $membershipFee, $openEnrollment, $showInList, $allowPublish, $maturePublish, $ownerRoleID){
			if(is_string($name) === true){
				$name = trim($name);
			}
			if(is_string($charter) === true){
				$charter = trim($charter);
			}
			if(is_string($membershipFee) === true && ctype_digit($membershipFee) === true){
				$membershipFee = (integer)$membershipFee;
			}
			if(is_string($openEnrollment) === true && ctype_digit($openEnrollment) === true){
				$openEnrollment = (bool)$openEnrollment;
			}
			if(is_string($showInList) === true && ctype_digit($showInList) === true){
				$showInList = (bool)$showInList;
			}
			if(is_string($allowPublish) === true && ctype_digit($allowPublish) === true){
				$allowPublish = (bool)$allowPublish;
			}
			if(is_string($maturePublish) === true && ctype_digit($maturePublish) === true){
				$maturePublish = (bool)$maturePublish;
			}

			if(is_string($uuid) === false){
				throw new InvalidArgumentException('Group ID should be a string.');
			}else if(preg_match(WebUI::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('Group ID should be a valid UUID.');
			}else if(is_string($name) === false){
				throw new InvalidArgumentException('Group name should be a string.');
			}else if($name === ''){
				throw new InvalidArgumentException('Group name should be a non-empty string.');
			}else if(is_string($charter) === false){
				throw new InvalidArgumentException('Group charter should be a string.');
			}else if(is_string($insignia) === false){
				throw new InvalidArgumentException('Group insignia should be a string.');
			}else if(preg_match(WebUI::regex_UUID, $insignia) !== 1){
				throw new InvalidArgumentException('Group insignia should be a valid UUID.');
			}else if(is_string($founder) === false){
				throw new InvalidArgumentException('Group founder should be a string.');
			}else if(preg_match(WebUI::regex_UUID, $founder) !== 1){
				throw new InvalidArgumentException('Group founder should be a valid UUID.');
			}else if(is_integer($membershipFee) === false){
				throw new InvalidArgumentException('Membership fee should be an integer.');
			}else if($membershipFee < 0){
				throw new InvalidArgumentException('Membership fee should be greater than or equal to zero.');
			}else if(is_bool($openEnrollment) === false){
				throw new InvalidArgumentException('Open Enrollment should be a boolean.');
			}else if(is_bool($showInList) === false){
				throw new InvalidArgumentException('Show in list flag should be a boolean.');
			}else if(is_bool($allowPublish) === false){
				throw new InvalidArgumentException('Allow publish flag should be a boolean.');
			}else if(is_bool($maturePublish) === false){
				throw new InvalidArgumentException('Mature publish flag should be a boolean.');
			}else if(is_string($ownerRoleID) === false){
				throw new InvalidArgumentException('Owner role ID should be a string.');
			}else if(preg_match(WebUI::regex_UUID, $ownerRoleID) !== 1){
				throw new InvalidArgumentException('Owner role ID should be a valid UUID.');
			}

			$this->GroupID        = $uuid;
			$this->GroupName      = $name;
			$this->Charter        = $charter;
			$this->GroupPicture   = $insignia;
			$this->FounderID      = $founder;
			$this->MembershipFee  = $membershipFee;
			$this->OpenEnrollment = $openEnrollment;
			$this->ShowInList     = $showInList;
			$this->AllowPublish   = $allowPublish;
			$this->MaturePublish  = $maturePublish;
			$this->OwnerRoleID    = $ownerRoleID;
		}


		public static function r($uuid, $name=null, $charter=null, $insignia=null, $founder=null, $membershipFee=null, $openEnrollment=null, $showInList=null, $allowPublish=null, $maturePublish=null, $ownerRoleID=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('Group ID should be a string.');
			}else if(preg_match(WebUI::regex_UUID, $uuid) !== 1){
				throw new InvalidArgumentException('Group ID should be a valid UUID.');
			}
			$uuid = strtolower($uuid);

			static $registry = array();

			$create = (isset($registry[$uuid]) === false);

			if($create === true && isset($name, $charter, $insignia, $founder, $membershipFee, $openEnrollment, $showInList, $allowPublish, $maturePublish, $ownerRoleID) === false){
				throw new InvalidArgumentException('Cannot return a group instance, group was never set.');
			}else if($create === false){
				$group = $registry[$uuid];

				$create = (
					$group->GroupName()      != $name ||
					$group->Charter()        != $charter ||
					$group->GroupPicture()   != $insignia ||
					$group->FounderID()      != $founder ||
					$group->MembershipFee()  != $membershipFee ||
					$group->OpenEnrollment() != $openEnrollment ||
					$group->ShowInList()     != $showInList ||
					$group->AllowPublish()   != $allowPublish ||
					$group->MaturePublish()  != $MaturePublish ||
					$group->OwnerRoleID()    != $ownerRoleID
				);
			}

			if($create === true){
				$registry[$uuid] = new static($uuid, $name, $charter, $insignia, $founder, $membershipFee, $openEnrollment, $showInList, $allowPublish, $maturePublish, $ownerRoleID);
			}

			return $registry[$uuid];
		}


		protected $GroupID;
		public function GroupID(){
			return $this->GroupID;
		}


		protected $GroupName;
		public function GroupName(){
			return $this->GroupName;
		}


		protected $Charter;
		public function Charter(){
			return $this->Charter;
		}


		protected $GroupPicture;
		public function GroupPicture(){
			return $this->GroupPicture;
		}


		protected $FounderID;
		public function FounderID(){
			return $this->FounderID;
		}


		protected $MembershipFee;
		public function MembershipFee(){
			return $this->MembershipFee;
		}


		protected $OpenEnrollment;
		public function OpenEnrollment(){
			return $this->OpenEnrollment;
		}


		protected $ShowInList;
		public function ShowInList(){
			return $this->ShowInList;
		}


		protected $AllowPublish;
		public function AllowPublish(){
			return $this->AllowPublish;
		}


		protected $MaturePublish;
		public function MaturePublish(){
			return $this->MaturePublish;
		}


		protected $OwnerRoleID;
		public function OwnerRoleID(){
			return $this->OwnerRoleID;
		}
	}

//!	Groups iterator
	class GetGroupRecords extends WebUI\abstractSeekableIterator{


		private $sort;


		private $boolFields;

		protected function __construct(WebUI $WebUI, $start=0, $total=0, array $sort=null, array $boolFields=null, array $groups=null){
			parent::__construct($WebUI, $start, $total);
			$this->sort = $sort;
			$this->boolFields = $boolFields;
			if(isset($groups) === true){
				$i = $start;
				foreach($groups as $group){
					if($group instanceof GroupRecord){
						$this->data[$i++] = $group;
					}else{
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::GroupRecord should be passed to Aurora::Addon::WebUI::GetGroupRecords::__construct()');
					}
				}
			}
		}


		public static function r(WebUI $WebUI, $start=0, $total=0, array $sort=null, array $boolFields=null, array $groups=null){
			static $registry = array();
			$hash1 = spl_object_hash($WebUI);
			$hash2 = md5(print_r($sort,true));
			$hash3 = md5(print_r($boolFields,true));

			if(isset($registry[$hash1]) === false){
				$registry[$hash1] = array();
			}
			if(isset($registry[$hash1][$hash2]) === false){
				$registry[$hash1][$hash2] = array();
			}

			$create = (isset($registry[$hash1][$hash2][$hash3]) === false) || ($create === false && $registry[$hash1][$hash2][$hash3]->count() !== $total);

			if($create === true){
				$registry[$hash1][$hash2][$hash3] = new static($WebUI, $start, $total, $sort, $boolFields, $groups);
			}

			$registry[$hash1][$hash2][$hash3]->seek($start);

			return $registry[$hash1][$hash2][$hash3];
		}
	}
}
?>