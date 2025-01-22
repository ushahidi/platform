<?php
/**
 * Ushahidi Set Validator
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Validator\HXL\HXLFormAttributeHXLAttributeTag;

use Ushahidi\Modules\V3\Validator\LegacyValidator;
use Ushahidi\Contracts\Repository\Entity\HXLTagRepository;
use Ushahidi\Contracts\Repository\Entity\ExportJobRepository;
use Ushahidi\Contracts\Repository\Entity\HXLAttributeRepository;
use Ushahidi\Contracts\Repository\Entity\FormAttributeRepository;
use Ushahidi\Contracts\Repository\Entity\HXLFormAttributeHXLAttributeTagRepository;

class Create extends LegacyValidator
{
    protected $repo;
    protected $hxl_attribute_repo;
    protected $hxl_tag_repo;
    protected $form_attribute_repo;
    protected $export_job_repo;

    public function __construct(
        HXLFormAttributeHXLAttributeTagRepository $repo,
        HXLTagRepository $hxl_tag_repo,
        HXLAttributeRepository $hxl_attribute_repo,
        FormAttributeRepository $form_attribute_repo,
        ExportJobRepository $export_job_repo
    ) {
        $this->repo = $repo;
        $this->hxl_attribute_repo = $hxl_attribute_repo;
        $this->hxl_tag_repo = $hxl_tag_repo;
        $this->form_attribute_repo = $form_attribute_repo;
        $this->export_job_repo = $export_job_repo;
    }
    /**
     * @return array|\Ushahidi\Core\Tool\ArrayValidation
     */
    protected function getRules()
    {
        return [
            'hxl_attribute_id' => [
                ['numeric'],
                [[$this->hxl_attribute_repo, 'exists'], [':value']],
            ],
            'hxl_tag_id' => [
                ['numeric'],
                [[$this->hxl_tag_repo, 'exists'], [':value']],
            ],
            'form_attribute_id' => [
                ['numeric'],
                [[$this->form_attribute_repo, 'exists'], [':value']],
            ],
            'export_job_id' => [
                ['numeric'],
                [[$this->export_job_repo, 'exists'], [':value']],
            ],
        ];
    }
}
