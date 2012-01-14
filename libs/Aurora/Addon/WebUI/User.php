<?php
//!	@file libs/Aurora/Addon/WebUI/User.php
//!	@brief User-related WebUI code
//!	@author SignpostMarv

namespace Aurora\Addon\WebUI{

	use IteratorAggregate;

	use Aurora\Services\Interfaces;
	use Aurora\Addon\abstractIterator;

//!	abstract implementation
	abstract class abstractUser implements Interfaces\User{

//!	protected constructor, should be hidden behind factory or registry methods. Assumes properties were already set.
		protected function __construct(){
			if(is_string($this->PrincipalID) === false){
				throw new InvalidArgumentException('User UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $this->PrincipalID) === false){
				throw new InvalidArgumentException('User UUID was not a valid UUID.');
			}else if(is_string($this->FirstName) === false){
				throw new InvalidArgumentException('User first name must be a string.');
			}else if(trim($this->FirstName) === ''){
				throw new InvalidArgumentException('User first name must not be an empty string.');
			}else if(is_string($this->LastName) === false){ // last names can be an empty string
				throw new InvalidArgumentException('User last name must be a string.');
			}
			$this->PrincipalID = strtolower($this->PrincipalID);
		}

//!	string UUID
//!	@see Aurora::Addon::WebUI::abstractUser::PrincipalID()
		protected $PrincipalID;
//!	@see Aurora::Addon::WebUI::abstractUser::$PrincipalID
		public function PrincipalID(){
			return $this->PrincipalID;
		}

//!	string First Name
//!	@see Aurora::Addon::WebUI::abstractUser::FirstName()
		protected $FirstName;
//!	@see Aurora::Addon::WebUI::abstractUser::$FirstName
		public function FirstName(){
			return $this->FirstName;
		}

//!	string Last Name
//!	@see Aurora::Addon::WebUI::abstractUser::LastName()
		protected $LastName;
//!	@see Aurora::Addon::WebUI::abstractUser::$LastName
		public function LastName(){
			return $this->LastName;
		}

//!	This is for child classes that won't have their own implementation
/**
*	@return string
*	@see Aurora::Addon::WebUI::abstractUser::FirstName()
*	@see Aurora::Addon::WebUI::abstractUser::LastName()
*/
		public function Name(){
			return trim($this->FirstName() . ' ' . $this->LastName()); // we use trim in case Aurora::Addon::WebUI::abstractUser::LastName() is an empty string.
		}
	}

	abstract class abstractUserHasName extends abstractUser{
		protected function __construct($uuid, $name){
			$firstName = explode(' ', $name);
			$lastName = array_pop($firstName);
			if($lastName === $name){
				$lastName = '';
				$firstName = $name;
			}else{
				$firstName = implode(' ', $firstName); // this is to future proof first names with multiple spaces.
			}

			$this->PrincipalID = $uuid;
			$this->FirstName   = $firstName;
			$this->LastName    = $lastName;

			parent::__construct();
		}
	}

//!	Lightweight generated user.
	class genUser extends abstractUser{
//!	Since this is a generated class, we need to override the parent constructor to set the properties, then call back to it.
/**
*	@param string $uuid Corresponds to Aurora::Services::Interfaces::User::PrincipalID()
*	@param string $first Corresponds to Aurora::Services::Interfaces::User::FirstName()
*	@param string $last Corresponds to Aurora::Services::Interfaces::User::LastName()
*/
		protected function __construct($uuid, $first, $last){
			$this->PrincipalID = $uuid;
			$this->FirstName   = $first;
			$this->LastName    = $last;
		}

//!	Since this is a generated class for non-unique entities, we're going to use a registry method.
/**
*	We're going to validate the UUID here, but leave the first/last name up to the class constructor, since we use the UUID as the registry key.
*	@param string $uuid Corresponds to Aurora::Services::Interfaces::User::PrincipalID()
*	@param string $first Corresponds to Aurora::Services::Interfaces::User::FirstName()
*	@param string $last Corresponds to Aurora::Services::Interfaces::User::LastName()
*	@return object instance of Aurora::Addon::WebUI::genUser
*/
		public static function r($uuid, $first=null, $last=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('User UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('User UUID was not a valid UUID.');
			}
			$uuid = strtolower($uuid);
			static $registry = array();
			if(isset($registry[$uuid]) === false){
				if(isset($first,$last) === false){
					throw new InvalidArgumentException('Cannot return an instance by UUID alone, user was never set.');
				}
				$registry[$uuid] = new static($uuid, $first, $last);
			}else if($registry[$uuid]->FirstName() !== $first || $registry[$uuid]->LastName() !== $last){ // assume a call was made to Aurora::Addon::WebUI::ChangeName()
				$registry[$uuid] = new static($uuid, $first, $last);
			}
			return $registry[$uuid];
		}
	}

	abstract class commonUser extends genUser{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid UUID for the user
*	@param string $name the user's name.
*	@param string $email either a valid email address or an empty string.
*/
		protected function __construct($uuid, $name, $email){
			if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if(trim($name) === ''){
				throw new InvalidArgumentException('Name cannot be an empty string.');
			}else if(is_string($email) === false){
				throw new InvalidArgumentException('Email address must be string.');
			}else if($email !== '' && is_email($email) === false){
				throw new InvalidArgumentException('Email address not valid.');
			}

			$firstName = explode(' ', $name);
			$lastName = array_pop($firstName);
			if($lastName === $name){
				$lastName = '';
				$firstName = $name;
			}else{
				$firstName = implode(' ', $firstName); // this is to future proof first names with multiple spaces.
			}

			$this->Email    = $email;
			$this->Name     = $name;

			parent::__construct($uuid, $firstName, $lastName);
		}

//!	string user name
//!	@see Aurora::Addon::WebUI::GridUserInfo::Name()
		protected $Name;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$Name
		public function Name(){
			return $this->Name;
		}

//!	string user email
//!	@see Aurora::Addon::WebUI::GridUserInfo::Email()
		protected $Email;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$Email
		public function Email(){
			return $this->Email;
		}
	}

//!	GridUserInfo class. Returned by Aurora::Addon::WebUI::GetGridUserInfo()
	class GridUserInfo extends commonUser{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid UUID for the user
*	@param string $name the user's name.
*	@param string $homeUUID the UUID of the user's home region.
*	@param string $homeName the name of the user's home region.
*	@param string $onlineStatus TRUE if the user is currently online, FALSE otherwise.
*	@param string $email either a valid email address or an empty string.
*/
		protected function __construct($uuid, $name, $homeUUID, $homeName, $onlineStatus, $email){
			if(is_string($homeUUID) === false){
				throw new InvalidArgumentException('Home region UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $homeUUID) !== 1){
				throw new InvalidArgumentException('Home region UUID was not a valid UUID.');
			}else if(is_string($homeName) === false){
				throw new InvalidArgumentException('Home region name must be a string.');
			}else if($homeUUID !== '00000000-0000-0000-0000-000000000000' && trim($homeName) === ''){
				throw new InvalidArgumentException('Home region name cannot be an empty string.');
			}else if(is_bool($onlineStatus) === false){
				throw new InvalidArgumentException('Online status must be a boolean.');
			}

			$this->HomeUUID = $homeUUID;
			$this->HomeName = $homeName;
			$this->Online   = $onlineStatus;

			parent::__construct($uuid, $name, $email);
		}

//!	Since this is a generated class for non-unique entities, we're going to use a registry method.
/**
*	@param string $uuid UUID for the user
*	@param string $name the user's name.
*	@param string $homeUUID the UUID of the user's home region.
*	@param string $homeName the name of the user's home region.
*	@param string $onlineStatus TRUE if the user is currently online, FALSE otherwise.
*	@param string $email either a valid email address or an empty string.
*	@return object instance of Aurora::Addon::WebUI::GridUserInfo
*/
		public static function r($uuid, $name=null, $homeUUID=null, $homeName=null, $onlineStatus=null, $email=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if((isset($name) || isset($homeUUID) || isset($homeName) || isset($onlineStatus) || isset($email)) && isset($name, $homeUUID, $homeName, $onlineStatus, $email) === false){
				throw new InvalidArgumentException('If the grid info of the user has changed, all info must be specified.');
			}
			$uuid = strtolower($uuid);
			static $registry = array();
			if(isset($registry[$uuid]) === false){
				if(isset($name, $homeUUID, $homeName, $onlineStatus, $email) === false){
					throw new InvalidArgumentException('Cannot return grid info for user by UUID, grid info has not been set.');
				}
				$registry[$uuid] = new static($uuid, $name, $homeUUID, $homeName, $onlineStatus, $email);
			}else if(isset($name, $homeUUID, $homeName, $onlineStatus, $email) === true){
				$info = $registry[$uuid];
				if(
					$info->Name()         !== $name         ||
					$info->HomeUUID()     !== $homeUUID     ||
					$info->HomeName()     !== $homeName     ||
					$info->Online() !== $onlineStatus ||
					$info->Email()        !== $email
				){
					$registry[$uuid] = new static($uuid, $name, $homeUUID, $homeName, $onlineStatus, $email);
				}
			}
			return $registry[$uuid];
		}

//!	string user home region UUID
//!	@see Aurora::Addon::WebUI::GridUserInfo::HomeUUID()
		protected $HomeUUID;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$HomeUUID
		public function HomeUUID(){
			return $this->HomeUUID;
		}

//!	string user home region name
//!	@see Aurora::Addon::WebUI::GridUserInfo::HomeName()
		protected $HomeName;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$HomeName
		public function HomeName(){
			return $this->HomeName;
		}

//!	boolean TRUE if Online, FALSE otherwise
//!	@see Aurora::Addon::WebUI::GridUserInfo::Online()
		protected $Online;
//!	@see Aurora::Addon::WebUI::GridUserInfo::$Online
		public function Online(){
			return $this->Online;
		}
	}

//!	UserProfile class. Returned by Aurora::Addon::WebUI::GetProfile()
	class UserProfile extends commonUser{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid Account UUID
*	@param string $name Account name
*	@param string $email Account email address
*	@param integer $created unix timestamp of when the account was created
*	@param boolean $allowPublish TRUE if the profile shows in search, FALSE otherwise
*	@param boolean $maturePublish TRUE if the profile does not show in general search, FALSE otherwise
*	@param integer $wantToMask mask of wants
*	@param string $wantToText description of wants
*	@param integer $canDoMask mask of things the user can do
*	@param string $canDoText description of things the user can do
*	@param string $languages Languages the user understands
*	@param string $image Asset UUID of profile image
*	@param string $aboutText Descriptive "about" text of a user.
*	@param string $firstLifeImage Asset UUID of "first life" profile image
*	@param string $firstLifeAboutText Descriptive "first life" about text of a user.
*	@param string $webURL User's external (third-party) web page
*	@param string $displayName user's display name that can appear in supported viewers as an alternative to their account name.
*	@param string $partnerUUID UUID of the user's partner account
*	@param boolean $visible TRUE if the user's online status is visible to everyone, FALSE otherwise.
*	@param string $customType custom account type.
*	@param string $notes Stringified JSON data of account notes.
*/
		protected function __construct($uuid, $name='', $email='', $created=0, $allowPublish=false, $maturePublish=false, $wantToMask=0, $wantToText='', $canDoMask=0, $canDoText='', $languages='', $image='00000000-0000-0000-0000-000000000000', $aboutText='', $firstLifeImage='00000000-0000-0000-0000-000000000000', $firstLifeAboutText='', $webURL='', $displayName='', $partnerUUID='00000000-0000-0000-0000-000000000000', $visible=false, $customType='', $notes='', $RLName=null, $RLAddress=null, $RLZip=null, $RLCity=null, $RLCountry=null){
			if(is_string($created) === true && ctype_digit($created) === true){
				$created = (integer)$created;
			}
			if(is_string($wantToMask) === true && ctype_digit($wantToMask) === true){
				$wantToMask = (integer)$wantToMask;
			}
			if(is_string($canDoMask) === true && ctype_digit($canDoMask) === true){
				$canDoMask = (integer)$canDoMask;
			}

			if(is_integer($created) === false){
				throw new InvalidArgumentException('created must be an integer.');
			}else if(is_bool($allowPublish) === false){
				throw new InvalidArgumentException('allowPublish must be a boolean.');
			}else if(is_bool($maturePublish) === false){
				throw new InvalidArgumentException('maturePublish must be a boolean.');
			}else if(is_integer($wantToMask) === false){
				throw new InvalidArgumentException('wantToMask must be an integer.');
			}else if(is_string($wantToText) === false){
				throw new InvalidArgumentException('wantToText must be a string.');
			}else if(is_integer($canDoMask) === false){
				throw new InvalidArgumentException('canDoMask must be an integer.');
			}else if(is_string($canDoText) === false){
				throw new InvalidArgumentException('canDoText must be an integer.');
			}else if(is_string($languages) === false){
				throw new InvalidArgumentException('languages must be a string.');
			}else if(is_string($image) === false){
				throw new InvalidArgumentException('image asset UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $image) !== 1){
				throw new InvalidArgumentException('image asset UUID was not a valid UUID.');
			}else if(is_string($aboutText) === false){
				throw new InvalidArgumentException('about text must be a string.');
			}else if(is_string($firstLifeImage) === false){
				throw new InvalidArgumentException('first life image asset UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $firstLifeImage) !== 1){
				throw new InvalidArgumentException('first life image asset UUID was not a valid UUID.');
			}else if(is_string($firstLifeAboutText) === false){
				throw new InvalidArgumentException('first life about text must be a string.');
			}else if(is_string($webURL) === false){
				throw new InvalidArgumentException('third-party user web profile must be a string.');
			}else if(trim($webURL) !== '' && in_array(parse_url($webURL, \PHP_URL_SCHEME), array('http', 'https')) === false){
				throw new InvalidArgumentException('third-party user web profile must be either http or https url.');
			}else if(is_string($displayName) === false){
				throw new InvalidArgumentException('display name must be a string.');
			}else if(is_string($partnerUUID) === false){
				throw new InvalidArgumentException('partner account UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $partnerUUID) !== 1){
				throw new InvalidArgumentException('partner account UUID was not a valid UUID.');
			}else if(is_bool($visible) === false){
				throw new InvalidArgumentException('visible must be a boolean.');
			}else if(is_string($customType) === false){
				throw new InvalidArgumentException('custom type must be a string.');
			}else if(is_string($notes) === false){
				throw new InvalidArgumentException('notes must be a string.');
			}else if(json_decode($notes) === false){
				throw new InvalidArgumentException('notes must be a valid stringified JSON entity.');
			}
			
			$this->Created            = $created;
			$this->AllowPublish       = $allowPublish;
			$this->MaturePublish      = $maturePublish;
			$this->WantToMask         = $wantToMask;
			$this->WantToText         = trim($wantToText);
			$this->CanDoMask          = $canDoMask;
			$this->CanDoText          = trim($canDoText);
			$this->Languages          = trim($languages);
			$this->Image              = $image;
			$this->AboutText          = trim($aboutText);
			$this->FirstLifeImage     = $firstLifeImage;
			$this->FirstLifeAboutText = trim($firstLifeAboutText);
			$this->WebURL             = trim($webURL);
			$this->DisplayName        = trim($displayName);
			$this->PartnerUUID        = $partnerUUID;
			$this->Visible            = $visible;
			$this->CustomType         = trim($customType);
			$this->Notes              = $notes;

			if(isset($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry) === true){
				$this->RLInfo = new RLInfo($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
			}else if(isset($RLName) || isset($RLAddress) || isset($RLZip) || isset($RLCity) || isset($RLCountry)){
				throw new InvalidArgumentException('If RL information is being specified, all RL information needs to be specified.');
			}

			parent::__construct($uuid, $name, $email);
		}

		public static function r($uuid, $name=null, $email=null, $created=null, $allowPublish=null, $maturePublish=null, $wantToMask=null, $wantToText=null, $canDoMask=null, $canDoText=null, $languages=null, $image=null, $aboutText=null, $firstLifeImage=null, $firstLifeAboutText=null, $webURL=null, $displayName=null, $partnerUUID=null, $visible=null, $customType=null, $notes=null, $RLName=null, $RLAddress=null, $RLZip=null, $RLCity=null, $RLCountry=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}else if((
				(isset($name) || isset($email) || isset($created) || isset($allowPublish) || isset($maturePublish) || isset($wantToMask) || isset($wantToText) || isset($canDoMask) || isset($canDoText) || isset($languages) || isset($image) || isset($aboutText) || isset($firstLifeImage) || isset($firstLifeAboutText) || isset($webURL) || isset($displayName) || isset($partnerUUID) || isset($visible) || isset($customType) || isset($notes)) &&
				isset($name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes) === false) &&
				((isset($RLName) || isset($RLAddress) || isset($RLZip) || isset($RLCity) || isset($RLCountry)) && 
				isset($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry) === false)
			){
				throw new InvalidArgumentException('If the profile info of the user has changed, all info must be specified.');
			}
			if(is_array($wantToMask) === true){
				if(count($wantToMask) > 32){
					throw new InvalidArgumentException('Cannot convert mask array to bitfield if array has greater than 32 entries');
				}
				$val = 0;
				foreach($wantToMask as $k=>$v){
					if($v != 0){
						$val |= 1 << $k;
					}
				}
				$wantToMask = $val;
			}
			if(is_array($canDoMask) === true){
				if(count($canDoMask) > 32){
					throw new InvalidArgumentException('Cannot convert mask array to bitfield if array has greater than 32 entries',1);
				}
				$val = 0;
				foreach($canDoMask as $k=>$v){
					if($v != 0){
						$val |= 1 << $k;
					}
				}
				$canDoMask = $val;
			}

			$uuid = strtolower($uuid);
			static $registry = array();
			if(isset($registry[$uuid]) === false){
				if(isset($name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes) === false){
					throw new InvalidArgumentException('Cannot return profile for user by UUID, profile data has not been set.');
				}
				$registry[$uuid] = new static($uuid, $name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes, $RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
			}else if(isset($uuid, $name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes) === true){
				$info = $registry[$uuid];
				if(
					$info->Name()               !== $name               ||
					$info->Email()              !== $email              ||
					$info->Created()            !== $created            ||
					$info->AllowPublish()       !== $allowPublish       ||
					$info->MaturePublish()      !== $maturePublish      ||
					$info->WantToMask()         !== $wantToMask         ||
					$info->WantToText()         !== $wantToText         ||
					$info->CanDoMask()          !== $canDoMask          ||
					$info->CanDoText()          !== $canDoText          ||
					$info->Languages()          !== $languages          ||
					$info->Image()              !== $image              ||
					$info->AboutText()          !== $aboutText          ||
					$info->FirstLifeImage()     !== $firstLifeImage     ||
					$info->FirstLifeAboutText() !== $firstLifeAboutText ||
					$info->WebURL()             !== $webURL             ||
					$info->DisplayName()        !== $displayName        ||
					$info->PartnerUUID()        !== $partnerUUID        ||
					$info->Visible()            !== $visible            ||
					$info->CustomType()         !== $customType         ||
					$info->Notes()              !== $notes              ||
					(string)$info->RLInfo()     !== trim(implode("\n", array($RLName, $RLAddress, $RLZip, $RLCity, $RLCountry)))
				){
					$registry[$uuid] = new static($uuid, $name, $email, $created, $allowPublish, $maturePublish, $wantToMask, $wantToText, $canDoMask, $canDoText, $languages, $image, $aboutText, $firstLifeImage, $firstLifeAboutText, $webURL, $displayName, $partnerUUID, $visible, $customType, $notes, $RLName, $RLAddress, $RLZip, $RLCity, $RLCountry);
				}
			}
			return $registry[$uuid];
		}

//!	integer unix timestamp of when the account was created.
//!	@see Aurora::Addon::WebUI::UserProfile::Created()
		protected $Created;
//!	@see Aurora::Addon::WebUI::UserProfile::$Created
		public function Created(){
			return $this->Created;
		}

//!	boolean Show in search
//!	@see Aurora::Addon::WebUI::UserProfile::AllowPublish()
		protected $AllowPublish;
//!	@see Aurora::Addon::WebUI::UserProfile::$AllowPublish
		public function AllowPublish(){
			return $this->AllowPublish;
		}

//!	boolean Mature publishing
//!	@see Aurora::Addon::WebUI::UserProfile::MaturePublish()
		protected $MaturePublish;
//!	@see Aurora::Addon::WebUI::UserProfile::$MaturePublish
		public function MaturePublish(){
			return $this->MaturePublish;
		}

//!	boolean Mask of wants
//!	@see Aurora::Addon::WebUI::UserProfile::WantToMask()
		protected $WantToMask;
//!	@see Aurora::Addon::WebUI::UserProfile::$WantToMask
		public function WantToMask(){
			return $this->WantToMask;
		}

//!	string String of wants
//!	@see Aurora::Addon::WebUI::UserProfile::WantToText()
		protected $WantToText;
//!	@see Aurora::Addon::WebUI::UserProfile::$WantToText
		public function WantToText(){
			return $this->WantToText;
		}

//!	boolean Mask of things the user can do
//!	@see Aurora::Addon::WebUI::UserProfile::CanDoMask()
		protected $CanDoMask;
//!	@see Aurora::Addon::WebUI::UserProfile::$CanDoMask
		public function CanDoMask(){
			return $this->CanDoMask;
		}

//!	string String of things the user can do
//!	@see Aurora::Addon::WebUI::UserProfile::CanDoText()
		protected $CanDoText;
//!	@see Aurora::Addon::WebUI::UserProfile::$CanDoText
		public function CanDoText(){
			return $this->CanDoText;
		}

//!	string Languages the person understands.
//!	@see Aurora::Addon::WebUI::UserProfile::Languages()
		protected $Languages;
//!	@see Aurora::Addon::WebUI::UserProfile::$Languages
		public function Languages(){
			return $this->Languages;
		}

//!	string UUID for the asset used in the main section of the user's profile.
//!	@see Aurora::Addon::WebUI::UserProfile::Image()
		protected $Image;
//!	@see Aurora::Addon::WebUI::UserProfile::$Image
		public function Image(){
			return $this->Image;
		}

//!	string Descriptive "about" text of user.
//!	@see Aurora::Addon::WebUI::UserProfile::AboutText()
		protected $AboutText;
//!	@see Aurora::Addon::WebUI::UserProfile::$AboutText
		public function AboutText(){
			return $this->AboutText;
		}

//!	string UUID for the asset used in the "first life" section of the user's profile.
//!	@see Aurora::Addon::WebUI::UserProfile::FirstLifeImage()
		protected $FirstLifeImage;
//!	@see Aurora::Addon::WebUI::UserProfile::$FirstLifeImage
		public function FirstLifeImage(){
			return $this->FirstLifeImage;
		}

//!	string Descriptive "first life" text of user.
//!	@see Aurora::Addon::WebUI::UserProfile::FirstLifeAboutText()
		protected $FirstLifeAboutText;
//!	@see Aurora::Addon::WebUI::UserProfile::$FirstLifeAboutText
		public function FirstLifeAboutText(){
			return $this->FirstLifeAboutText;
		}

//!	string User's external (third-party) web page
//!	@see Aurora::Addon::WebUI::UserProfile::WebURL()
		protected $WebURL;
//!	@see Aurora::Addon::WebUI::UserProfile::$WebURL
		public function WebURL(){
			return $this->WebURL;
		}

//!	string user's display name that can appear in supported viewers as an alternative to their account name.
//!	@see Aurora::Addon::WebUI::UserProfile::DisplayName()
		protected $DisplayName;
//!	@see Aurora::Addon::WebUI::UserProfile::$DisplayName
		public function DisplayName(){
			return $this->DisplayName;
		}

//!	string UUID of the user's partner account
//!	@see Aurora::Addon::WebUI::UserProfile::PartnerUUID()
		protected $PartnerUUID;
//!	@see Aurora::Addon::WebUI::UserProfile::$PartnerUUID
		public function PartnerUUID(){
			return $this->PartnerUUID;
		}

//!	boolean Show online status
//!	@see Aurora::Addon::WebUI::UserProfile::Visible()
		protected $Visible;
//!	@see Aurora::Addon::WebUI::UserProfile::$Visible
		public function Visible(){
			return $this->Visible;
		}

//!	string custom type
//!	@see Aurora::Addon::WebUI::UserProfile::CustomType()
		protected $CustomType;
//!	@see Aurora::Addon::WebUI::UserProfile::$CustomType
		public function CustomType(){
			return $this->CustomType;
		}

//!	string profile notes.
//!	@see Aurora::Addon::WebUI::UserProfile::Notes()
		protected $Notes;
//!	@see Aurora::Addon::WebUI::UserProfile::$Notes
		public function Notes(){
			return $this->Notes;
		}

//!	mixed Either an instance of Aurora::Addon::WebUI::RLInfo or NULL.
//!	@see Aurora::Addon::WebUI::UserProfile::RLInfo()
		protected $RLInfo = null;
//!	@see Aurora::Addon::WebUI::UserProfile::$RLInfo
		public function RLInfo(){
			return $this->RLInfo;
		}
	}

//!	Encapsulating real-life/meatspace info in a class so that Aurora::Addon::WebUI::UserProfile::RLInfo() can simply return NULL to indicate the absence of such information.
	class RLInfo implements IteratorAggregate{
//!	public constructor
/**
*	Although it's likely this information will be unique, until it becomes used repeatedly inside a single workflow,
*	 we're not going to do a registry method for this class as it's currently only used inside Aurora::Addon::WebUI::UserProfile::__construct() which is hidden behind a registry method.
*	@param string $name meatspace name of account holder.
*	@param string $address postal address of user.
*	@param string $zip zip/postal code of user.
*	@param string $city meatspace city of user.
*	@param string $country meatspace country of user.
*/
		public function __construct($name, $address, $zip, $city, $country){
			if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if(is_string($address) === false){
				throw new InvalidArgumentException('Address must be a string.');
			}else if(is_string($zip) === false){
				throw new InvalidArgumentException('Zip must be a string.');
			}else if(is_string($city) === false){
				throw new InvalidArgumentException('City must be as tring.');
			}else if(is_string($country) === false){
				throw new InvalidArgumentException('Country must be a string.');
			}

			$this->Name = trim($name);
			$this->Address = trim($address);
			$this->Zip = trim($zip);
			$this->City = trim($city);
			$this->Country = trim($country);
		}

//!	string meatspace name of account holder.
//!	@see Aurora::Addon::WebUI::RLInfo::Name()
		protected $Name;
//!	@see Aurora::Addon::WebUI::RLInfo::$Name
		public function Name(){
			return $this->Name;
		}

//!	string postal address of user.
//!	@see Aurora::Addon::WebUI::RLInfo::Address()
		protected $Address;
//!	@see Aurora::Addon::WebUI::RLInfo::$Address
		public function Address(){
			return $this->Address;
		}

//!	string zip/postal code of user.
//!	@see Aurora::Addon::WebUI::RLInfo::Zip()
		protected $Zip;
//!	@see Aurora::Addon::WebUI::RLInfo::$Zip
		public function Zip(){
			return $this->Zip;
		}

//!	string meatspace city of user
//!	@see Aurora::Addon::WebUI::RLInfo::City()
		protected $City;
//!	@see Aurora::Addon::WebUI::RLInfo::$City
		public function City(){
			return $this->City;
		}

//!	string meatspace country of user
//!	@see Aurora::Addon::WebUI::RLInfo::Country()
		protected $Country;
//!	@see Aurora::Addon::WebUI::RLInfo::$Country
		public function Country(){
			return $this->Country;
		}

//!	@return string
		public function __toString(){
			return trim(implode("\n", array(
				$this->Name(),
				$this->Address(),
				$this->Zip(),
				$this->City(),
				$this->Country()
			)));
		}

//!	@return object an instance of Aurora::Addon::WebUI::RLInfoIterator corresponding to this object
		public function getIterator(){
			return RLInfoIterator::r($this);
		}
	}

//!	This is Iterator here could be considered pure laziness.
	class RLInfoIterator extends abstractIterator{

//!	this gets hidden behind a registry method.
/**
*	@param object $RLInfo an instance of Aurora::Addon::WebUI::RLInfo
*/
		protected function __construct(RLInfo $RLInfo){
			$this->data = array(
				'RLName'    => $RLInfo->Name(),
				'RLAddress' => $RLInfo->Address(),
				'RLZip'     => $RLInfo->Zip(),
				'RLCity'    => $RLInfo->City(),
				'RLCountry' => $RLInfo->Country()
			);
		}

//!	Registry method.
/**
*	@param object $RLInfo an instance of Aurora::Addon::WebUI::RLInfo
*	@return object instance of Aurora::Addon::WebUI::RLInfoIterator
*/
		public static function r(RLInfo $RLInfo){
			static $registry = array();
			$hash = spl_object_hash($RLInfo);
			if(isset($registry[$hash]) === false){
				$registry[$hash] = new static($RLInfo);
			}
			return $registry[$hash];
		}
	}

//!	SearchUser class. Included in result returned by Aurora::Addon::WebUI::FindUsers()
	class SearchUser extends abstractUserHasName{

//!	We need to add in some more properties and validation since we're extending another class.
/**
*	@param string $uuid Account UUID
*	@param string $name Account name
*	@param integer $created unix timestamp of when the account was created
*	@param integer $userFlags bitfield of user flags
*/
		protected function __construct($uuid, $name, $created, $userFlags){
			if(is_integer($created) === false){
				throw new InvalidArgumentException('Created timestamp must be an integer.');
			}else if(is_integer($userFlags) === false){
				throw new InvalidArgumentException('User Flags must be an integer.');
			}

			$this->Created   = $created;
			$this->UserFlags = $userFlags;
			parent::__construct($uuid, $name);
		}

//!	Registry method.
/**
*	@param string $uuid Account UUID
*	@param string $name Account name
*	@param integer $created unix timestamp of when the account was created
*	@param integer $userFlags bitfield of user flags
*/
		public static function r($uuid, $name=null, $created=null, $userFlags=null){
			if(is_string($uuid) === false){
				throw new InvalidArgumentException('UUID must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $uuid) === false){
				throw new InvalidArgumentException('UUID was not a valid UUID.');
			}

			$uuid = strtolower($uuid);
			static $registry = array();
			$create = (isset($registry[$uuid]) === false);

			if($create === false && isset($name, $created, $userFlags) === true){
				$user = $registry[$uuid];
				$create = ($user->Name() !== $name || $user->Created() !== $created || $user->UserFlags !== $userFlags);
			}else if($created === true && isset($name, $created, $userFlags) === false){
				throw new InvalidArgumentException('Cannot create an instance of Aurora::Addon::WebUI::SearchUser if no information is specified.');
			}

			if($create){
				$registry[$uuid] = new static($uuid, $name, $created, $userFlags);
			}

			return $registry[$uuid];
		}

//!	integer unix timestamp of when the account was created.
//!	@see Aurora::Addon::WebUI::SearchUser::Created()
		protected $Created;
//!	@see Aurora::Addon::WebUI::SearchUser::$Created
		public function Created(){
			return $this->Created;
		}

//!	integer bitfield of user flags
//!	@see Aurora::Addon::WebUI::SearchUser::UserFlags()
		protected $UserFlags;
//!	@see Aurora::Addon::WebUI::SearchUser::$UserFlags
		public function UserFlags(){
			return $this->UserFlags;
		}
	}

//!	SearchUserResults iterator. Returned by Aurora::Addon::WebUI::FindUsers
	class SearchUserResults extends abstractUserIterator{

//!	public constructor
/**
*	Since Aurora::Addon::WebUI::SearchUserResults does not implement methods for appending values, calling the constructor with no arguments is a shorthand means of indicating there are no search users available.
*	@param mixed $archives an array of Aurora::Addon::WebUI::SearchUser instances or NULL
*/
		public function __construct(array $users=null){
			if(isset($users) === true){
				foreach($users as $v){
					if(($v instanceof SearchUser) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::SearchUser should be included in the array passed to Aurora::Addon::WebUI::SearchUserResults::__construct()');
					}
				}
				reset($users);
				$this->data = $users;
			}
		}
	}
}
?>