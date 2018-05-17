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
    protected $repo;
    protected $user_repo;
    protected $license_repo;
    protected $export_job_repo;
    protected $default_error_source = 'hxl_metadata';

    public function __construct(
        HXLMetadataRepository $repo,
        UserRepository $user_repo,
        HXLLicenseRepository $license_repo,
        ExportJobRepository $export_job_repo
    ) {
        $this->repo = $repo;
        $this->user_repo = $user_repo;
        $this->license_repo = $license_repo;
        $this->export_job_repo = $export_job_repo;
    }
    /**
     * @return array|\Ushahidi\Core\Tool\ArrayValidation
     */
    protected function getRules()
    {
        return array_merge([
            'private' => [
                ['not_empty']
            ],
            'dataset_title' => [
                ['not_empty'],
                ['min_length', [':value', 2]],
                ['max_length', [':value', 255]],
                ['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ],
            'organisation' => [
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
            ],
            'maintainer' => [
                ['not_empty'],
                ['min_length', [':value', 2]],
                ['max_length', [':value', 255]],
                ['regex', [':value', Validator::REGEX_STANDARD_TEXT]], // alpha, number, punctuation, space
            ]
        ], $this->getForeignKeyRules());
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
            'export_job_id' => [
                [[$this->export_job_repo, 'exists'], [':value']],
                [[$this, 'notExists'], [':value', ':validation']],
            ],
            'user_id' => [
                [[$this->user_repo, 'exists'], [':value']],
            ]
        ];
    }

    public function notExists($value, $validation) {
        if (!$value) {
            return true;
        }
        $entity = $this->repo->getEntityByJobId($value);
        if ($entity->getId()) {
            $validation->error('export_job_id', 'uniqueMetadataByJob');
        }
    }
}
