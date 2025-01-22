<?php

namespace Ushahidi\Modules\V5\Http\Controllers;

use Illuminate\Http\Request;
use Ushahidi\Modules\V5\Actions\CSV\Queries\FetchCSVByIdQuery;
use Ushahidi\Modules\V5\Actions\CSV\Queries\FetchCSVQuery;
use Ushahidi\Modules\V5\Http\Resources\CSV\CSVResource;
use Ushahidi\Modules\V5\Http\Resources\CSV\CSVCollection;
use Ushahidi\Modules\V5\Actions\CSV\Commands\CreateCSVCommand;
use Ushahidi\Modules\V5\Actions\CSV\Commands\UpdateCSVCommand;
use Ushahidi\Modules\V5\Actions\CSV\Commands\DeleteCSVCommand;
use Ushahidi\Modules\V5\Requests\CSVRequest;
use Ushahidi\Modules\V5\Models\CSV;
use Ushahidi\Core\Exception\NotFoundException;
use Ushahidi\Core\Concerns\Event;
use Ushahidi\Modules\V3\Listener\Import;

class CSVController extends V5Controller
{
   // Use Event trait to trigger events
    use Event;

    /**
     * Display the specified resource.
     *
     * @param integer $id
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(int $id)
    {
        $csv = $this->queryBus->handle(new FetchCSVByIdQuery($id));
        $this->authorize('show', $csv);
        return new CSVResource($csv);
    } //end show()



    /**
     * Display the specified resource.
     *
     * @return CSVCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('index', CSV::class);
        $csvs = $this->queryBus->handle(FetchCSVQuery::FromRequest($request));
        return new CSVCollection($csvs);
    } //end index()


    /**
     * Create new CSV.
     *
     * @param CSVRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(CSVRequest $request)
    {
        $command = CreateCSVCommand::fromRequest($request);
        $new_csv = new CSV($command->getCSVEntity()->asArray());
        $this->authorize('store', $new_csv);
        return $this->show($this->commandBus->handle($command));
    } //end store()

     /**
     * update  CSV.
     *
     * @param int id
     * @param CSVRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(int $id, CSVRequest $request)
    {
        $old_csv = $this->queryBus->handle(new FetchCSVByIdQuery($id));
        $command = UpdateCSVCommand::fromRequest($id, $request, $old_csv);
        $new_csv = new CSV($command->getCSVEntity()->asArray());
        $this->authorize('update', $new_csv);
        $this->commandBus->handle($command);
        return $this->show($id);
    }// end update

     /**
     * Create new CSV.
     *
     * @param int id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(int $id)
    {
        try {
            $csv = $this->queryBus->handle(new FetchCSVByIdQuery($id));
        } catch (NotFoundException $e) {
            $csv = new CSV();
        }
        $this->authorize('delete', $csv);
        $this->commandBus->handle(new DeleteCSVCommand($id));
        return $this->deleteResponse($id);
    }// end delete


    public function import(int $id)
    {
        // To Do deprecate calling V3 code
        /**
         * Step two of import.
         * Support all line endings without manually specifying it
         * (primarily added because of OS9 line endings which do not work by default )
         */
        ini_set('auto_detect_line_endings', 1);

        // Get payload from CSV repo
        $csv = service('repository.csv')->get($id);

        $fs = service('tool.filesystem');
        $reader = service('filereader.csv');
        $transformer = service('transformer.csv');
        // Read file
        $file = new \SplTempFileObject();
        $contents = $fs->read($csv->filename);
        $file->fwrite($contents);

        // Get records
        // @todo read up to a sensible offset and process the rest later
        $records = $reader->process($file);
        // Set map and fixed values for transformer
        $transformer->setColumnNames($csv->columns);
        $transformer->setMap($csv->maps_to);
        $transformer->setFixedValues($csv->fixed);

        $new_status = 'PENDING';
        $csv->setState([
            'status' => $new_status
        ]);

        service('repository.csv')->update($csv);
        $repo = service('repository.post');
        $this->setEmitter(new \League\Event\Emitter);
        $this->setEvent("ImportPosts");
        $this->setListener(new Import());
        $this->emit(
            $this->event,
            $records,
            $csv,
            $transformer,
            $repo,
            $this
        );


        return response()->json(['result' => ['import' => $new_status]]);
    }

    public function verify($entity)
    {
        // ... verify that the entity can be created by the current user
       // $this->verifyCreateAuth($entity);

        // ... verify that the entity is in a valid state
       // $this->verifyValid($entity);
    }
} //end class
