<?php

/**
 * Ushahidi Form Stage
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Ohanzee\Entities;

use Ushahidi\Core\StaticEntity;
use Ushahidi\Core\Entity\FormStage as EntityFormStage;

class FormStage extends StaticEntity implements EntityFormStage
{
    const DEFAULT_TYPE = 'task';
    const DEFAULT_PRIORITY = 99;
    const DEFAULT_REQUIRED = 0;
    const DEFAULT_SHOW_WHEN_PUBLISHED = 1;
    const DEFAULT_TASK_IS_INTERNAL_ONLY = 1;

    protected $id;
    protected $form_id;
    protected $label;
    protected $priority;
    protected $icon;
    protected $type;
    protected $required;
    protected $show_when_published;
    protected $description;
    protected $task_is_internal_only;

    // DataTransformer
    protected function getDefinition()
    {
        return [
            'id'       => 'int',
            'description' => 'string',
            'show_when_published' => 'boolean',
            'type'     => 'string',
            'form_id'  => 'int',
            'label'    => 'string',
            'priority' => 'int',
            'icon'     => 'string',
            'required' => 'boolean',
            'task_is_internal_only' => 'boolean'
        ];
    }

    // Entity
    public function getResource()
    {
        return 'form_stages';
    }

    public static function buildEntity(array $input, $action = "create", array $old_Values = null): FormStage
    {

        if ($action === "update") {
            return new FormStage([
                "id" => $old_Values['id'],
                "form_id" => isset($input["survey_id"]) ? $input["survey_id"] : $old_Values['form_id'],
                "description" => isset($input["description"]) ? $input["description"] : $old_Values['description'],
                "label" => isset($input["label"]) ? $input["label"] : $old_Values['label'],
                "icon" => isset($input["icon"]) ? $input["icon"] : null,
                "type" => isset($input["type"]) ? $input["type"] : $old_Values['type'],
                "priority" => isset($input["priority"]) ? $input["priority"] : $old_Values['priority'],
                "required" => isset($input["required"]) ? $input["required"] : $old_Values['required'],

                "show_when_published" => isset($input["show_when_published"])
                ? $input["show_when_published"]
                : $old_Values['show_when_published'],

                "task_is_internal_only" => isset($input["task_is_internal_only"])
                ? $input["task_is_internal_only"]
                : $old_Values['task_is_internal_only'],
            ]);
        }
        return new FormStage([
            "form_id" => $input["survey_id"],
            "description" => isset($input["description"]) ? $input["description"] : null,
            "label" => isset($input["label"]) ? $input["label"] : null,
            "icon" => isset($input["icon"]) ? $input["icon"] : null,
            "type" => isset($input["type"]) ? $input["type"] : self::DEFAULT_TYPE,
            "priority" => isset($input["priority"]) ? $input["priority"] : self::DEFAULT_PRIORITY,
            "required" => isset($input["required"]) ? $input["required"] : self::DEFAULT_REQUIRED,

            "show_when_published" => isset($input["show_when_published"])
            ? $input["show_when_published"]
            : self::DEFAULT_SHOW_WHEN_PUBLISHED,

            "task_is_internal_only" => isset($input["task_is_internal_only"])
            ? $input["task_is_internal_only"]
            : self::DEFAULT_TASK_IS_INTERNAL_ONLY,
        ]);
    }
}
