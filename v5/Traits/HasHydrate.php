<?php

namespace v5\Traits;

use Illuminate\Http\Request;


trait HasHydrate
{
    /**
     * get the  hedrate relationships from the request
     *
     * @param Request $request
     * @param  array  $relationships
     * @return array
     */

    private static function toHydrate(Request $request, array $relationships)
    {
        $to_hydrate = $relationships;

        if ($request->has('hydrate') && !$request->get('hydrate')) {
            $to_hydrate = [];
        }
        if ($request->get('hydrate')) {

            $to_hydrate = explode(',', $request->get('hydrate'));
        }
        return $to_hydrate;
    }

    /**
     * get the approved hedrate relationships
     *
     * @param  array  $relationships
     * @param Request $request
     * @return array
     */
    public function getHydrate(array $relationships, Request $request): array
    {
        $only_original = self::toHydrate($request, $relationships);
        return array_filter($only_original, function ($o) use ($relationships) {
            return in_array($o, $relationships);
        });
    }
}
