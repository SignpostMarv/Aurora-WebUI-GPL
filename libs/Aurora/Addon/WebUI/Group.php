<?php
//!	@file libs/Aurora/Addon/WebUI/Group.php
//!	@brief Group-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

	use SeekableIterator;

	use Aurora\Addon\WebUI;
	use Aurora\Framework;

//!	Implementation of Aurora::Framework::GroupRecord
	class GroupRecord implements Framework\GroupRecord{

//!	We hide this behind a registry method.
/**
*	@param string $uuid group UUID
*	@param string $name group name
*	@param string $charter group charter
*	@param string $insignia asset ID for group insignia
*	@param string $founder user UUID for group founder
*	@param integer $membershipFee fee required to join the group
*	@param bool $openEnrollment TRUE if anyone can join, FALSE if they need to be invited.
*	@param bool $showInList TRUE if shown in search, FALSE otherwise
*	@param bool $allowPublish not too sure what this does, as I thought it was what $showInList was for.
*	@param bool $maturePublish TRUE if group is mature-rated, FALSE otherwise.
*	@param string $ownerRoleID UUID for owner role.
*/
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

//!	We use a registry method since groups are uniquely identified by UUIDs
/**
*	@param string $uuid group UUID
*	@param mixed $name group name
*	@param mixed $charter group charter
*	@param mixed $insignia asset ID for group insignia
*	@param mixed $founder user UUID for group founder
*	@param mixed $membershipFee fee required to join the group
*	@param mixed $openEnrollment TRUE if anyone can join, FALSE if they need to be invited.
*	@param mixed $showInList TRUE if shown in search, FALSE otherwise
*	@param mixed $allowPublish not too sure what this does, as I thought it was what $showInList was for.
*	@param mixed $maturePublish TRUE if group is mature-rated, FALSE otherwise.
*	@param mixed $ownerRoleID UUID for owner role.
*	@return object Aurora::Addon::WebUI::GroupRecord
*/
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
					$group->MaturePublish()  != $maturePublish ||
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
	class GetGroupRecords extends WebUI\abstractSeekableFilterableIterator{

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instance of Aurora::Addon::WebUI We need to specify this in case we want to iterate past the original set of results.
*	@param integer $start initial cursor position
*	@param integer $total Total number of results possible with specified filters
*	@param array $sort optional array of field names for keys and booleans for values, indicating ASC and DESC sort orders for the specified fields.
*	@param array $boolFields optional array of field names for keys and booleans for values, indicating 1 and 0 for field values.
*	@param array $groups if specified, should be an array of instances of Aurora::Addon::WebUI::GroupRecord that were pre-fetched with a call to the API end-point.
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, array $sort=null, array $boolFields=null, array $groups=null){
			parent::__construct($WebUI, $start, $total, $sort, $boolFields);
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

//!	To avoid slowdowns due to an excessive amount of curl calls, we populate Aurora::Addon::WebUI::GetGroupRecords::$data in batches of 10
/**
*	@return mixed either NULL or an instance of Aurora::Addon::WebUI::GroupRecord
*/
		public function current(){
			if($this->valid() === false){
				return null;
			}else if(isset($this->data[$this->key()]) === false){
				$start   = $this->key();
				$results = $this->WebUI->GetGroups($start, 10, $this->sort, $this->boolFields);
				foreach($results as $group){
					$this->data[$start++] = $group;
				}
			}
			return $this->data[$this->key()];
		}
	}

//!	Implementation of Aurora::Framework::GroupNoticeData
	class GroupNoticeData implements Framework\GroupNoticeData{

/**
*	@param string $GroupID GroupID that the notice belongs to.
*	@param string $NoticeID unique identifier for the notice.
*	@param integer $timestamp unix timestamp indicating when the group notice was created.
*	@param string $FromName Name of user that created the group notice.
*	@param string $Subject Subject of group notice.
*	@param string $Message Group notice message (this is actually from GroupNoticeInfo, but we're being lazy)
*	@param boolean $HasAttachment TRUE of the group notice has an attachment, FALSE otherwise.
*	@param string $ItemID attachment ID
*	@param integer $AssetType asset type
*	@param string $ItemName name of attachment
*/
		public function __construct($GroupID, $NoticeID, $timestamp, $FromName, $Subject, $Message, $HasAttachment=false, $ItemID='00000000-0000-0000-0000-000000000000', $AssetType=-1, $ItemName=''){
			if(is_string($timestamp) === true && ctype_digit($timestamp) === true){
				$timestamp = (integer)$timestamp;
			}
			if(is_string($FromName) === true){
				$FromName = trim($FromName);
			}
			if(is_string($Subject) === true){
				$Subject = trim($Subject);
			}
			if(is_string($Message) === true){
				$Message = trim($Message);
			}
			if(is_string($AssetType) === true && ctype_digit($AssetType) === true){
				$AssetType = (integer)$AssetType;
			}
			if(is_string($ItemName) === true){
				$ItemName = trim($ItemName);
			}

			if(is_string($GroupID) === false){
				throw new InvalidArgumentException('GroupID must be a string.');
			}else if(preg_match(WebUI::regex_UUID, $GroupID) !== 1){
				throw new InvalidArgumentException('GroupID must be a valid UUID.');
			}else if(is_string($NoticeID) === false){
				throw new InvalidArgumentException('NoticeID must be a string.');
			}else if(preg_match(WebUI::regex_UUID, $NoticeID) !== 1){
				throw new InvalidArgumentException('NoticeID must be a valid UUID.');
			}else if(is_integer($timestamp) === false){
				throw new InvalidArgumentException('Timestamp must be an integer.');
			}else if(is_string($FromName) === false){
				throw new InvalidArgumentException('FromName must be a string.');
			}else if($FromName === ''){
				throw new InvalidArgumentException('FromName cannot be an empty string.');
			}else if(is_string($Subject) === false){
				throw new InvalidArgumentException('Subject must be a string.');
			}else if($Subject === ''){
				throw new InvalidArgumentException('Subject cannot be an empty string.');
			}else if(is_string($Message) === false){
				throw new InvalidArgumentException('Message must be a string.');
			}else if(is_bool($HasAttachment) === false){
				throw new InvalidArgumentException('HasAttachment flag must be a boolean.');
			}else if(is_string($ItemID) === false){
				throw new InvalidArgumentException('ItemID must be a string.');
			}else if(preg_match(WebUI::regex_UUID, $ItemID) !== 1){
				throw new InvalidArgumentException('ItemID must be a valid UUID.');
			}else if(is_integer($AssetType) === false){
				throw new InvalidArgumentException('AssetType must be an integer.');
			}else if(is_string($ItemName) === false){
				throw new InvalidArgumentException('ItemName must be a string.');
			}else if($HasAttachment === true && $ItemName === ''){
				throw new InvalidArgumentException('ItemName cannot be an empty string when a group notice has an attachment.');
			}

			$this->GroupID       = $GroupID;
			$this->NoticeID      = $NoticeID;
			$this->Timestamp     = $timestamp;
			$this->FromName      = $FromName;
			$this->Subject       = $Subject;
			$this->Message       = $Message;
			$this->HasAttachment = $HasAttachment;
			$this->AssetType     = $AssetType;
			$this->ItemID        = $ItemID;
			$this->ItemName      = $ItemName;
		}

//!	registry method
/**
*	@param string $GroupID GroupID that the notice belongs to.
*	@param string $NoticeID unique identifier for the notice.
*	@param integer $timestamp unix timestamp indicating when the group notice was created.
*	@param string $FromName Name of user that created the group notice.
*	@param string $Subject Subject of group notice.
*	@param boolean $HasAttachment TRUE of the group notice has an attachment, FALSE otherwise.
*	@param string $ItemID attachment ID
*	@param integer $AssetType asset type
*	@param string $ItemName name of attachment
*	@return object instance of Aurora::Framework::GroupNoticeData
*/
		public static function r($GroupID, $NoticeID, $timestamp=null, $FromName=null, $Subject=null, $Message=null, $HasAttachment=false, $ItemID='00000000-0000-0000-0000-000000000000', $AssetType=-1, $ItemName=''){
			if(is_string($GroupID) === false){
				throw new InvalidArgumentException('GroupID must be a string.');
			}else if(preg_match(WebUI::regex_UUID, $GroupID) === false){
				throw new InvalidArgumentException('GroupID must be a valid UUID.');
			}else if(is_string($NoticeID) === false){
				throw new InvalidArgumentException('NoticeID must be a string.');
			}else if(preg_match(WebUI::regex_UUID, $NoticeID) === false){
				throw new InvalidArgumentException('NoticeID must be a valid UUID.');
			}

			$GroupID  = strtolower($GroupID);
			$NoticeID = strtolower($NoticeID);
			static $registry = array();
			if(isset($registry[$GroupID]) === false){
				$registry[$GroupID] = array();
			}
			if(isset($registry[$GroupID][$NoticeID]) === false){
				$registry[$GroupID][$NoticeID] = new static($GroupID, $NoticeID, $timestamp, $FromName, $Subject, $Message, $HasAttachment, $ItemID, $AssetType, $ItemName);
			}

			return $registry[$GroupID][$NoticeID];
		}

//!	string GroupID that the notice belongs to.
//!	@see Aurora::Addon::WebUI::GroupNoticeData::GroupID()
		protected $GroupID;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$GroupID
		public function GroupID(){
			return $this->GroupID;
		}

//!	string NoticeID unique identifier for the notice.
//!	@see Aurora::Addon::WebUI::GroupNoticeData::NoticeID()
		protected $NoticeID;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$NoticeID
		public function NoticeID(){
			return $this->NoticeID;
		}

//!	integer unix timestamp indicating when the group notice was created.
//!	@see Aurora::Addon::WebUI::GroupNoticeData::Timestamp()
		protected $Timestamp;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$Timestamp
		public function Timestamp(){
			return $this->Timestamp;
		}

//!	string Name of user that created the group notice.
//!	@see Aurora::Addon::WebUI::GroupNoticeData::FromName()
		protected $FromName;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$FromName
		public function FromName(){
			return $this->FromName;
		}

//!	string Subject of group notice.
//!	@see Aurora::Addon::WebUI::GroupNoticeData::Subject()
		protected $Subject;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$Subject
		public function Subject(){
			return $this->Subject;
		}

//!	string Message of group notice.
//!	@see Aurora::Addon::WebUI::GroupNoticeData::Message()
		protected $Message;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$Message
		public function Message(){
			return $this->Message;
		}

//!	boolean TRUE of the group notice has an attachment, FALSE otherwise.
//!	@see Aurora::Addon::WebUI::GroupNoticeData::HasAttachment()
		protected $HasAttachment;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$HasAttachment
		public function HasAttachment(){
			return $this->HasAttachment;
		}

//!	integer asset type
//!	@see Aurora::Addon::WebUI::GroupNoticeData::AssetType()
		protected $AssetType;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$AssetType
		public function AssetType(){
			return $this->AssetType;
		}

//!	string attachment ID
//!	@see Aurora::Addon::WebUI::GroupNoticeData::ItemID()
		protected $ItemID;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$ItemID
		public function ItemID(){
			return $this->ItemID;
		}

//!	string name of attachment
//!	@see Aurora::Addon::WebUI::GroupNoticeData::ItemName()
		protected $ItemName;
//!	@see Aurora::Addon::WebUI::GroupNoticeData::$ItemName
		public function ItemName(){
			return $this->ItemName;
		}
	}

//!	Group Notices iterator
	class GetGroupNotices extends abstractSeekableFilterableIterator{

	protected $groups;

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $WebUI instance of Aurora::Addon::WebUI We need to specify this in case we want to iterate past the original set of results.
*	@param integer $start initial cursor position
*	@param integer $total Total number of results possible with specified filters
*	@param array $sort optional array of field names for keys and booleans for values, indicating ASC and DESC sort orders for the specified fields.
*	@param array $boolFields optional array of field names for keys and booleans for values, indicating 1 and 0 for field values.
*	@param array $groups if specified, should be an array of instances of Aurora::Addon::WebUI::GroupNoticeData that were pre-fetched with a call to the API end-point.
*/
		protected function __construct(WebUI $WebUI, $start=0, $total=0, array $groups, array $groupNotices=null){
			foreach($groups as $groupID){
				if(is_string($groupID) === false){
					throw new InvalidArgumentException('GroupID must be a string.');
				}else if(preg_match(WebUI::regex_UUID, $groupID) != 1){
					throw new InvalidArgumentException('GroupID must be a valid UUID.');
				}
			}
			$this->groups = $groups;
			parent::__construct($WebUI, $start, $total, null, null);
			if(isset($groupNotices) === true){
				$i = $start;
				foreach($groupNotices as $group){
					if($group instanceof GroupNoticeData){
						$this->data[$i++] = $group;
					}else{
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::GroupNoticeData should be passed to Aurora::Addon::WebUI::GetGroupRecords::__construct()');
					}
				}
			}
		}

//! This is a registry method for a class that implements the SeekableIterator class, so we can save ourselves some API calls if we've already fetched some groups.
/**
*	@param object $WebUI instance of Aurora::Addon::WebUI We need to specify this in case we want to iterate past the original set of results.
*	@param integer $start initial cursor position
*	@param integer $total Total number of results possible with specified filters
*	@param array $groups array of group IDs that notices should be fetched for
*	@param array $entities if specified, should be an array of entity objects to be validated by the child constructor
*	@param array $ignored this parameter is ignored, only here to comply with the method
*/
		public static function r(WebUI $WebUI, $start=0, $total=0, array $groups=null, array $entities=null, array $ignored=null){
			sort($groups);
			static $registry = array();
			$hash1 = spl_object_hash($WebUI);
			$hash2 = md5(print_r($groups,true));

			if(isset($registry[$hash1]) === false){
				$registry[$hash1] = array();
			}

			$create = (isset($registry[$hash1][$hash2]) === false) || ($create === false && $registry[$hash1][$hash2]->count() !== $total);

			if($create === true){
				$registry[$hash1][$hash2] = new static($WebUI, $start, $total, $groups, $entities);
			}

			$registry[$hash1][$hash2]->seek($start);

			return $registry[$hash1][$hash2];
		}

//!	To avoid slowdowns due to an excessive amount of curl calls, we populate Aurora::Addon::WebUI::GetGroupRecords::$data in batches of 10
/**
*	@return mixed either NULL or an instance of Aurora::Addon::WebUI::GroupRecord
*/
		public function current(){
			if($this->valid() === false){
				return null;
			}else if(isset($this->data[$this->key()]) === false){
				$start   = $this->key();
				$results = $this->WebUI->GroupNotices($start, 10, $this->groups, true);
				foreach($results as $group){
					$this->data[$start++] = $group;
				}
			}
			return $this->data[$this->key()];
		}
	}

//!	News iterator
	class GetNewsFromGroupNotices extends GetGroupNotices{
//!	To avoid slowdowns due to an excessive amount of curl calls, we populate Aurora::Addon::WebUI::GetGroupRecords::$data in batches of 10
/**
*	@return mixed either NULL or an instance of Aurora::Addon::WebUI::GroupRecord
*/
		public function current(){
			if($this->valid() === false){
				return null;
			}else if(isset($this->data[$this->key()]) === false){
				$start   = $this->key();
				$results = $this->WebUI->NewsFromGroupNotices($start, 10);
				foreach($results as $group){
					$this->data[$start++] = $group;
				}
			}
			return $this->data[$this->key()];
		}
	}
}
?>