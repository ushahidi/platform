<?php

namespace v5\Traits;

use Illuminate\Http\Request;

trait HasOnlyParameters
{
    /**
     *  get only fields from the request
     * @param Request $request
     * @param array $approved_fields
     * @return array
     */
    public static function onlyOriginal(Request $request, array $approved_fields): array
    {
        $only_original = $approved_fields;
        if ($request->query('format') === 'minimal') {
            $only_original = ['id', 'name', 'description', 'translations'];
        } elseif ($request->get('only')) {
            $only_original = explode(',', $request->get('only'));
        }
        return $only_original;
    }

    /**
     * get the approved only fields
     * @param Request $request
     * @param array $approved_fields
     * @param array $required_fields
     * @return array
     */
    public static function includeFields(Request $request, array $approved_fields = [], array $required_fields = [])
    {
        $only_fields = $approved_fields;
        if ($request->has('only') && !$request->get('only')) {
            return [];
        }
        $only_original = self::onlyOriginal($request, $approved_fields);
        if (count($only_original) > 0) {
            $only_fields = array_filter($only_original, function ($f) use ($approved_fields) {
                return in_array($f, $approved_fields);
            });
        }
        return array_merge($required_fields, $only_fields);
    }
}
