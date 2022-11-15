<?php

use Phinx\Migration\AbstractMigration;

class AddDefaultPostType extends AbstractMigration
{

    /**
     * Migrate Up.
     */
    public function up()
    {
        $connection = $this->getAdapter()->getConnection();
        $adapter = $this->getAdapter();

        $count = $this->fetchRow("SELECT COUNT(*) AS form_count FROM forms");

        // If we don't yet have any post types..
        if ($count['form_count'] == 0) {
            // .. create a default post type
            // Note: Timestamp here *may* be in a different timezone, since
            // we're using mysql time, not PHP time.
            $this->execute(
                "INSERT INTO forms (name, description, type, created)
                VALUES ( 'Basic Post', 'Post with a location', 'report', UNIX_TIMESTAMP(NOW()) )"
            );

            $connection->prepare(
                "INSERT INTO form_stages (label, priority, required, form_id)
                VALUES ( 'Structure', 0, 1, :form_id )"
            )->execute([ ':form_id' => $connection->lastInsertId() ]);

            $attrInsert = $connection->prepare(
                "INSERT INTO form_attributes (" . $adapter->quoteColumnName('key') . ",
                    label, input, type, required, priority, cardinality, form_stage_id)
                VALUES
                    ( 'location_default', 'Location', 'location', 'point', 0, 0, 1, :form_stage_id )
                "
            )->execute([ ':form_stage_id' => $connection->lastInsertId() ]);
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        // Noop - too risky to delete a post type
    }
}
