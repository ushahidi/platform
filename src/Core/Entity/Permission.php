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

use Ushahidi\Core\StaticEntity;

class Permission extends StaticEntity
{
    protected $id;
    protected $name;
    protected $description;
    // FIXME: this LEGACY_DATA_IMPORT has to be removed after the prod release
    const LEGACY_DATA_IMPORT    = 'Bulk Data Import';
    // Standard permissions names
    const DATA_IMPORT_EXPORT    = 'Bulk Data Import and Export';
    const MANAGE_POSTS          = 'Manage Posts';
    const MANAGE_SETS           = 'Manage Collections and Saved Searches';
    const MANAGE_SETTINGS       = 'Manage Settings';
    const MANAGE_USERS          = 'Manage Users';
    const EDIT_OWN_POSTS        = 'Edit their own posts';

    // DataTransformer
    public function getDefinition()
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'description' => 'string',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'permission';
    }

    // StatefulData
    protected function getImmutable()
    {
        return array_merge(parent::getImmutable(), ['name']);
    }
}
