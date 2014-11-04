<?php

/**
 * Ushahidi Api Endpoint
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Api
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Api;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\AuthorizerTrait;
use Ushahidi\Core\Tool\ParserTrait;
use Ushahidi\Core\Tool\FormatterTrait;
use Ushahidi\Core\Usecase;

use Ushahidi\Core\Exception\AuthorizerException;

class Endpoint
{
	// Uses several traits to assign tools. Each of these traits provides a
	// setter method for the tool. For example, the AuthorizerTrait provides
	// a `setAuthorizer` method which only accepts `Authorizer` instances.
	use AuthorizerTrait,
		ParserTrait,
		FormatterTrait;

	/**
	 * @var Ushahidi\Core\Entity
	 */
	protected $resource;

	/**
	 * @var Ushahidi\Core\Usecase
	 */
	protected $usecase;

	/**
	 * Takes an array of tools assigns them by type.
	 * @param  Array $tools
	 */
	public function __construct(Array $tools)
	{
		$this->setAuthorizer($tools['auth']);
		$this->setFormatter($tools['formatter']);
		$this->setParser($tools['parser']);
		$this->setUsecase($tools['usecase']);
		$this->setResource($tools['resource']);
	}

	/**
	 * One of the CRUDS use cases.
	 * @param  Ushahidi\Core\Usecase $usecase
	 * @return void
	 */
	private function setUsecase(Usecase $usecase)
	{
		$this->usecase = $usecase;
	}

	/**
	 * An empty resource to be used as an early visibility check.
	 * @param  Ushahidi\Core\Entity $resource
	 * @return void
	 */
	private function setResource(Entity $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * Get the minimum possible action required to access the endpoint.
	 * @return String
	 */
	protected function getAccessAction()
	{
		return 'read';
	}

	/**
	 * Runs the API endpoint input/output sequence:
	 *
	 * - convert raw request data into input data
	 * - pass the input to the usecase to get a result
	 * - format the result for the response output
	 * - return the formatted result
	 *
	 * @param  Array $request raw input data
	 * @return mixed
	 */
	public function run(Array $request)
	{
		if (!$this->auth->isAllowed($this->resource, $this->getAccessAction())) {
			throw new AuthorizerException(sprintf(
				'User %d is not allowed to %s the %s endpoint',
				$this->auth->getUserId(),
				$this->getAccessAction(),
				$this->resource->getResource()
			));
		}

		// todo: replace __invoke with a better method name
		$input  = $this->parser->__invoke($request);
		$result = $this->usecase->interact($input);
		$output = $this->formatter->__invoke($result);

		if (method_exists($this->formatter, 'getPaging')) {
			// Collections always have additional paging metadata, which are
			// partially determined by the request input.
			$output += $this->formatter->getPaging($input);
		}

		return $output;
	}
}
