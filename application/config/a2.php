<?php

return array(

	/*
	 * The Authentication library to use
	 * Make sure that the library supports:
	 * 1) A get_user method that returns FALSE when no user is logged in
	 *	  and a user object that implements Acl_Role_Interface when a user is logged in
	 * 2) A static instance method to instantiate a Authentication object
	 *
	 * array(CLASS_NAME,array $arguments)
	 */
	'lib' => array(
		'class'  => 'A1', // (or AUTH)
		'params' => array(
			'name' => 'a1'
		)
	),

	/**
	 * Throws an exception when authorization fails.
	 */
	'exception' => FALSE,

	/**
	 * Exception class to throw when authorization fails (eg 'HTTP_Exception_401')
	 */
	'exception_type' => 'a2_exception',

	/*
	 * The ACL Roles (String IDs are fine, use of ACL_Role_Interface objects also possible)
	 * Use: ROLE => PARENT(S) (make sure parent is defined as role itself before you use it as a parent)
	 */
	'roles' => array
	(
		// ADD YOUR OWN ROLES HERE
		'user'  => 'guest',
		'admin' => 'user'
	),

	/*
	 * The name of the guest role
	 * Used when no user is logged in.
	 */
	'guest_role' => 'guest',

	/*
	 * The name of the user role
	 * Used when user is logged in but has no role.
	 */
	'user_role' => 'user',

	/*
	 * The ACL Resources (String IDs are fine, use of ACL_Resource_Interface objects also possible)
	 * Use: ROLE => PARENT (make sure parent is defined as resource itself before you use it as a parent)
	 */
	'resources' => array
	(
		'api'                => NULL, // Not sure we really need this?
		'posts'              => NULL,
		'tags'               => NULL,
		'sets'               => NULL,
		'media'              => NULL,
		'forms'              => NULL,
		'form_attributes'    => 'forms',
		'form_groups'        => 'forms',
		'users'              => NULL,
		'messages'           => NULL,
		'dataproviders'      => NULL,
		'stats'              => NULL,
		// Pages
		'login'              => NULL,
		'register'           => NULL,
		'logout'             => NULL,
		// Special default value - used to ensure dev assign some resource id
		'undefined'          => NULL,
	),

	/*
	 * The ACL Rules (Again, string IDs are fine, use of ACL_Role/Resource_Interface objects also possible)
	 * Split in allow rules and deny rules, one sub-array per rule:
	     array( ROLES, RESOURCES, PRIVILEGES, ASSERTION)
	 *
	 * Assertions are defined as follows :
			array(CLASS_NAME,$argument) // (only assertion objects that support (at most) 1 argument are supported
			                            //  if you need to give your assertion object several arguments, use an array)
	 */
	'rules' => array
	(
		'allow' => array
		(
			'AdminAllowAll' => array(
				'role' => 'admin'
			),
			// User
			'UserCanEditOwnPost' => array(
				'role'      => 'user',
				'resource'  => 'posts',
				'privilege' => array('get', 'put', 'delete'),
				'assertion' => array('Acl_Assert_Argument', array('id' => 'user_id'))
			),
			'UserCanLogout' => array(
				'role'      => 'user',
				'resource'  => 'logout'
			),
			'UserCanCreateSet' => array(
				'role'      => 'user',
				'resource'  => 'sets',
				'privilege' => array('post')
			),
			'UserCanEditOwnSet' => array(
				'role'      => 'user',
				'resource'  => 'sets',
				'privilege' => array('put', 'delete'),
				'assertion' => array('Acl_Assert_Argument', array('id' => 'user_id'))
			),
			'UserCanEditOwnUser' => array(
				'role'      => 'user',
				'resource'  => 'users',
				'privilege' => array('put', 'get', 'delete', 'get_full'),
				'assertion' => array('Acl_Assert_Argument', array('id' => 'id'))
			),
			// Guest
			'GuestCanViewPublicPost' => array(
				'role'      => 'guest',
				'resource'  => 'posts',
				'privilege' => array('get'),
				'assertion' => array('Acl_Assert_Combined', array(
					array('Acl_Assert_Value', array('status' => 'published')), // Post itself is published
					array('Acl_Assert_RelationAllowed', array('parent')), // Parent post is published
				))
			),
			'GuestCanCreatePost' => array(
				'role'      => 'guest',
				'resource'  => 'posts',
				'privilege' => array('post')
			),
			'GuestCanCreateMedia' => array(
				'role'      => 'guest',
				'resource'  => array('media'),
				'privilege' => array('post'),
			),
			'GuestCanViewForms' => array(
				'role'      => 'guest',
				'resource'  => array('forms'),
				'privilege' => array('get'),
				'assertion' => array('Acl_Assert_RelationAllowed', array('parent'))
			),
			'GuestCanViewTags' => array(
				'role'      => 'guest',
				'resource'  => array('tags'),
				'privilege' => array('get'),
				'assertion' => array('Acl_Assert_RelationAllowed', array('parent'))
			),
			'GuestCanViewMedia' => array(
				'role'      => 'guest',
				'resource'  => array('media'),
				'privilege' => array('get'),
			),
			'GuestCanViewSets' => array(
				'role'      => 'guest',
				'resource'  => array('sets'),
				'privilege' => array('get')
			),
			'GuestCanViewUsers' => array(
				'role'      => 'guest',
				'resource'  => array('users'),
				'privilege' => array('get')
			),
			'GuestCanLogin' => array(
				'role'      => 'guest',
				'resource'  => 'login'
			),
			'GuestCanRegister' => array(
				'role'      => 'guest',
				'resource'  => 'register'
			),
		),
		'deny' => array
		(
			'UserCantLogin' => array(
				'role'      => 'user',
				'resource'  => 'login'
			),
			'UserCantRegister' => array(
				'role'      => 'user',
				'resource'  => 'register'
			),
			// Block admin from resources with undefined permissions
			'AdminCantAccessUndefined' => array(
				'role'      => 'admin',
				'resource'  => 'undefined'
			),
		)
	)
);
