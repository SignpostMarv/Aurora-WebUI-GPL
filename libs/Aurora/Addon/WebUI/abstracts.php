<?php
//!	@file libs/Aurora/Addon/WebUI/abstracts.php
//!	@brief abstract WebUI classes
//!	@author SignpostMarv

namespace Aurora\Addon\WebUI{

	use Iterator;
	use SeekableIterator;
	use Countable;
	use ArrayAccess;

	use Aurora\Addon\WebUI;

//!	abstract iterator, not for any particular class but we don't want to duplicate code.
	abstract class abstractIterator implements Iterator, Countable{
//!	array holds the values of the iterator class
		protected $data = array();

		public function current(){
			return current($this->data);
		}

		public function key(){
			return key($this->data);
		}

		public function next(){
			next($this->data);
		}

		public function rewind(){
			reset($this->data);
		}

		public function valid(){
			return $this->key() !== null;
		}

		public function count(){
			return count($this->data);
		}
	}

//!	abstract iterator for instances of Aurora::Addon::WebUI::abstractUser
	abstract class abstractUserIterator extends abstractIterator{

//!	public constructor
/**
*	Since Aurora::Addon::WebUI::abstractUserIterator does not implement methods for appending values, calling the constructor with no arguments is a shorthand means of indicating there are no users available.
*	@param mixed $archives an array of Aurora::Addon::WebUI::abstractUser instances or NULL
*/
		public function __construct(array $archives=null){
			if(isset($archives) === true){
				foreach($archives as $v){
					if(($v instanceof abstractUser) === false){
						throw new InvalidArgumentException('Only instances of Aurora::Addon::WebUI::abstractUser should be included in the array passed to Aurora::Addon::WebUI::abstractUserIterator::__construct()');
					}
				}
				reset($archives);
				$this->data = $archives;
			}
		}
	}

//!	class for Write-Once-Read-Many implementations of ArrayAccess
	abstract class WORM extends abstractIterator implements ArrayAccess{

//!	protected constructor, hidden behind a singleton, factory or registry method.
		protected function __construct(){
		}


		public function offsetExists($offset){
			return isset($offset, $this->data[$offset]);
		}


		public function offsetGet($offset){
			return isset($this[$offset]) ? $this->data[$offset] : null;
		}


		public function offsetUnset($offset){
			throw new BadMethodCallException('data cannot be unset.');
		}

//!	Attempts to return the offset on Aurora::Addon::WebUI::WORM::$data for $value if $value exists in the instance.
/**
*	@param mixed $value
*	@return mixed FALSE if the value was not found, otherwise returns the offset.
*/
		public function valueOffset($value){
			return array_search($value, $this->data);
		}
	}

//!	abstract seekable iterator
	abstract class abstractSeekableIterator extends abstractIterator{

//!	object instance of Aurora::Addon::WebUI
		protected $WebUI;


		protected function __construct(WebUI $WebUI, $start=0, $total=0){
			if(is_integer($total) === false){
				throw new InvalidArgumentException('Total number of entities must be an integer.');
			}else if($total < 0){
				throw new InvalidArgumentException('Total number of entities must be greater than or equal to zero.');
			}
			$this->WebUI = $WebUI;
			$this->total = $total;
			$this->seek($start);
		}

//!	integer total number of groups
		protected $total;

//!	@return integer
		public function count(){
			return $this->total;
		}

//!	@return integer
		public function key(){
			return ($this->pos < $this->count()) ? $this->pos : null;
		}

//!	@return bool TRUE if the current cursor position is valid, FALSE otherwise.
		public function valid(){
			return ($this->key() !== null);
		}

//!	advance the cursor
		public function next(){
			++$this->pos;
		}

//!	integer cursor position
		protected $pos=0;

//!	Move the cursor to the specified point.
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
			}else if($to > 0 && $to >= $this->count()){
				throw new LengthException('Cannot seek past Aurora::Addon::WebUI::abstractSeekableIterator::count()');
			}

			$this->pos = $to;
		}
	}
}
?>