<?php

/**
 * Ushahidi Api Endpoint
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Api
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace UshahidiApi;

use Ushahidi\Tool\Parser;
use Ushahidi\Tool\Formatter;
use Ushahidi\Usecase;

class Endpoint
{
	protected $parser;
	protected $formatter;
	protected $usecase;

	public function __construct(
		Parser $parser,
		Formatter $formatter,
		Usecase $usecase
	) {
		$this->parser = $parser;
		$this->formatter = $formatter;
		$this->usecase = $usecase;
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
		// todo: replace __invoke with a better method name
		$input  = $this->parser->__invoke($request);
		$result = $this->usecase->interact($input);
		$output = $this->formatter->__invoke($result);

		if ($this->formatter instanceof CollectionFormatter) {
			// Collections always have additional paging metadata, which are
			// partially determined by the request input.
			$output += $this->formatter->getPaging($input);
		}

		return $output;
	}
}
