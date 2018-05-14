<?php

/**
 * Ushahidi Export Validator
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\ExportJob;

use Ushahidi\App\Facades\Features;
use Ushahidi\Core\Tool\Validator;

class Update extends Validator
{
    protected $default_error_source = 'export';

    protected function getRules()
    {

        return array_merge([
            'id' => [
                ['numeric'],
            ],
            'entity_type' => [
                ['in_array', [':value', ['post']]],
            ],
        ], $this->getHxlRules());
    }

    /**
     * @return array
     * Return hxl rules array if Feature hxl is enabled, empty array otherwise
     */
    private function getHxlRules()
    {
        $hxl_rules = [];
        if (Features::isEnabled('hxl')) {
            $hxl_rules = [
                'send_to_hdx' => [
                    [[$this, 'sendToBrowserIsFalse'], [':validation', ':value', ':fulldata']],
                ],
                'send_to_browser' => [
                    [[$this, 'sendToHDXIsFalse'], [':validation', ':value', ':fulldata']],
                ],
                'include_hxl' => [
                    [[$this, 'trueIfSendToHDXIsTrue'], [':validation', ':value', ':fulldata']],
                ]
            ];
        }
        return $hxl_rules;
    }

    /**
     * @param $validation
     * @param $value
     * @param $fullData
     * @return bool
     */
    public function trueIfSendToHDXIsTrue($validation, $value, $fullData)
    {
        if ($fullData['send_to_hdx'] === true && $value === false) {
            $validation->error('include_hxl', 'includeHXLShouldBeTrue');
        }
        return true;
    }

    /**
     * @param $validation
     * @param $value
     * @param $fullData
     * @return bool
     */
    public function sendToBrowserIsFalse($validation, $value, $fullData)
    {
        if (!$this->isOppositeBool($value, $fullData['send_to_browser'])) {
            $validation->error('send_to_hdx', 'sendToHDXShouldBeTrue');
        }
        return true;
    }

    /**
     * @param $validation
     * @param $value
     * @param $fullData
     * @return bool
     */
    public function sendToHDXIsFalse($validation, $value, $fullData)
    {
        if (!$this->isOppositeBool($value, $fullData['send_to_hdx'])) {
            $validation->error('send_to_browser', 'sendToBrowserShouldBeTrue');
        }
        return true;
    }

    /**
     * @param $first
     * @param $second
     * @return bool
     */
    private function isOppositeBool($first, $second)
    {
        if ($this->isBool($first) && $this->isBool($second)) {
            return $first !== $second;
        }
        return false;
    }

    /**
     * @param $value
     * @return bool
     */
    private function isBool($value)
    {
        return $value !== null && is_bool($value);
    }
}
