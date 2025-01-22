<?php

/**
 * Ushahidi Platform Factory for Formatters
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Factory;

class FormatterFactory
{
    // Array of formatters, mapped by resource:
    //
    //     $map = [
    //         'widgets' => $di->lazyNew('Namespace\To\WidgetFormatter'),
    //         ...
    //     ]
    //
    // Resource names correspond with entity types.
    protected $map = [];

    // Array of flags for actions that are collections. Typically this is only
    // the "search" action:
    //
    //     $collections = ['search' => true];
    //
    // Each of the entities in a collection will be formatted separately.
    protected $collections = [];

    // Closure used to wrap the creation of a new collection.
    protected $collection_factory;

    /**
     * @param  array   $map
     * @param  array   $collections
     * @param  \Closure $factory
     */
    public function __construct(
        array $map,
        array $collections,
        $factory
    ) {
        $this->map         = $map;
        $this->collections = $collections;

        $this->collection_factory = $factory;
    }

    /**
     * Checks if the given action should be returned as a collection.
     *
     * @param  string $action
     * @return boolean
     */
    public function isCollection($action)
    {
        return !empty($this->collections[$action]);
    }

    /**
     * Gets a formatter from the map by resource and action.
     * If the action is a collection action, the formatter will be returned
     * as a `Collection`.
     *
     * @param  string $resource
     * @param  string $action
     * @return \Ushahidi\Contracts\Formatter
     */
    public function get($resource, $action)
    {
        if (empty($this->map[$resource])) {
            throw new \Exception(sprintf('Formatter for %s is not defined', $resource));
        }
        $factory = $this->map[$resource];

        if ($this->isCollection($action)) {
            $collection = $this->collection_factory;
            $formatter = $collection($factory());
        } else {
            $formatter = $factory();
        }

        return $formatter;
    }
}
