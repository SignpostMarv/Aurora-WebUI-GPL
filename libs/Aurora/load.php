<?php
//!	This file loads all the code for the Aurora goodness.
/**
*	@author SignpostMarv
*/

//!	working in the global namespace here
namespace{
	require_once('Framework.php');
	require_once('Addon.php');

//!	Class used for globally-available stuff, because a) define and const only support scalar values, and b) the global operate is ugly.
/**
*	We're making this "final" for semantic reasons.
*	The suggested usage is to alias IHateGlobals to Globals (or G, or whatever you want, really) then:
*	Globals::i()->foo = 'bar'; echo Globals::i()->foo;
*	Globals::i()->baz = function(){ return 'bat'; }; echo Globals::i()->bat();
*/
	final class IHateGlobals{

//!	We're going to use a singelton method, so we need to make the constructor non-public.
		private function __construct(){}

//!	Returns an instance of IHateGlobals via a singelton pattern.
/**
*	Marv's habit here is to use single-letter method names for singelton (i), registry (r) and factory (f) methods.
*	Although in this scheme singelton should probably have "s" instead of "i", what we're returning is the sole instance of a class (emphasis on instance).
*	The instance is held as a static variable inside the method because we want to ensure it's not interfered with by other methods.
*	@return object an instance of IHateGlobals
*/
		final public static function i(){
			static $instance;
			if(isset($instance) === false){
				$instance = new self();
			}
			return $instance;
		}

//!	Stores all the values
		private $data = array();

//!	Attempts to set the $value to the specified $name.
/**
*	Do bear in mind that setting a global to null won't stop it from being overwritten. But you shouldn't be doing that anyway.
*	@param string $name
*	@param mixed $value this can be any valid type in PHP, not just scalar values. So feel free to put anonymous functions in.
*/
		final public function __set($name, $value){
			if(isset($this->data[$name]) === true){ // if the name has already been assigned a value,
				if($value !== $this->data[$name]){ // silently fail if the value is identical, throwing an exception otherwise.
					throw new RuntimeException('Cannot overrwite globals');
				}
			}else if(is_string($name) === false || ctype_graph($name) === false){ // this is a lazy (e.g. non-regex) way of checking the name is valid, although is possibly superfluous since if one is doing Globals::i()->foo, the PHP interpreter should ensure the property $name is valid anyway.
				throw new InvalidArgumentException('Global name is invalid.');
			}
			$this->data[$name] = $value;
		}

//!	Attempts to return the value, silently failing by returning null if the property wasn't set.
/**
*	@param string $name
*	@return mixed
*/
		final public function __get($name){ // since there's no other way to put properties in, we're not being paranoid here.
			return isset($this->data[$name]) ? $this->data[$name] : null;
		}

//!	Determine whether or not a property has been set.
/**
*	@param string $name 
*	@return TRUE if the property has been set, FALSE otherwise.
*/
		final public function __isset($name){
			return isset($this->data[$name]);
		}

//!	Since globals cannot be overwritten, they cannot be unset either.
/**
*	@param string $name 
*/
		final public function __unset($name){
			if(isset($this->data[$name]) === false){ // we will silently fail when values have not been set.
				throw new RuntimeException('Globals cannot be overwritten');
			}
		}

//!	Here's the fun bit. Support for adding anonymous functions.
/**
*	@param string $name
*	@param array $arguments
*	@return mixed returns the value of the callback given the supplied $arguments or throws an exception if the specified $name is not callable for whatever reason.
*/
		final public function __call($name, array $arguments){
			if(isset($this->data[$name]) === false){
				throw new BadMethodCallException('The requested method does not exist');
			}else if(is_callable($this->data[$name]) === false){
				throw new BadMethodCallException('A value was found with the specified name, but it was not callable.');
			}
			return call_user_func_array($this->data[$name], $arguments);
		}

//!	Wraps to IHateGlobals::__call()
/**
*	@param string $name
*	@param array $arguments
*/
		final public static function __callStatic($name, array $arguments){
			if(self::i()->__isset($name) === false){ // The reason why we're not just passing straight to IHateGlobals::__call() is in the event IHateGlobals::__callStatic() is called directly- one could put an invalid value in $name.
				throw new BadMethodCallException('The requested method does not exist.');
			}
			return self::i()->__call($name, $arguments); // if $name is actually present, assume it was a valid property- otherwise it would not have been able to have been set to begin with- and jump to IHateGlobals::__call()
		}
	}
}
?>