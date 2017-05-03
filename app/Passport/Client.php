<?php


namespace Ushahidi\App\Passport;

use Laravel\Passport\Client as LaravelPassportClient;

class Client extends LaravelPassportClient
{

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
	public $incrementing = false;
}
