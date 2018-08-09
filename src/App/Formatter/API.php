<?php

/**
 * Ushahidi API Formatter
 *
 * Takes an entity object and returns an array.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Formatter;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Tool\Formatter;
use Ushahidi\Core\Exception\FormatterException;
use Ushahidi\App\Http\Controllers\RESTController;
use Illuminate\Support\Str;

class API implements Formatter
{
    // Formatter
    public function __invoke($entity)
    {
        if (!($entity instanceof Entity)) {
            throw new FormatterException("API formatter requries an Entity as input");
        }

        $fields = $entity->asArray();

        $data = [
            'id'  => $entity->id,
            'url' => url(RESTController::url($entity->getResource(), $entity->id)),
            ];

        if (isset($fields['parent_id'])) {
            $data['parent'] = $this->getRelation($entity->getResource(), $entity->parent_id);
            unset($fields['parent_id']);
        }

        if (isset($fields['user_id'])) {
            $data['user'] = $this->getRelation('users', $entity->user_id);
            unset($fields['user_id']);
        }

        foreach ($fields as $field => $value) {
            $name = $this->getFieldName($field);
            if (is_string($value)) {
                $value = trim($value);
            }

            $method = 'format' . Str::studly($field);
            $methodWithFields = 'format' . Str::studly($field) . 'WithFields';
            if (method_exists($this, $method)) {
                $data[$name] = $this->$method($value);
            } elseif (method_exists($this, $methodWithFields)) {
                $data[$name] = $this->$methodWithFields($value, $fields);
            } else {
                $data[$name] = $value;
            }
        }

        $data = $this->addMetadata($data, $entity);

        return $data;
    }

    /**
     * Method that can add any kind of additional metadata about the entity,
     * by overloading this method in an extended class.
     *
     * Must return the formatted data!
     *
     * @param  Array  $data   formatted data
     * @param  Entity $entity resource
     * @return Array
     */
    protected function addMetadata(array $data, Entity $entity)
    {
        // By default, noop
        return $data;
    }

    protected function getFieldName($field)
    {
        // can be overloaded to remap specific fields to different public names
        return $field;
    }

    protected function formatCreated($value)
    {
        return date(\DateTime::W3C, $value);
    }

    protected function formatUpdated($value)
    {
        return $value ? $this->formatCreated($value) : null;
    }

    /**
     * Format relations into url/id arrays
     * @param  string $resource resource name as used in urls
     * @param  int    $id       resource id
     * @return array
     */
    protected function getRelation($resource, $id)
    {
        return !$id ? null : [
            'id'  => intval($id),
            'url' => url(RESTController::url($resource, $id)),
        ];
    }
}
