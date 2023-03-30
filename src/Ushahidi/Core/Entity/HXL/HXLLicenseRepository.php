<?php

/**
 * Repository for HXLLicense
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Platform
 * @copyright 2022 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity\HXL;

use Ushahidi\Core\Contracts\Repository\EntityGet;
use Ushahidi\Core\Contracts\Repository\ReadRepository;
use Ushahidi\Core\Contracts\Repository\SearchRepository;

interface HXLLicenseRepository extends
    EntityGet,
    ReadRepository,
    SearchRepository
{
}
