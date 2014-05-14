<?php

/*
 * Relation Value Assertion - check if certain keys on a relation of the resource match a value
 * 
 * Possible use when you want to check if the resource parent object has a public attribute
 *
 * The assertion object requires an array of KEY => VALUE pairs, where the KEYs refer
 * to resource attribute name, and VALUEs are the static value they must match.
 * You must also pass a special 'relation' key to define the relation to check the value on
 *
 * For example new Acl_Assert_RelationValue(array('relation' => 'parent', 'status'=>'published'));
 */
 
class Acl_Assert_RelationValue implements Acl_Assert_Interface {
	
	protected $_arguments;
	protected $_relation;

	public function __construct($arguments)
	{
		$this->_relation = $arguments['relation'];
		unset($arguments['relation']);
		
		$this->_arguments = $arguments;
	}
	
	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		if (is_object($resource))
		{
			// If the relation doesn't exist, assume we're OK.
			$relation = $resource->{$this->_relation};
			if (! $relation->loaded())
			{
				return TRUE;
			}
			
			foreach($this->_arguments as $relation_key => $value_match)
			{
				if(! isset($relation->$relation_key)
					OR $relation->$relation_key !== $value_match
				)
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
}
