<?php

/**
 * Ushahidi Platform Factory for Use Cases
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Factory;

class UsecaseFactory
{
	// Array of use cases, mapped by action:
	//
	//     $map = [
	//         'create' => $di->newFactory('Namespace\To\CreateUsecase'),
	//         'search' => $di->newFactory('Namespace\To\SearchUsecase'),
	//         ...
	//     ]
	//
	// Each action is reusable by any resource, but only if the endpoint
	// definition allows it.
	protected $map = [];

	// Array of actions that require input to know which records to fetch:
	//
	//     $read = [
	//         'read'   => true,
	//         'search' => true,
	//         'delete' => true,
	//     ];
	//
	// Read actions use a Parser to create input data.
	protected $read = [];

	// Array of actions that require input used to modify a record:
	//
	//     $write = [
	//         'create' => true,
	//         'update' => true,
	//     ];
	//
	// Read actions use a Parser to create input data.
	protected $write = [];

	/**
	 * @param  Ushahidi\Factory\AuthorizerFactory
	 */
	protected $authorizers;

	/**
	 * @param  Ushahidi\Factory\ParserFactory
	 */
	protected $parsers;

	/**
	 * @param  Ushahidi\Factory\ValidatorFactory
	 */
	protected $validators;

	/**
	 * @param  Ushahidi\Factory\RepositoryFactory
	 */
	protected $repositories;

	/**
	 * Uses collaborator factories to load use case interactors using
	 * specific collaborators for the entity/resource type.
	 *
	 * @param  Ushahidi\Factory\AuthorizerFactory $authorizers
	 * @param  Ushahidi\Factory\ParserFactory     $parsers
	 * @param  Ushahidi\Factory\RepositoryFactory $repositories
	 * @param  Ushahidi\Factory\ValidatorFactory  $validators
	 * @param  Array $map
	 * @param  Array $read
	 * @param  Array $write
	 */
	public function __construct(
		AuthorizerFactory  $authorizers,
		ParserFactory      $parsers,
		RepositoryFactory  $repositories,
		ValidatorFactory   $validators,
		Array $map,
		Array $read,
		Array $write
	) {
		$this->authorizers  = $authorizers;
		$this->parsers      = $parsers;
		$this->repositories = $repositories;
		$this->validators   = $validators;

		$this->map   = $map;
		$this->read  = $read;
		$this->write = $write;
	}

	/**
	 * Checks if a given action requires read input.
	 * @param  String $action
	 * @return Boolean
	 */
	public function isRead($action)
	{
		return !empty($this->read[$action]);
	}

	/**
	 * Checks if a given action requires write input.
	 * @param  String $action
	 * @return Boolean
	 */
	public function isWrite($action)
	{
		return !empty($this->write[$action]);
	}

	/**
	 * Gets a usecase from the map by action. Loads the tools for the usecase
	 * from the factories by resource and action.
	 * @param  String $resource
	 * @param  String $action
	 * @return Ushahidi\Usecase
	 */
	public function get($resource, $action)
	{
		if (empty($this->map[$action])) {
			throw new \Exception(sprintf('Usecase action %s is not defined', $action));
		}

		$auth = $this->authorizers->get($resource);
		$repo = $this->repositories->get($resource);

		if ($this->isWrite($action)) {
			$valid = $this->validators->get($resource, $action);
		}

		$factory = $this->map[$action];
		$params  = compact('auth', 'repo', 'valid');

		return $factory($params);
	}
}
