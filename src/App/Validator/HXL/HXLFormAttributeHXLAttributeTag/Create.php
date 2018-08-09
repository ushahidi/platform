<?php
/**
 * Ushahidi Set Validator
 *
 * @author    Ushahidi Team <team@ushahidi.com>
 * @package   Ushahidi\Application
 * @copyright 2014 Ushahidi
 * @license   https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Validator\HXL\HXLFormAttributeHXLAttributeTag;

use Ushahidi\App\Repository\HXL\HXLAttributeRepository;
use Ushahidi\App\Repository\HXL\HXLFormAttributeHXLAttributeTagRepository;
use Ushahidi\App\Repository\HXL\HXLTagRepository;
use Ushahidi\Core\Entity\ExportJobRepository;
use Ushahidi\Core\Entity\FormAttribute;
use Ushahidi\Core\Entity\FormAttributeRepository;
use Ushahidi\Core\Entity\HXL\HXLAttribute;
use Ushahidi\Core\Entity\HXL\HXLFormAttributeHXLAttributeTag;
use Ushahidi\Core\Entity\HXL\HXLTag;
use Ushahidi\Core\Entity\UserRepository;
use Ushahidi\Core\Tool\Validator;

class Create extends Validator
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
