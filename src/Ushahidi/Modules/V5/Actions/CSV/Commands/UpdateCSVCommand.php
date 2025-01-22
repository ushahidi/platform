<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\CSV;
use Ushahidi\Modules\V5\Requests\CSVRequest;
use Ushahidi\Core\Entity\CSV as CSVEntity;

class UpdateCSVCommand implements Command
{

    /**
     * @var int
     */
    private $id;
    /**
     * @var CSVEntity
     */
    private $csv_entity;

    public function __construct(
        int $id,
        CSVEntity $csv_entity
    ) {
        $this->id = $id;
        $this->csv_entity = $csv_entity;
    }

    public static function fromRequest(int $id, CSVRequest $request, CSV $current_csv): self
    {

        $input['columns'] = $request->has('columns') ? $request->input('columns') : $current_csv->columns;
        $input['maps_to'] = $request->has('maps_to') ? $request->input('maps_to') : $current_csv->maps_to;
        $input['fixed'] = $request->has('fixed') ? $request->input('fixed') : $current_csv->fixed;
        $input['filename'] = $request->has('filename') ? $request->input('filename') : $current_csv->filename;
        $input['size'] = $request->has('size') ? $request->input('size') : $current_csv->size;
        $input['mime'] = $request->has('mime') ? $request->input('mime') : $current_csv->mime;
        $input['status'] = $request->has('status') ? $request->input('status') : $current_csv->status;
        $input['errors'] = $request->has('errors') ? $request->input('errors') : $current_csv->errors;
        $input['processed'] = $request->has('processed') ? $request->input('processed') : $current_csv->processed;
        $input['collection_id'] = $request->has('collection_id')
        ? $request->input('collection_id') : $current_csv->collection_id;
        $input['created'] = strtotime($current_csv->created);
        $input['updated'] = time();

        return new self($id, new CSVEntity($input));
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
     * @return CSVEntity
     */
    public function getCSVEntity(): CSVEntity
    {
        return $this->csv_entity;
    }
}
