<?php

namespace Ushahidi\Modules\V5\Actions\CSV\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\CSV;
use Ushahidi\Modules\V5\Requests\CSVRequest;
use Ushahidi\Core\Entity\CSV as CSVEntity;
use Illuminate\Support\Facades\Auth;
use Ushahidi\Modules\V5\Helpers\ParameterUtilities;

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

        $input['columns'] = $request->input('columns') ?? $current_csv->columns;
        $input['maps_to'] = $request->input('maps_to') ?? $current_csv->maps_to;
        $input['fixed'] = $request->input('fixed') ?? $current_csv->fixed;
        $input['filename'] = $request->input('filename') ?? $current_csv->filename;
        $input['size'] = $request->input('size') ?? $current_csv->size;
        $input['mime'] = $request->input('mime') ?? $current_csv->mime;
        $input['status'] = $request->input('status') ?? $current_csv->status;
        $input['errors'] = $request->input('errors') ?? $current_csv->errors;
        $input['processed'] = $request->input('processed') ?? $current_csv->processed;
        $input['collection_id'] = $request->input('collection_id') ?? $current_csv->collection_id;
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
