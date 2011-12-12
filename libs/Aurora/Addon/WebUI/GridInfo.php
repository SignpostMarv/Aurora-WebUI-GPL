<?php
//!	@file libs/Aurora/Addon/WebUI/GridInfo.php
//!	@brief GridInfo-related WebUI code
//!	@author SignpostMarv


namespace Aurora\Addon\WebUI{

//! class for representing the online status result of an API query.
/**
*	Now the simplest approach would just be to return the object that from json_decode() in Aurora::Addon::WebUI::makeCallToAPI(), but stdClass doesn't prevent properties being removed or overwritten.
*/
	class OnlineStatus{
//!	Constructor used by Aurora::Addon::WebUI::OnlineStatus()
/**
*	We could- if we wanted to be really paranoid- examine the backtrace to make sure that the constructor is online called from within Aurora::Addon::WebUI::OnlineStatus(), but that would be a waste of resources.
*	@param boolean $Online TRUE means the grid is online, FALSE otherwise.
*	@param boolean $LoginEnabled TRUE means the grid has logins enabled, FALSE otherwise.
*	@see Aurora::Addon::WebUI::OnlineStatus::maybe2bool()
*	@see Aurora::Addon::WebUI::OnlineStatus::$Online
*	@see Aurora::Addon::WebUI::OnlineStatus::$LoginEnabled
*/
		public function __construct($Online, $LoginEnabled){
			self::maybe2bool($Online);
			self::maybe2bool($LoginEnabled);

			if(is_bool($Online) === false){
				throw new InvalidArgumentException('Online should be boolean');
			}else if(is_bool($LoginEnabled) === false){
				throw new InvalidArgumentException('LoginEnabled should be boolean');
			}

			$this->Online       = $Online;
			$this->LoginEnabled = $LoginEnabled;
		}

//!	boolean
//!	@see Aurora::Addon::WebUI::OnlineStatus::Online()
		protected $Online;
//!	@see Aurora::Addon::WebUI::OnlineStatus::$Online
//!	@return boolean TRUE if grid is online, FALSE otherwise.
		public function Online(){
			return $this->Online;
		}

//!	boolean
//!	@see Aurora::Addon::WebUI::OnlineStatus::LoginEnabled()
		protected $LoginEnabled;
//!	@return boolean TRUE if logins are enabled, FALSE otherwise.
//!	@see Aurora::Addon::WebUI::OnlineStatus::$LoginEnabled
		public function LoginEnabled(){
			return $this->LoginEnabled;
		}

//!	deduplication of code to convert arguments to Aurora::Addon::WebUI::OnlineStatus::__construct() to boolean.
//!	@param mixed $val passed by reference
		final protected static function maybe2bool(& $val){
			if(is_integer($val) === true){
				$val = ($val !== 0);
			}
		}
	}

//!	This is a really basic implementation, purely to give Aurora::Addon::WebUI::get_grid_info() a strong return type.
	class GridInfo extends WORM{

//!	factory method
		public static function f(){
			return new static();
		}


		public function offsetSet($offset, $value){
			if(is_string($offset) === false){
				throw new InvalidArgumentException('Offsets must be strings.');
			}else if(ctype_graph($offset) === false){
				throw new InvalidArgumentException('Offsets cannot have whitespace.');
			}else if(is_scalar($value) === false){
				throw new InvalidArgumentException('Values must be scalar.');
			}else if(isset($this[$offset]) === true){
				throw new InvalidArgumentException('That offset already exists.');
			}

			$this->data[$offset] = $value;
		}
	}
}
?>