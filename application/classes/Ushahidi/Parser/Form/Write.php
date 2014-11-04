<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Ushahidi Create Form Parser
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Core\Tool\Parser;
use Ushahidi\Core\Exception\ParserException;
use Ushahidi\Core\Usecase\Form\FormData;

class Ushahidi_Parser_Form_Write implements Parser
{
	public function __invoke(Array $data)
	{
		$valid = Validation::factory($data)
			->rules('name', [
				['not_empty'],
			])
			->rules('description', [
				['not_empty'],
			]);

		if (!$valid->check())
		{
			throw new ParserException("Failed to parse form write request", $valid->errors('form'));
		}

		// Ensure that all properties of a Form entity are defined by using Arr::extract
		return new FormData(Arr::extract($data, ['id', 'name', 'description', 'disabled']));
	}
}
