<?php

namespace Ushahidi\Modules\V5\Actions\Export\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\ExportJob;
use Ushahidi\Modules\V5\Requests\ExportJobRequest;
use Ushahidi\Core\Entity\ExportJob as ExportJobEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

class UpdateExportJobCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var ExportJobEntity
     */
    private $export_job_entity;

    public function __construct(
        int $id,
        ExportJobEntity $export_job_entity
    ) {
        $this->id = $id;
        $this->export_job_entity = $export_job_entity;
    }

    public static function fromRequest(int $id, ExportJobRequest $request, ExportJob $current_export_job): self
    {
        $user = Auth::user();
        if (self::hasPermissionToUpdateUser($user)) {
            $input['user_id'] = $request->input('user_id') ?? $current_export_job->user_id;
        } else {
            $input['user_id'] = $current_export_job->user_id;
        }
        $input['entity_type'] = $request->input('entity_type')?? $current_export_job->entity_type;
        $input['fields'] = $request->input('fields')?? $current_export_job->fields;
        $input['filters'] = $request->input('filters')?? $current_export_job->filters;
        $input['status'] = $request->input('status')?? $current_export_job->status;
        $input['url'] = $request->input('url')?? $current_export_job->url;
        $input['url_expiration'] = $request->input('url_expiration')?? $current_export_job->url_expiration;
        $input['status_details'] = $request->input('status_details')?? $current_export_job->status_details;
        $input['header_row'] = $request->input('header_row')?? $current_export_job->header_row;
        $input['hxl_meta_data_id'] = $request->input('hxl_meta_data_id')?? $current_export_job->hxl_meta_data_id;
        $input['include_hxl'] = $request->input('include_hxl')?? $current_export_job->include_hxl;
        $input['send_to_browser'] = $request->input('send_to_browser')?? $current_export_job->send_to_browser;
        $input['send_to_hdx'] = $request->input('send_to_hdx')?? $current_export_job->send_to_hdx;
        $input['hxl_heading_row'] = $request->input('hxl_heading_row')?? $current_export_job->hxl_heading_row;
        $input['total_rows'] = $request->input('total_rows')?? $current_export_job->total_rows;
        $input['total_batches'] = $request->input('total_batches')?? $current_export_job->total_batches;
        $input['created'] = strtotime($current_export_job->created);
        $input['updated'] = time();

        return new self($id, new ExportJobEntity($input));
    }
    private static function hasPermissionToUpdateUser($user)
    {
        if ($user->role === "admin") {
            return true;
        }
        return false;
    }

    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return ExportJobEntity
     */
    public function getExportJobEntity(): ExportJobEntity
    {
        return $this->export_job_entity;
    }
}
