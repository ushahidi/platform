<?php

namespace Ushahidi\Entity;

use Ushahidi\Traits\ArrayExchange;

class Contact
{
	use ArrayExchange;

	public $id;
	public $user_id;
	public $data_provider;
	public $type;
	public $contact;
	public $created;
}
