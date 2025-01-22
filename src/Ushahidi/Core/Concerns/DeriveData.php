<?php

/**
 * Ushahidi Data Deriver Trait
 *
 * Gives objects new `deriver($data)` and `getDeriver()` methods,
 * which can be used to ensure data type consistency.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2022 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

trait DeriveData
{
    protected function derive(array $data)
    {
        foreach ($this->getDerived() as $key => $possible) {
            if (!array_key_exists($key, $data)) {
                if (!is_array($possible)) {
                    // Always possible to derive data from more than one source.
                    $possible = [$possible];
                }
                foreach ($possible as $from) {
                    if ($from instanceof \Closure) {
                        // Callable function which returns the derived value
                        //
                        // function ($data) {
                        //     return $data['foo'] . '-' . uniqid();
                        // }
                        //
                        // Its important we check for Closure here rather than
                        // using is_callable which allows global function as well
                        if ($derivedValue = $from($data)) {
                            $data[$key] = $derivedValue;
                        }
                    } elseif (array_key_exists($from, $data)) {
                        // Derived value comes from a simple alias:
                        //
                        //     $data['foo'] = $data['bar'];
                        //
                        $data[$key] = $data[$from];
                    } elseif (strpos($from, '.')) {
                        // Derived value comes from a complex alias:
                        //
                        //     $data['foo'] = $data['relation']['bar'];
                        //
                        list($arr, $from) = explode('.', $from, 2);
                        if (array_key_exists($arr, $data)
                            && is_array($data[$arr])
                            && array_key_exists($from, $data[$arr])
                        ) {
                            $data[$key] = $data[$arr][$from];
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Return the list of attribute deriver from the data given
     *
     *     return [
     *         'user_id'  => ['user', 'user.id'],
     *         'post_id'  => ['post', 'contact']'
     *         'media_id' => [
     *               function($data) {
     *                  return 'photo-' + $data['photo_id']
     *               }
     *           ],
     *     ];
     *
     * @return array
     */
    abstract protected function getDerived();
}
