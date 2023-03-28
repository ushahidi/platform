<?php

namespace Ushahidi\Modules\V2\Mappers;

use Ushahidi\Modules\V2\Import;
use Illuminate\Support\Facades\Log;
use Ushahidi\Core\Ohanzee\Entities\Form;
use Ushahidi\Modules\V2\Contracts\Mapper;

class FormMapper implements Mapper
{
    public function __invoke(Import $import, array $input) : array
    {
        $form = new Form([
            'name' => $input['form_title'],
            'description' => $input['form_description'],
            'disabled' => !$input['form_active'],
            'require_approval' => true,
            'everyone_can_create' => true,
        ]);

        Log::debug(
            "Mapping v2 form {input} to {form}",
            ['input' => $input, 'form' => $form]
        );

        return [
            'result' => $form,
        ];
    }
}
