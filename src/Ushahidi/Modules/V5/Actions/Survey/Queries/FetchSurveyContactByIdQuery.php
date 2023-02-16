<?php

namespace Ushahidi\Modules\V5\Actions\Survey\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Models\Survey;

class FetchSurveyByIdQuery implements Query
{


    /**
     * int
     */
    private $id;
    private $format;
    private $only_fields;
    private $hydrate;

    public function __construct(int $id = 0, ?string $format = null, ?string $only_fields = null, ?string $hydrate = null)
    {

        $this->id = $id;
        $this->format = $format;
        $this->only_fields = $only_fields;
        $this->hydrate = $hydrate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFormat()
    {
        return $this->format;
    }
    public function getOnlyFields()
    {
        return $this->only_fields;
    }

    public function getHydrate()
    {
        return $this->hydrate;
    }
}
