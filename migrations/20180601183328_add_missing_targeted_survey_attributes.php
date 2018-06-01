<?php

use Phinx\Migration\AbstractMigration;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class AddMissingTargetedSurveyAttributes extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function up()
    {

        $pdo = $this->getAdapter()->getConnection();
        // Get existing user info
        $rows = $this->fetchAll(
            "SELECT 
                form_stages.id 
            FROM 
                form_stages 
            INNER JOIN 
                forms 
            ON 
                forms.id = form_stages.form_id 
            WHERE 
                forms.targeted_survey=1"
        );

        $already_set = $this->fetchAll(
            "SELECT 
                form_stage_id 
            FROM 
                form_attributes 
            WHERE 
                type='title'
            OR
                type='description'"
        );

        $form_stages_to_ignore = [];
        foreach ($already_set as $form_stage) {
            if (!in_array($form_stage['form_stage_id'], $form_stages_to_ignore)) {
                array_push($form_stages_to_ignore, $form_stage['form_stage_id']);
            }
        }

        $insert = $pdo->prepare(
            "INSERT into
                form_attributes
                (`label`, `type`, `required`, `priority`, `cardinality`, `input`, `key`, `form_stage_id`)
            VALUES
                ('Title', 'title', 1, 0, 0, 'varchar', :title_key, :title_form_stage_id),
                ('Description', 'description', 1, 0, 0, 'text', :desc_key, :desc_form_stage_id)"
        );

        foreach ($rows as $row) {
            if (!in_array($row['id'], $form_stages_to_ignore)) {
                $uuid = Uuid::uuid4();
                $title_key = $uuid->toString();
                $uuid = Uuid::uuid4();
                $desc_key = $uuid->toString();

                $insert->execute(
                    [
                        ':title_form_stage_id' => $row['id'],
                        ':desc_form_stage_id' => $row['id'],
                        ':title_key' => $title_key,
                        ':desc_key' => $desc_key
                    ]
                );
            }
        }
    }
}
