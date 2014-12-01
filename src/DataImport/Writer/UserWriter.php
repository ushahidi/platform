<?php

/**
 * Ushahidi Platform Data Import User Writer
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\DataImport\Writer;

use Ddeboer\DataImport\Writer\WriterInterface;
use Ushahidi\Core\Usecase\User\UserData;
use Ushahidi\Core\Usecase\CreateRepository;

class UserWriter implements WriterInterface
{

	/**
	 * @param Repository $repo
	 */
	public function __construct(CreateRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * {@inheritDoc}
	 */
	public function prepare()
	{
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function writeItem(array $item)
	{
		$data = new UserData($item);
		$this->repo->create($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function finish()
	{
		return $this;
	}
}
