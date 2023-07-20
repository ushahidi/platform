<?php

namespace Ushahidi\Modules\V5\Actions\Export\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\ExportJob;
use Ushahidi\Modules\V5\Requests\ExportJobRequest;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Core\Entity\ExportJob as ExportJobEntity;
use Ushahidi\Modules\V5\Models\Stage;

class CreateExportJobCommand implements Command
{
    /**
     * @var ExportJobEntity
     */
    private $export_job_entity;


    

    public function __construct(ExportJobEntity $export_job_entity)
    {
        $this->export_job_entity = $export_job_entity;
    }

    public static function fromRequest(ExportJobRequest $request): self
    {

        $user = Auth::user();
        $input['user_id'] = $request->input('user_id') ?? ($user ? $user->id : null);
        $input['entity_type'] = $request->input('entity_type');
        $input['fields'] = $request->input('fields');
        $input['filters'] = $request->input('filters');
        $input['status'] = $request->input('status');
        $input['url'] = $request->input('url');
        $input['url_expiration'] = $request->input('url_expiration');
        $input['status_details'] = $request->input('status_details');
        $input['header_row'] = $request->input('header_row');
        $input['hxl_meta_data_id'] = $request->input('hxl_meta_data_id');
        $input['include_hxl'] = $request->input('include_hxl')?? ExportJobEntity::DEFAULT_INCLUDE_HXL;
        $input['send_to_browser'] = $request->input('send_to_browser')?? ExportJobEntity::DEFAULT_SEND_TO_BROWSER;
        $input['send_to_hdx'] = $request->input('send_to_hdx')?? ExportJobEntity::DEFAULT_SEND_TO_HDX;
        $input['hxl_heading_row'] = $request->input('hxl_heading_row');
        $input['total_rows'] = $request->input('total_rows');
        $input['total_batches'] = $request->input('total_batches');
        $input['created'] = time();
        $input['updated'] = time();

        return new self(new ExportJobEntity($input));
    }

    /**
     * @return ExportJobEntity
     */
    public function getExportJobEntity(): ExportJobEntity
    {
        return $this->export_job_entity;
    }
}
