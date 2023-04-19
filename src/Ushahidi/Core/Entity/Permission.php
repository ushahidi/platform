<?php

/**
 * Ushahidi Permission Entity
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Contracts\Entity;

interface Permission extends Entity
{
        // FIXME: this LEGACY_DATA_IMPORT has to be removed after the prod release
        const LEGACY_DATA_IMPORT    = 'Bulk Data Import';
        // Standard permissions names
        const DATA_IMPORT_EXPORT    = 'Bulk Data Import and Export';
        const MANAGE_POSTS          = 'Manage Posts';
        const MANAGE_SETS           = 'Manage Collections and Saved Searches';
        const MANAGE_SETTINGS       = 'Manage Settings';
        const MANAGE_USERS          = 'Manage Users';
        const EDIT_OWN_POSTS        = 'Edit their own posts';
        const DELETE_POSTS          = 'Delete Posts';
        const DELETE_OWN_POSTS      = 'Delete Their Own Posts';
}
