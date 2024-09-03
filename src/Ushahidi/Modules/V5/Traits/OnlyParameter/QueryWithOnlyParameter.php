<?php

namespace Ushahidi\Modules\V5\Traits\OnlyParameter;

use Illuminate\Http\Request;

trait QueryWithOnlyParameter
{
     /**
     * @var array
     */
    private $fields;
    /**
     * @var array
     */
    private $hydrates;

    private $with_relationships;

    private $fields_for_relationships;


    public function getFields(): array
    {
        return $this->fields;
    }

    public function getHydrates(): array
    {
        return $this->hydrates;
    }

    public function getWithRelationship(): array
    {
        return $this->with_relationships;
    }

    public function getFieldsForRelationship(): array
    {
        return $this->fields_for_relationships;
    }


    /**
     *  get only fields from the request
     * @param Request $request
     * @param array $approved_fields
     */
    private function getOnlyValuesFromRequest(Request $request, array $allowed_fields, array $allowed_relations): void
    {
        $this->fields = $allowed_fields;
        $this->hydrates = array_keys($allowed_relations);

        if ($request->query('format') === 'minimal') {
            $this->fields = ['id', 'name', 'description', 'translations'];
            $this->hydrates = ['translations'];
        } elseif ($request->get('only')) {
            $only_values = explode(',', $request->get('only'));
            $this->fields = [];
            $this->hydrates = [];
            foreach ($only_values as $only_value) {
                if (in_array($only_value, $allowed_fields)) {
                    $this->fields[] = $only_value;
                } elseif (array_key_exists($only_value, $allowed_relations)) {
                    $this->hydrates[] = $only_value;
                }
            }
        }
    }
    private function getNeededRelations(array $allowed_relations)
    {
        $this->with_relationships = [];
        $this->fields_for_relationships = [];
        foreach ($this->hydrates as $hydrate) {
            if ($allowed_relations[$hydrate]) {
                $this->with_relationships = array_unique(
                    array_merge(
                        $this->with_relationships,
                        $allowed_relations[$hydrate]['relationships']
                    )
                );
                $this->fields_for_relationships = array_unique(
                    array_merge(
                        $this->fields_for_relationships,
                        $allowed_relations[$hydrate]['fields']
                    )
                );
            }
        }
    }
    public function addOnlyParameteresFromRequest(Request $request, array $allowed_fields, array $allowed_relations, array $required_fields): void
    {
        $this->getOnlyValuesFromRequest($request, $allowed_fields, $allowed_relations);
        $this->fields = array_unique(array_merge($this->fields, $required_fields));
        $this->getNeededRelations($allowed_relations);
    }
  
    public function addOnlyValues(array $fields, array $hydrates, array $allowed_relations, array $required_fields)
    {
        $this->fields = $fields;
        $this->hydrates = $hydrates;
        $this->fields = array_unique(array_merge($this->fields, $required_fields));
        $this->getNeededRelations($allowed_relations);
    }
}
