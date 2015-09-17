<?php

/**
 * Ushahidi Client Context Trait
 *
 * Gives objects methods for setting and retrieving the client context.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Traits;

use Ushahidi\Core\Entity\Client;

trait ClientContext
{
	// storage for the client
	protected $client;

	/**
	 * Set the client context.
	 * @param  Client $client  set the context
	 * @return void
	 */
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * Get the client context.
	 * @return Client
	 */
	public function getClient()
	{
		if (!$this->client) {
			throw new RuntimeException('Cannot get the client context before it has been set');
		}

		return $this->client;
	}

	/**
	 * Get the clientid for this context.
	 * @return Integer
	 */
	public function getClientId()
	{
		return $this->client->id;
	}
}
