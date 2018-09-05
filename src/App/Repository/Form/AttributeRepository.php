<?php

/**
 * Ushahidi Form Attribute Repository
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Repository\Form;

use Illuminate\Support\Facades\App;
use Ohanzee\DB;
use Ohanzee\Database;
use Ushahidi\Core\Entity;
use Ushahidi\Core\SearchData;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormAttributeRepository as FormAttributeRepositoryContract;
use Ushahidi\Core\Entity\FormStageRepository as FormStageRepositoryContract;
use Ushahidi\Core\Entity\FormRepository as FormRepositoryContract;
use Ushahidi\Core\Traits\UserContext;
use Ushahidi\App\Repository\OhanzeeRepository;
use Ushahidi\App\Repository\JsonTranscodeRepository;
use Ushahidi\App\Repository\FormsTagsTrait;

use Ushahidi\Core\Tool\Permissions\InteractsWithFormPermissions;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class AttributeRepository extends OhanzeeRepository implements
    FormAttributeRepositoryContract
{
    use UserContext;
    // Checks if user is Admin

    use InteractsWithFormPermissions;

    protected $form_stage_repo;

    protected $form_repo;

    protected $form_id;

    // Use the JSON transcoder to encode properties
    use JsonTranscodeRepository;
    use FormsTagsTrait;

    /**
     * Construct
     * @param Database                              $db
     * @param FormStageRepository                   $form_stage_repo
     * @param FormRepository                   $form_repo
     */
    public function __construct(
        Database $db,
        FormStageRepositoryContract $form_stage_repo,
        FormRepositoryContract $form_repo
    ) {
        parent::__construct($db);

        $this->form_stage_repo = $form_stage_repo;
        $this->form_repo = $form_repo;
    }

    // JsonTranscodeRepository
    protected function getJsonProperties()
    {
        return ['options', 'config'];
    }

    protected function getFormId($form_stage_id)
    {
        $form_id = $this->form_stage_repo->getFormByStageId($form_stage_id);
        if ($form_id) {
            return $form_id;
        }
        return null;
    }

    // Override selectQuery to fetch attribute 'key' too
    protected function selectQuery(array $where = [], $form_id = null, $form_stage_id = null)
    {
        $query = parent::selectQuery($where);

        if (!$form_id && $form_stage_id) {
            $form_id = $this->getFormId();
        }

        // Restrict returned attributes based on User rights
        $user = $this->getUser();
        if (!$this->formPermissions->canUserEditForm($user, $form_id)) {
            $exclude_stages = $this->form_stage_repo->getHiddenStageIds($form_id);
            $exclude_stages ? $query->where('form_attributes.form_stage_id', 'NOT IN', $exclude_stages) : null;
        }

        return $query;
    }

    // CreateRepository
    public function create(Entity $entity)
    {
        $record = $entity->asArray();
        unset($record['form_id']);

        $uuid = Uuid::uuid4();
        $record['key'] = $uuid->toString();

        return $this->executeInsertAttribute($this->removeNullValues($record));
    }

    // Override SearchRepository
    public function setSearchParams(SearchData $search)
    {
        $form_id = null;
        if ($search->form_id) {
            $form_id = $search->form_id;
        }

        $this->search_query = $this->selectQuery([], $form_id);

        $sorting = $search->getSorting();

        if (!empty($sorting['orderby'])) {
            $this->search_query->order_by(
                $this->getTable() . '.' . $sorting['orderby'],
                isset($sorting['order']) ? $sorting['order'] : null
            );
        }

        if (!empty($sorting['offset'])) {
            $this->search_query->offset($sorting['offset']);
        }

        if (!empty($sorting['limit'])) {
            $this->search_query->limit($sorting['limit']);
        }

        // apply the unique conditions of the search
        $this->setSearchConditions($search);
    }

    // SearchRepository
    protected function setSearchConditions(SearchData $search)
    {
        $query = $this->search_query;

        foreach ([
                     'key', 'label', 'input', 'type'
                 ] as $key) {
            if (isset($search->$key)) {
                $query->where('form_attributes.' . $key, '=', $search->$key);
            }
        }

        if ($search->form_id) {
            $query
                ->join('form_stages', 'INNER')->on('form_stages.id', '=', 'form_attributes.form_stage_id')
                ->where('form_stages.form_id', '=', $search->form_id);
        }
    }

    // OhanzeeRepository
    protected function getTable()
    {
        return 'form_attributes';
    }

    // OhanzeeRepository
    public function getEntity(array $data = null)
    {
        return new FormAttribute($data);
    }

    // OhanzeeRepository
    public function getSearchFields()
    {
        return ['form_id', 'type', 'label', 'key', 'input'];
    }

    // FormAttributeRepository
    public function getByKey($key_value, $form_id = null, $include_no_form = false)
    {
        $query = $this->getQueryByField('key', $key_value, $form_id, $include_no_form, 1);
        $result = $query->execute($this->db);
        return $this->getEntity($result->current());
    }

    // FormAttributeRepository
    public function getAllByType($field_value, $form_id = null, $attribute_id = null)
    {
        $query = $this->getQueryByField('type', $field_value, $form_id, false, null);
        if ($attribute_id) {
            $query->where('form_attributes.id', '!=', $attribute_id);
        }
        return $query->execute($this->db);
    }

    // FormAttributeRepository
    private function getQueryByField($field, $field_value, $form_id = null, $include_no_form = false, $limit = 1)
    {
        $query = $this->selectQuery([], $form_id)
        ->select('form_attributes.*')
        ->join('form_stages', 'LEFT')
        ->on('form_stages.id', '=', 'form_attributes.form_stage_id')
        ->where('form_attributes.' . $field, '=', $field_value);
        if ($limit) {
            $query->limit($limit);
        }
        if ($form_id) {
            $query
            ->and_where_open()
            ->where('form_id', '=', $form_id);
            if ($include_no_form) {
                $query->or_where('form_id', 'IS', null);
            }
            $query->and_where_close();
        }
        return $query;
    }

    // FormAttributeRepository
    public function getAll()
    {
        $query = $this->selectQuery();

        $results = $query->execute($this->db);

        return $this->getCollection($results->as_array());
    }

    /**
     * Filter the form ids that are included in the attributes the user selected for a CSV
     * @param $include_attributes
     * @return null|array
     */
    public function getFormsByAttributes($include_attributes)
    {
        if (!empty($include_attributes)) {
            return array_column($this->selectQuery()
                ->resetSelect()
                ->select('form_stages.form_id')
                ->distinct(true)
                ->join('form_stages')
                ->on('form_stages.id', '=', 'form_attributes.form_stage_id')
                ->where('form_attributes.key', 'IN', $include_attributes)
                ->execute($this->db)
                ->as_array(), 'form_id');
        }
        return null;
    }

    // FormAttributeRepository
    public function getByForm($form_id)
    {
        $query = $this->selectQuery([
            'form_stages.form_id' => $form_id,
        ], $form_id)
            ->select('form_attributes.*')
            ->join('form_stages', 'INNER')
            ->on('form_stages.id', '=', 'form_attributes.form_stage_id');

        $results = $query->execute($this->db);

        return $this->getCollection($results->as_array());
    }

    /**
     * @param $include_attributes (optional)
     * @return array
     * Returns a list of attributes with the relevant fields.
     * This is mainly to be used in the post exporter where we need a consistent list of attributes
     * that does not directly depend on the rows we are fetching at the time but on the
     * list of form ids that match a specific query
     */
    public function getExportAttributes(array $include_attributes = null)
    {
        $sql = "SELECT DISTINCT form_attributes.*,
			form_stages.priority as form_stage_priority,
			form_stages.form_id as form_id, forms.name as form_name, forms.id as form_id " .
            "FROM form_attributes " .
            "INNER JOIN form_stages ON form_attributes.form_stage_id = form_stages.id " .
            "INNER JOIN forms ON form_stages.form_id = forms.id ";
        if (!empty($include_attributes)) {
            $sql .= " AND form_attributes.key IN :form_attributes ";
        }
        $sql .= "ORDER BY forms.id, form_stages.priority, form_attributes.priority ";
        $results = DB::query(Database::SELECT, $sql)
            ->bind(':form_attributes', $include_attributes)
            ->execute($this->db);
        $attributes = $results->as_array();
        $native = [
            [
                'label' => 'Post ID',
                'key' => 'id',
                'type' => 'integer',
                'input' => 'number',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 1
            ],
            [
                'label' => 'Survey',
                'key' => 'form_name',
                'type' => 'form_name',
                'input' => 'text',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 2
            ],
            [
                'label' => 'Post Status',
                'key' => 'status',
                'type' => 'string',
                'input' => 'string',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 3
            ],
            [
                'label' => 'Created (UTC)',
                'key' => 'created',
                'type' => 'datetime',
                'input' => 'native',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 4
            ],
            [
                'label' => 'Updated (UTC)',
                'key' => 'updated',
                'type' => 'datetime',
                'input' => 'native',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 5
            ],
            [
                'label' => 'Post Date (UTC)',
                'key' => 'post_date',
                'type' => 'datetime',
                'input' => 'native',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 6
            ],
            [
                'label' => 'Contact ID',
                'key' => 'contact_id',
                'type' => 'integer',
                'input' => 'number',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 7
            ],
            [
                'label' => 'Contact',
                'key' => 'contact',
                'type' => 'text',
                'input' => 'text',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'priority' => 8
            ],
            [
                'label' => 'Unstructured Description',
                'key' => 'description',
                'type' => 'description',
                'input' => 'text',
                'form_id' => 0,
                'form_stage_id' => 0,
                'form_stage_priority' => 0,
                'unstructured' => true,
                'priority' => 9
            ]
        ];

        return array_merge($native, $attributes);
    }

    /**
     * @param int $form_id
     * @return Entity|FormAttribute
     *
     * Selects the first attribute of the same stage AFTER $last_attribute_id
     * for the form $form_id. Will only work correctly for targeted surveys or other single stage surveys
     * @return FormAttribute entity
     */
    public function getNextByFormAttribute($last_attribute_id)
    {
        $current_attribute = $this->get($last_attribute_id);
        $next_attribute = DB::select($this->getTable() . '.*')
            ->from($this->getTable())
            ->where('form_stage_id', '=', $current_attribute->form_stage_id)
            ->where('priority', '>', $current_attribute->priority)
            ->where('form_attributes.type', 'not in', ['title', 'description'])
            ->order_by('form_attributes.priority', 'ASC')
            ->limit(1)
            ->execute($this->db);

        return $this->getEntity($next_attribute->current());
    }

    public function getFirstNonDefaultByForm($form_id)
    {
        $query = $this->selectQuery([
            'form_stages.form_id' => $form_id,
        ], $form_id)
            ->select('form_attributes.*')
            ->join('form_stages', 'INNER')
            ->on('form_stages.id', '=', 'form_attributes.form_stage_id')
            ->where('form_attributes.type', 'not in', ['title', 'description'])
            ->order_by('form_stages.priority', 'ASC')
            ->order_by('form_attributes.priority', 'ASC')
            ->limit(1);

        $results = $query->execute($this->db);

        return $this->getEntity($results->current());
    }

    // FormAttributeRepository
    public function getRequired($stage_id)
    {
        $form_id = $this->getFormId($stage_id);

        $query = $this->selectQuery([
            'form_attributes.form_stage_id' => $stage_id,
            'form_attributes.required' => true
        ], $form_id)
            ->select('form_attributes.*');

        $results = $query->execute($this->db);

        return $this->getCollection($results->as_array());
    }

    // FormAttributeRepository
    public function isKeyAvailable($key)
    {
        return $this->selectCount(compact('key')) === 0;
    }
}
