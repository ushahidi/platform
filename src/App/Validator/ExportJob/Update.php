<?php

/**
 * Ushahidi Export Validator
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\ExportJob;

use Ushahidi\App\Facades\Features;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Entity\HXL\HXLMetadataRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;

class Update extends Validator
{
    protected $default_error_source = 'export';
    protected $repo;
    protected $user_repo;
    protected $hxl_meta_data_repo;

    public function __construct(
        ExportJobRepository $repo,
        UserRepository $user_repo,
        HXLMetadataRepository $hxl_meta_data_repo
    ) {
        $this->repo = $repo;
        $this->user_repo = $user_repo;
        $this->hxl_meta_data_repo = $hxl_meta_data_repo;
    }

    protected function getRules()
    {

        return array_merge(
            [
                'id' => [
                    ['numeric'],
                ],
                'entity_type' => [
                    ['in_array', [':value', ['post']]],
                ],
                'hxl_meta_data_id' => [
                    ['in_array', [':value', ['post']]],
                ],
            ],
            $this->getHxlRules(),
            $this->getForeignKeyRules()
        );
    }

    /**
     * Get rules for references to other tables
     * @return array
     */
    private function getForeignKeyRules()
    {
        return [
            'hxl_meta_data_id' => [
                [[$this->hxl_meta_data_repo, 'exists'], [':value']],
            ],
            'user_id' => [
                [[$this->user_repo, 'exists'], [':value']],
            ]
        ];
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
                    [[$this, 'sendToBrowserIsFalse'], [':value', ':fulldata', ':validation']],
                ],
                'send_to_browser' => [
                    [[$this, 'sendToHDXIsFalse'], [':value', ':fulldata', ':validation']],
                ],
                'include_hxl' => [
                    [[$this, 'trueIfSendToHDXIsTrue'], [':value', ':fulldata', ':validation']],
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
    public function trueIfSendToHDXIsTrue($value, $fullData, $validation)
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
    public function sendToBrowserIsFalse($value, $fullData, $validation)
    {
        if (!$this->isOppositeBool($fullData['send_to_hdx'], $fullData['send_to_browser'])) {
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
    public function sendToHDXIsFalse($value, $fullData, $validation)
    {
        if (!$this->isOppositeBool($fullData['send_to_browser'], $fullData['send_to_hdx'])) {
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
