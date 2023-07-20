<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\Export\Queries\FetchExportJobByIdQuery;
use Ushahidi\Modules\V5\Actions\Export\Queries\FetchExportJobQuery;
use Ushahidi\Modules\V5\Http\Resources\Export\ExportJobResource;
use Ushahidi\Modules\V5\Http\Resources\Export\ExportJobCollection;
use Ushahidi\Modules\V5\Actions\Export\Commands\CreateExportJobCommand;
use Ushahidi\Modules\V5\Actions\Export\Commands\UpdateExportJobCommand;
use Ushahidi\Modules\V5\Actions\Export\Commands\DeleteExportJobCommand;
use Ushahidi\Modules\V5\Requests\ExportJobRequest;
use Ushahidi\Modules\V5\Models\ExportJob;
use Ushahidi\Core\Exception\NotFoundException;

class ExportJobController extends V5Controller
{


    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $export_job = $this->queryBus->handle(new FetchExportJobByIdQuery($id));
        $this->authorize('show', $export_job);
        return new ExportJobResource($export_job);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return ExportJobCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', ExportJob::class);
        $export_jobs = $this->queryBus->handle(FetchExportJobQuery::FromRequest($request));
        return new ExportJobCollection($export_jobs);
    } //end index()


    /**
     * Create new ExportJob.
     *
     * @param ExportJobRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(ExportJobRequest $request)
    {
        $command = CreateExportJobCommand::fromRequest($request);
        $new_export_job = new ExportJob($command->getExportJobEntity()->asArray());
        $this->authorize('store', $new_export_job);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  ExportJob.
     *
     * @param int id
     * @param ExportJobRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, ExportJobRequest $request)
    {
        $old_export_job = $this->queryBus->handle(new FetchExportJobByIdQuery($id));
        $command = UpdateExportJobCommand::fromRequest($id, $request, $old_export_job);
        $new_export_job = new ExportJob($command->getExportJobEntity()->asArray());
        $this->authorize('update', $new_export_job);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new ExportJob.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $export_job = $this->queryBus->handle(new FetchExportJobByIdQuery($id));
        } catch (NotFoundException $e) {
            $export_job = new ExportJob();
        }
        $this->authorize('delete', $export_job);
        $this->commandBus->handle(new DeleteExportJobCommand($id));
        return $this->deleteResponse($id);
    }// end delete
} //end class
