<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Illuminate\Support\Facades\Log;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Form;
use Ushahidi\App\ImportUshahidiV2\Import;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;

class FormMapper implements Mapper
{
    public function __invoke(Import $import, array $input) : array
    {
        $form = new Form([
            'name' => $input['form_title'],
            'description' => $input['form_description'],
            'disabled' => !$input['form_active'],
            'name' => $input['form_title'],
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
