<?php

/**
 * Ushahidi Set Validator
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\HXL\Metadata;

use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Entity\HXL\HXLLicenseRepository;
use Ushahidi\Core\Entity\HXL\HXLMetadataRepository;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;

class Create extends Validator
{
    protected $default_error_source = 'hxl_metadata';

    protected $repo;
    protected $user_repo;
    protected $license_repo;

    public function __construct(
        HXLMetadataRepository $repo,
        UserRepository $user_repo,
        HXLLicenseRepository $license_repo
    ) {
        $this->repo = $repo;
        $this->user_repo = $user_repo;
        $this->license_repo = $license_repo;
    }
    /**
     * @return array|\Ushahidi\Core\Tool\ArrayValidation
     */
    protected function getRules()
    {
        return array_merge([
            'private' => [
                [[$this, 'notEmptyBool'], [':value', ':validation']],
            ],
            'dataset_title' => [
                ['not_empty'],
                ['min_length', [':value', 2]],
                ['max_length', [':value', 255]],
                ['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ],
            'organisation_id' => [
                ['not_empty'],
                ['min_length', [':value', 1]],
                ['max_length', [':value', 255]],
                ['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ],
            'organisation_name' => [
                ['not_empty'],
                ['min_length', [':value', 1]],
                ['max_length', [':value', 255]],
                ['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ],
            'source' => [
                ['not_empty'],
                ['min_length', [':value', 2]],
                ['max_length', [':value', 255]],
                ['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ]
        ], $this->getForeignKeyRules());
    }

    public function notEmptyBool($value, $validation)
    {
        if ($value === null) {
            $validation->error('private', 'privateShouldNotBeEmpty');
        }
        return true;
    }
    /**
     * Get rules for references to other tables
     * @return array
     */
    private function getForeignKeyRules()
    {
        return [
            'license_id' => [
                [[$this->license_repo, 'exists'], [':value']],
            ],
            'user_id' => [
                [[$this->user_repo, 'exists'], [':value']],
            ]
        ];
    }
}
