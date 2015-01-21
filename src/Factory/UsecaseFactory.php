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
	// Array of common use cases, mapped by action:
	//
	//     $actions = [
	//         'create' => $di->newFactory('Namespace\To\CreateUsecase'),
	//         'search' => $di->newFactory('Namespace\To\SearchUsecase'),
	//         ...
	//     ]
	//
	// Each action is reusable by any resource, but only if the endpoint
	// definition allows it.
	protected $actions = [];

	// Array of specific use casess, mapped by resource and action:
	//
	//     $map['messsages']['create'] = $di->lazyNew('Namespace\To\OverloadedUsecase');
	//
	// Actions correspond with usecases, resources with entity types.
	protected $map = [];

	/**
	 * @param  AuthorizerFactory
	 */
	protected $authorizers;

	/**
	 * @param  FormatterFactory
	 */
	protected $formatters;

	/**
	 * @param  RepositoryFactory
	 */
	protected $repositories;

	/**
	 * @param  ValidatorFactory
	 */
	protected $validators;

	/**
	 * Uses collaborator factories to load use case interactors using
	 * specific collaborators for the entity/resource type.
	 *
	 * @param  AuthorizerFactory $authorizers
	 * @param  RepositoryFactory $repositories
	 * @param  FormatterFactory  $formatters
	 * @param  Array $actions
	 * @param  Array $map
	 */
	public function __construct(
		AuthorizerFactory $authorizers,
		DataFactory       $data,
		FormatterFactory  $formatters,
		RepositoryFactory $repositories,
		ValidatorFactory  $validators,
		Array $actions,
		Array $map
	) {
		$this->authorizers  = $authorizers;
		$this->data         = $data;
		$this->formatters   = $formatters;
		$this->repositories = $repositories;
		$this->validators   = $validators;

		$this->actions = $actions;
		$this->map     = $map;
	}

	/**
	 * Gets a usecase from the map by action. Loads the tools for the usecase
	 * from the factories by resource and action.
	 *
	 *     $read_post = $usecases->get('posts', 'read');
	 *
	 * @param  String $resource
	 * @param  String $action
	 * @return Ushahidi\Core\Usecase
	 */
	public function get($resource, $action)
	{
		if (isset($this->map[$resource][$action])) {
			$factory = $this->map[$resource][$action];
		} elseif (isset($this->actions[$action])) {
			$factory = $this->actions[$action];
		}

		if (empty($factory)) {
			throw new \Exception(sprintf('Usecase %s.%s is not defined', $resource, $action));
		}

		$usecase = $factory()
			->setAuthorizer($this->authorizers->get($resource))
			->setRepository($this->repositories->get($resource))
			->setFormatter($this->formatters->get($resource, $action))
			;

		if ($usecase->isWrite()) {
			$usecase->setValidator($this->validators->get($resource, $action));
		}

		if ($usecase->isSearch()) {
			$usecase->setData($this->data->get($action));
		}

		return $usecase;
	}
}
