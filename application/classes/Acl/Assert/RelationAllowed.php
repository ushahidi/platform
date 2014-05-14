<?php

/*
 * Relation Allowed Assertion - check if access is allowed to a relation
 * 
 * Possible use when you want to check if the resource parent object has a public attribute
 *
 * The assertion object requires an array of KEY => VALUE pairs, where the KEYs refer
 * to resource attribute name, and VALUEs are the static value they must match.
 * You must also pass a special 'relation' key to define the relation to check the value on
 *
 * For example new Acl_Assert_RelationAllowed(array('parent'));
 */
 
class Acl_Assert_RelationAllowed implements Acl_Assert_Interface {
	
	protected $_relations;

	public function __construct($relations)
	{
		$this->_relations = is_array($relations) ? $relations : array($relations);
	}
	
	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		if (is_object($resource))
		{
			foreach($this->_relations as $relation)
			{
				$relation = $resource->$relation;
				// If the relation doesn't exist, assume we're OK.
				if (! $relation->loaded())
				{
					continue; 
				}
				
				if(! $acl->is_allowed($role, $relation, $privilege))
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
}
