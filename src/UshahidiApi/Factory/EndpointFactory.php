<?php

/**
 * Ushahidi API Factory for Endpoints
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\API
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace UshahidiApi\Factory;

use Ushahidi\Factory;

class EndpointFactory
{
	protected $parsers;
	protected $usecases;
	protected $validators;
	protected $authorizers;
	protected $repositories;
	protected $formatters;

	protected $endpoints = [];

	public function __construct(
		Factory\ParserFactory      $parsers,
		Factory\UsecaseFactory     $usecases,
		Factory\AuthorizerFactory  $authorizers,
		Factory\RepositoryFactory  $repositories,
		Factory\FormatterFactory   $formatters,
		$factory,
		Array $endpoints
	) {
		$this->parsers      = $parsers;
		$this->usecases     = $usecases;
		$this->authorizers  = $authorizers;
		$this->repositories = $repositories;
		$this->formatters   = $formatters;

		$this->endpoint_factory = $factory;
		foreach ($endpoints as $resource => $actions) {
			$this->endpoints[$resource] = array_merge($this->getDefaultActions(), $actions);
		}
	}

	/**
	 * Get the default actions that are available for all endpoints.
	 * @return Array
	 */
	protected function getDefaultActions()
	{
		return [
			'create' => true,
			'read'   => true,
			'update' => true,
			'delete' => true,
			'search' => true,
		];
	}

	public function get($resource, $action)
	{
		if (empty($this->endpoints[$resource][$action])) {
			throw new \Exception(sprintf('Endpoint %s.%s does not exist', $resource, $action));
		}

		$endpoint = $this->endpoint_factory;

		$auth      = $this->authorizers->get($resource);
		$parser    = $this->parsers->get($resource, $action);
		$formatter = $this->formatters->get($resource, $action);
		$usecase   = $this->usecases->get($resource, $action);

		// Load an empty entity from this resource repository.
		// The entity will be used for an early access check by the endpoint.
		$resource = $this->repositories->get($resource)->getEntity();

		return $endpoint(compact(
			'auth',
			'parser',
			'formatter',
			'usecase',
			'resource'
		));
	}
}
