<?php

/**
 * Ushahidi Platform Date Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Tool;

use Ushahidi\Contracts\Repository\EntityGet;

class Date
{
    protected $repo;

    public function __construct(EntityGet $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get the configured date format.
     *
     * Format will be PHP native, see http://php.net/date
     *
     * @return string
     */
    public function getDateFormat()
    {
        $config = $this->repo->get('site');
        return $config->date_format;
    }

    /**
     * Converts a date string to a UNIX timestamp. If no format is given,
     * the configured date format will be used.
     *
     * @param  string $time date/time string
     * @param  string $format non-default format
     * @return integer
     */
    public function getTimestampFromString($time, $format = null)
    {
        if (!$format) {
            $format = $this->getDateFormat();
        }

        $dt = \DateTime::createFromFormat($format, $time);

        return $dt->getTimestamp();
    }

    /**
     * Adds a timestamp to every row in a set of results. Each row in the results
     * is expected to be an array. If $add is set to the same value as $key,
     * the date will be replaced with the timestamp.
     * @param  array  $results [arr, arr, ...]
     * @param  string $key that contains the date string
     * @param  string $add the new key for the timestamp
     * @param  string $format non-default format
     * @return array
     */
    public function addTimestampToResults(array $results, $key = 'date', $add = 'ts', $format = null)
    {
        if (!$format) {
            $format = $this->getDateFormat();
        }

        foreach ($results as $idx => $row) {
            // Insert (or replace) the timestamp into the result set
            $results[$idx][$add] = $this->getTimestampFromString($row[$key], $format);
        }

        return $results;
    }
}
