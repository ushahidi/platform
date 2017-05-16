<?php

/**
 * Ushahidi Platform Validator Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Traits\GetSet;
use Ushahidi\Core\Tool\Validation;
use Ushahidi\Core\Tool\ValidationEngineTrait;

abstract class Validator
{
	use GetSet;
	use ValidationEngineTrait;

	protected $default_error_source = null;

	// Regex that only allows letters, numbers, punctuation, and space.
	const REGEX_STANDARD_TEXT = '/^[\pL\pN\pP ]++$/uD';

	/**
	 * Must return an array of rules that the validator should apply
	 *
	 * @return  Array  $rules array of $key => $rule
	 */
	abstract protected function getRules();

	/**
	 * Check the data against the rules returned by getRules()
	 *
	 * @param  Array $data      an array of changed values to check in $key => $value format
	 * @param  Array $fullData  an array of full entity data for reference during validation
	 * @return bool
	 */
	public function check(array $data, array $fullData = [])
	{
		// If no full data is passed, fallback to changed values
		if (!$fullData) {
			$fullData = $data;
		}

		$this->validation_engine->setFullData($fullData);
		$this->validation_engine->setData($data);
		$this->attachRules($this->getRules());
		return $this->validation_engine->check();
	}

	/**
	 * Return an array of any errors that occurred during validation
	 *
	 * @param  String $source
	 * @return Array
	 */
	public function errors($source = null)
	{
		return $this->validation_engine->errors($source ?: $this->default_error_source);
	}

	/**
	 * Attach a set of rules to the validator
	 * @param  array  $rules Array of rules in $key => $rule format
	 */
	protected function attachRules($rules = array())
	{
		foreach ($rules as $name => $ruleset) {
			$this->validation_engine->rules($name, $ruleset);
		}
	}
}
