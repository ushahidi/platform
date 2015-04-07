<?php

/**
 * Writer Trait - Adds writer property and setWriter method
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport;

use Ddeboer\DataImport\Writer\WriterInterface;

trait WriterTrait {

	/**
	 * Writer
	 * @var Writer
	 */
	protected $writer;

	/**
	 * Set writer
	 * @param Writer $writer
	 */
	public function setWriter(WriterInterface $writer)
	{
		$this->writer = $writer;
	}

	/**
	 * Get writer
	 */
	public function getWriter()
	{
		return $this->writer;
	}

}
