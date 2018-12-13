<?php

namespace Ushahidi\App\ImportUshahidiV2\Mappers;

use Ushahidi\Core\Entity;
use Ushahidi\Core\Entity\Form;
use Ushahidi\App\ImportUshahidiV2\Contracts\Mapper;

class FormMapper implements Mapper
{
    public function __invoke(int $importId, array $input) : Entity
    {
        return new Form([
            'name' => $input['form_title'],
            'description' => $input['form_description'],
            'disabled' => $input['form_active'],
            'name' => $input['form_title'],
            'require_approval' => true,
            'everyone_can_create' => true,
        ]);
    }
}
