<?php
//!	@file libs/Aurora/Addon/WebUI/abstracts.php
//!	@brief abstract WebUI classes
//!	@author SignpostMarv

namespace Aurora\Addon\WebUI{

	use Aurora\Addon\abstractIterator;

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
}
?>