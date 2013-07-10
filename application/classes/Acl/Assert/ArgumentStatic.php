<?php

/*
 * Static Argument Assertion - check if certain keys of resource match a static value
 * 
 * Possible use when you want to check if the resource object has a public attribute
 *
 * The assertion object requires an array of KEY => VALUE pairs, where the KEYs refer
 * to resource attribute name, and VALUEs are the static value they must match.
 *
 * For example new Acl_Assert_Argument(array('status'=>'published'));
 */
 
class Acl_Assert_ArgumentStatic implements Acl_Assert_Interface {
	
	protected $_arguments;

	public function __construct($arguments)
	{
		$this->_arguments = $arguments;
	}
	
	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		if (! $resource instanceof Acl_Resource_Interface)
		{
			return FALSE;
		}
		
		foreach($this->_arguments as $resource_key => $value_match)
		{
			if(! isset($resource->$resource_key)
				OR $resource->$resource_key !== $value_match
			)
			{
				return FALSE;
			}
		}
		
		return TRUE;
	}
}