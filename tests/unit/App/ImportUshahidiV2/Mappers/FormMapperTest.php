<?php

namespace Tests\Unit\App\ImportUshahidiV2\Mappers;

use Ushahidi\App\ImportUshahidiV2\Mappers\FormMapper;
use Ushahidi\Core\Entity\FOrm;
use Tests\TestCase;
use Mockery as M;
use Faker;

/**
 * @backupGlobals disabled
 * @preserveGlobalState disabled
 */
class FormMapperTest extends TestCase
{
    public function testMap()
    {
        $faker = Faker\Factory::create();
        $input = [
            'form_title' => 'Special form',
            'form_description' => 'A very very special form',
            'form_active' => '1',
        ];

        $mapper = new FormMapper();

        $form = $mapper(1, $input);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertEquals('Special form', $form->name);
        $this->assertEquals('A very very special form', $form->description);
        $this->assertEquals(false, $form->disabled);
    }

    public function testMapDisabled()
    {
        $faker = Faker\Factory::create();
        $input = [
            'form_title' => 'Special form',
            'form_description' => 'A very very special form',
            'form_active' => '0',
        ];

        $mapper = new FormMapper();

        $form = $mapper(1, $input);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertEquals('Special form', $form->name);
        $this->assertEquals('A very very special form', $form->description);
        $this->assertEquals(true, $form->disabled);
    }
}
