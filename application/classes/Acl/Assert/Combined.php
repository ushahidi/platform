<?php

/*
 * Combined Assertion - combine multiple assertions
 *
 * The assertion object requires an array assertions.
 * Assertions can be formatted as
 * - array('Assertion_Name')
 * - array('Assertion_Name', array('params'))
 * - 'Assertion_Name'
 * - new Acl_Assert_Argument
 *
 * For example new Acl_Assert_Combined(array(
 *  array('Acl_Assert_Value', array('status' => 'published')),
 *  array('Acl_Assert_RelationValue', array('relation' => 'parent', 'status' => 'published')),
 *  );
 */
 
class Acl_Assert_Combined implements Acl_Assert_Interface {
	
	protected $_assertions = array();

	/**
	 * @param array $assertions
	 */
	public function __construct($assertions)
	{
		foreach ($assertions as $assertion)
		{
			// create assert object
			if ( is_array($assertion))
			{
				$assertion = count($assertion) === 2
					? new $assertion[0]($assertion[1])
					: new $assertion[0];
			}
			elseif ( is_string($assertion) )
			{
				$assertion = new $assertion;
			}
			$this->_assertions[] = $assertion;
		}
	}
	
	public function assert(Acl $acl, $role = null, $resource = null, $privilege = null)
	{
		// Trigger each assertion, and fail if any assertions fail.
		foreach($this->_assertions as $assertion)
		{
			if (! $assertion->assert($acl, $role, $resource, $privilege)) return FALSE;
		}
		
		return TRUE;
	}
}