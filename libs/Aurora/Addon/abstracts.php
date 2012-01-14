<?php
//!	@file libs/Aurora/Addon/abstracts.php
//!	@brief abstract classes
//!	@author SignpostMarv

namespace Aurora\Addon{

	use Iterator;
	use SeekableIterator;
	use Countable;
	use ArrayAccess;

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

//!	Attempts to return the offset on Aurora::Addon::WORM::$data for $value if $value exists in the instance.
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

//!	object instance of Aurora::Addon::abstractAPI
		protected $API;

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
		protected function __construct(abstractAPI $API, $start=0, $total=0){
			if(is_integer($total) === false){
				throw new InvalidArgumentException('Total number of entities must be an integer.');
			}else if($total < 0){
				throw new InvalidArgumentException('Total number of entities must be greater than or equal to zero.');
			}
			$this->API = $API;
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
				throw new LengthException('Cannot seek past Aurora::Addon::abstractSeekableIterator::count()');
			}

			$this->pos = $to;
		}
	}
	

	abstract class abstractSeekableFilterableIterator extends abstractSeekableIterator{

//!	mixed either NULL indicating no sort filters, or an array of field name keys and boolean values indicating sort order.
		private $sort;

//!	mixed either NULL indicating no boolean filters, or an array of field name keys and boolean values.
		private $boolFields;

//!	Because we use a seekable iterator, we hide the constructor behind a registry method to avoid needlessly calling the end-point if we've rewound the iterator, or moved the cursor to an already populated position.
/**
*	@param object $API instance of Aurora::Addon::abstractAPI We need to specify this in case we want to iterate past the original set of results.
*	@param integer $start initial cursor position
*	@param integer $total Total number of results possible with specified filters
*	@param array $sort optional array of field names for keys and booleans for values, indicating ASC and DESC sort orders for the specified fields.
*	@param array $boolFields optional array of field names for keys and booleans for values, indicating 1 and 0 for field values.
*/
		protected function __construct(abstractAPI $API, $start=0, $total=0, array $sort=null, array $boolFields=null){
			parent::__construct($API, $start, $total);
			$this->sort = $sort;
			$this->boolFields = $boolFields;
		}

//! This is a registry method for a class that implements the SeekableIterator class, so we can save ourselves some API calls if we've already fetched some entities.
/**
*	@param object $API instance of Aurora::Addon::abstractAPI We need to specify this in case we want to iterate past the original set of results.
*	@param integer $start initial cursor position
*	@param integer $total Total number of results possible with specified filters
*	@param array $sort optional array of field names for keys and booleans for values, indicating ASC and DESC sort orders for the specified fields.
*	@param array $boolFields optional array of field names for keys and booleans for values, indicating 1 and 0 for field values.
*	@param array $entities if specified, should be an array of entity objects to be validated by the child constructor
*/
		public static function r(abstractAPI $API, $start=0, $total=0, array $sort=null, array $boolFields=null, array $entities=null){
			static $registry = array();
			$hash1 = spl_object_hash($API);
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
				$registry[$hash1][$hash2][$hash3] = new static($API, $start, $total, $sort, $boolFields, $entities);
			}

			$registry[$hash1][$hash2][$hash3]->seek($start);

			return $registry[$hash1][$hash2][$hash3];
		}
	}
}