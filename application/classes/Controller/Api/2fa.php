<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Api_2fa extends Ushahidi_Rest {
  protected $_action_map = array
	(
		Http_Request::POST    => 'post',
		Http_Request::OPTIONS => 'options'
	);

	protected function _scope()
	{
		return 'users';
	}

	public function action_post_index()
	{
    if ($id = service('session.user')->getId()) {
      // Replace the "me" id with the actual id
		  $this->_usecase = service('factory.usecase')
			  ->get($this->_scope(), 'generategoogle2fa')
			  ->setPayload($this->_payload())
        ->setIdentifiers($this->_identifiers())
        ->setIdentifiers(['id' => $id])
        ->setFormatter(service('formatter.output.json'));
	  }
  } 
}
