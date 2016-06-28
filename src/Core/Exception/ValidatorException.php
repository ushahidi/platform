<?php

/**
 * Ushahidi Platform Validator Exception
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Exception;

use RecursiveIteratorIterator;
use RecursiveArrayIterator;

class ValidatorException extends \InvalidArgumentException
{
	private $errors;

	public function __construct($message, Array $errors, Exception $previous = null)
	{
		$flatErrors = iterator_to_array(new RecursiveIteratorIterator(new RecursiveArrayIterator($errors)), false);

		$message = $message . ":\n" . implode("\n", $flatErrors);

		parent::__construct($message, 0, $previous);
		$this->setErrors($errors);
	}

	public function setErrors(Array $errors)
	{
		$this->errors = $errors;
	}

	public function getErrors()
	{
		return $this->errors ?: array();
	}
}
