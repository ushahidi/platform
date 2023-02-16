<?php

namespace Ushahidi\Modules\V5\Actions;

use App\Bus\Query\AbstractQueryHandler;

abstract class V5QueryHandler extends AbstractQueryHandler
{

    /**
     * get the approved only fields from the request
     * @param ?string $only
     * @param array $approved_fields
     * @param array $required_fields
     * @return array
     */
    public function getSelectFields(?string $format, ?string $only, array $approved_fields = [], array $required_fields = [])
    {
        $only_fields = [];
        if ($format === 'minimal') {
            $only_fields = ['id', 'name', 'description', 'translations'];
        } elseif ($only) {
            $only_fields = explode(',', $only);
        } else {
            $only_fields = $approved_fields;
        }
        if (count($only_fields) > 0) {
            $only_fields = array_filter($only_fields, function ($f) use ($approved_fields) {
                return in_array($f, $approved_fields);
            });
        }
        return array_merge($required_fields, $only_fields);
    }



    /**
     * get the approved hedrate relationships
     *
     * @param  array  $relationships
     * @param ?string $hydrate
     * @return array
     */
    public function getHydrateRelationshpis(array $relationships, ?string $hydrate): array
    {
        if ($hydrate) {
            $required_relationships = explode(',', $hydrate);
        } else {
            $required_relationships = $relationships;
        }
        return array_filter($required_relationships, function ($o) use ($relationships) {
            return in_array($o, $relationships);
        });
    }
}
