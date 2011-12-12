<?php
//!	@file libs/Aurora/Addon/WebUI/Friends.php
//!	@brief Friends-related WebUI code
//!	@author SignpostMarv

namespace Aurora\Addon\WebUI{

//!	Class to be used for holding friendInfo data
/**
*	extends Aurora::Addon::WebUI::abstractUserHasName so instances of the class can be more flexibly used.
*/
	class FriendInfo extends abstractUserHasName{

//!	We want to hide this behind a registry method so the constructor needs to be protected
/**
*	@param object $with An instance of Aurora::Addon::WebUI::abstractUser corresponding to the user that the instantiated user is friends with.
*	@param string $uuid the UUID of the user that $with is friends with.
*	@param string $name the Name of the user that $with is friends with.
*	@param integer $myFlags bitfield of OpenMetaverse::FriendRights constants corresponding to rights granted by the user this instance is representing to $with
*	@param integer $theirFlags bitfield of OpenMetaverse::FriendRights constants corresponding to rights granted by $with to the user this instance is representing.
*	@see Aurora::Addon::WebUI::FriendInfo::$With
*	@see Aurora::Addon::WebUI::FriendInfo::$MyFlags
*	@see Aurora::Addon::WebUI::FriendInfo::$TheirFlags
*/
		protected function __construct(abstractUser $with, $uuid, $name, $myFlags, $theirFlags){
			if(is_string($myFlags) === true && ctype_digit($myFlags) === true){
				$myFlags = (integer)$myFlags;
			}
			if(is_string($theirFlags) === true && ctype_digit($theirFlags) === true){
				$theirFlags = (integer)$theirFlags;
			}

			parent::__construct($uuid, $name);

			if($this->PrincipalID() === $with->PrincipalID()){
				throw new InvalidArgumentException('User cannot be friends with themselves.');
			}

			if(is_integer($myFlags) === false){
				throw new InvalidArgumentException('Flags must be an integer.');
			}else if(is_integer($theirFlags) === false){
				throw new InvalidArgumentException('Flags must be an integer.');
			}

			$this->With       = $with;
			$this->MyFlags    = $myFlags;
			$this->TheirFlags = $theirFlags;
		}

//!	Registry method
/**
*	Caches instances of Aurora::Addon::WebUI::FriendInfo, refreshing objects only when necessary.
*	@param object $with An instance of Aurora::Addon::WebUI::abstractUser corresponding to the user that the instantiated user is friends with.
*	@param string $uuid the UUID of the user that $with is friends with.
*	@param mixed $name NULL for shorthand fetching of the cached object or string the Name of the user that $with is friends with.
*	@param mixed $myFlags NULL for shorthand fetching of the cached object or integer bitfield of OpenMetaverse::FriendRights constants corresponding to rights granted by the user this instance is representing to $with
*	@param mixed $theirFlags NULL for shorthand fetching of the cached object or integer bitfield of OpenMetaverse::FriendRights constants corresponding to rights granted by $with to the user this instance is representing.
*	@return object instance of Aurora::Addon::WebUI::FriendInfo
*/
		public static function r(abstractUser $with, $uuid, $name=null, $myFlags=null, $theirFlags=null){
			static $registry = array();
			if(isset($registry[$with->PrincipalID()]) === false){
				$registry[$with->PrincipalID()] = array();
			}

			$create = (isset($registry[$with->PrincipalID()][$uuid]) === false);

			if($create === true && isset($name, $myFlags, $theirFlags) === false){
				throw new InvalidArgumentException('Cannot return cached FriendInfo object, object has not been created.');
			}else if($create === false){
				$_myFlags    = (integer)$myFlags;
				$_theirFlags = (integer)$theirFlags;

				$FriendInfo = $registry[$with->PrincipalID()][$uuid];

				$create = (
					$FriendInfo->MyFlags() !== $_myFlags ||
					$FriendInfo->TheirFlags() !== $_theirFlags
				);
			}

			if($create === true){
				$registry[$with->PrincipalID()][$uuid] = new static($with, $uuid, $name, $myFlags, $theirFlags);
			}

			return $registry[$with->PrincipalID()][$uuid];
		}

//!	object instance of Aurora::Addon::WebUI::abstractUser indicating who this object is friends with.
//!	@see Aurora::Addon::WebUI::FriendInfo::With()
		protected $With;
//!	@see Aurora::Addon::WebUI::FriendInfo::$With
		public function With(){
			return $this->With;
		}


//!	integer bitfield of OpenMetaverse::FriendRights constants corresponding to rights granted by the user this instance is representing to Aurora::Addon::WebUI::FriendInfo::With()
//!	@see Aurora::Addon::WebUI::FriendInfo::MyFlags()
		protected $MyFlags;
//!	@see Aurora::Addon::WebUI::FriendInfo::$MyFlags
		public function MyFlags(){
			return $this->MyFlags;
		}

//!	integer bitfield of OpenMetaverse::FriendRights constants corresponding to rights granted by Aurora::Addon::WebUI::FriendInfo::With() to the user this instance is representing.
//!	@see Aurora::Addon::WebUI::FriendInfo::TheirFlags()
		protected $TheirFlags;
//!	@see Aurora::Addon::WebUI::FriendInfo::$TheirFlags
		public function TheirFlags(){
			return $this->TheirFlags;
		}
	}

//!	Iterator for instances of Aurora::Addon::WebUI::FriendInfo
	class FriendsList extends abstractUserIterator{

//!	restricts the contents of Aurora::Addon::WebUI::FriendsList::$data to instances of Aurora::Addon::WebUI::FriendInfo
		public function __construct(array $friendInfo=null){
			if(isset($friendInfo) === true){
				foreach($friendInfo as $v){
					if(($v instanceof FriendInfo) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::FriendInfo should be included in the array passed to Aurora::Addon::WebUI::FriendsList::__construct()');
					}
				}
				reset($friendInfo);
				$this->data = $friendInfo;
			}
		}
	}
}
?>