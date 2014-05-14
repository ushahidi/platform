<?php

/*
 * Value Assertion - check if certain keys of resource match a value
 * 
 * Possible use when you want to check if the resource object has a public attribute
 *
 * The assertion object requires an array of KEY => VALUE pairs, where the KEYs refer
 * to resource attribute name, and VALUEs are the static value they must match.
 *
 * For example new Acl_Assert_Value(array('status'=>'published'));
 */
 
class Acl_Assert_Value implements Acl_Assert_Interface {
	
	protected $_arguments;

	public function __construct($arguments)
	{
		$this->_arguments = $arguments;
	}
	
	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		if (is_object($resource))
		{
			foreach($this->_arguments as $resource_key => $value_match)
			{
				if(! isset($resource->$resource_key)
					OR $resource->$resource_key !== $value_match
				)
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
}
