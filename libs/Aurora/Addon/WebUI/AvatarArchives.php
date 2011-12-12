<?php
//!	@file libs/Aurora/Addon/WebUI/AvatarArchives.php
//!	@brief Avatar Archive-related WebUI code
//!	@author SignpostMarv

namespace Aurora\Addon\WebUI{

//!	basic stub class for individual avatar archives.
	class basicAvatarArchive{

//!	We're not going to use a registry method here now (although we may in the future), so we're leaving the constructor as a public method.
/**
*	@param string $name Name of the archive
*	@param string $snapshot UUID of a texture that shows off this archive.
*/
		public function __construct($name, $snapshot){
			if(is_string($name) === true){
				$name = trim($name);
			}

			if(is_string($name) === false){
				throw new InvalidArgumentException('Name must be a string.');
			}else if(is_string($snapshot) === false){
				throw new InvalidArgumentException('Snapshot must be a string.');
			}else if(preg_match(\Aurora\Addon\WebUI::regex_UUID, $snapshot) !== 1){
				throw new InvalidArgumentException('Snapshot was not a valid UUID.');
			}

			$this->Name = $name;
			$this->Snapshot = $snapshot;
		}

//!	string Name of the archive
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::Name()
		protected $Name;
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::$Name
		public function Name(){
			return $this->Name;
		}

//!	string UUID of a texture that shows off this archive
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::Snapshot()
		protected $Snapshot;
//!	@see Aurora::Addon::WebUI::basicAvatarArchive::$Snapshot
		public function Snapshot(){
			return $this->Snapshot;
		}
	}

//!	Iterator for instances of Aurora::Addon::WebUI::basicAvatarArchive
	class AvatarArchives extends abstractIterator{

//!	public constructor
/**
*	Since Aurora::Addon::WebUI::AvatarArchives does not implement methods for appending values, calling the constructor with no arguments is a shorthand means of indicating there are no avatar archives available.
*	@param mixed $archives an array of Aurora::Addon::WebUI::basicAvatarArchive instances or NULL
*/
		public function __construct(array $archives=null){
			if(isset($archives) === true){
				foreach($archives as $v){
					if(($v instanceof basicAvatarArchive) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::basicAvatarArchive should be included in the array passed to Aurora::Addon::WebUI::AvatarArchives::__construct()');
					}
				}
				reset($archives);
				$this->data = $archives;
			}
		}
	}
}
?>