<?php

/**
 * Ushahidi Role
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Entity;

use Ushahidi\Core\EloquentEntity;

class Role extends EloquentEntity
{
    protected $id;
    protected $name;
    protected $display_name;
    protected $description;
    protected $permissions;
    protected $protected;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'           => 'int',
            'name'         => 'string',
            'display_name' => 'string',
            'description'  => 'string',
            'permissions'  => 'array',
            'protected'    => 'boolean',
        ];
    }

    // Entity
    public function getResource()
    {
        return 'roles';
    }

    // Entity
    public function getId()
    {
        return $this->getAttribute('name');
    }

    // StatefulData
    protected function getImmutable()
    {
        return array_merge(parent::getImmutable(), ['name','protected']);
    }
}
