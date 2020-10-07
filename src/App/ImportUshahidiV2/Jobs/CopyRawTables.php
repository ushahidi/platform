<?php

namespace Ushahidi\App\ImportUshahidiV2\Jobs;

use Ushahidi\App\Jobs\Job;
use Ushahidi\App\ImportUshahidiV2\ImportSourceData;
use Ushahidi\App\ImportUshahidiV2\Contracts\ImportSourceDataRepository;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CopyRawTables extends Job
{
    use Concerns\ConnectsToV2DB;

    protected const INSERT_CHUNK_SIZE = 1000;

    protected $importId;
    protected $dbConfig;

    protected $sourceDataRepo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(int $importId, array $dbConfig)
    {
        $this->importId = $importId;
        $this->dbConfig = $dbConfig;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ImportSourceDataRepository $sourceDataRepo)
    {
        $this->sourceDataRepo = $sourceDataRepo;

        $tables = $this->getConnection()->select("SHOW TABLES");

        foreach (array_values($tables) as $table) {
            $table = array_values(get_object_vars($table))[0];
            // skip tables suffixed with '__backup'
            if (preg_match('/__backup$/', $table)) {
                continue;
            }
            $this->dumpTable($table);
        }
    }

    protected function dumpTable(string $table)
    {
        $insertChunk = [];

        $q = $this->getConnection()->table($table);

        // table specific selection query
        switch ($table) {
            case "geometry":
                $q = $q->select(
                    'id',
                    'incident_id',
                    DB::raw('astext(geometry) as geometry'),
                    'geometry_label',
                    'geometry_comment',
                    'geometry_color',
                    'geometry_strokewidth'
                );
                break;
            default:
        }

        $cursor = $q->cursor();

        foreach ($cursor as $row) {
            $json_row = json_encode($row);
            $sourceData = [
                'import_id' => $this->importId,
                'source_table' => $table,
                'row_id' => $this->getRowId($row, $table),
                'data' => $json_row
            ];

            // Log::debug("Saving db row", [
            //     'row' => $row,
            //     'as' => $sourceData
            // ]);

            $insertChunk[] = $sourceData;
            if (count($insertChunk) >= self::INSERT_CHUNK_SIZE) {
                $this->sourceDataRepo->insert($insertChunk);
                $insertChunk = [];
            }
        }

        if (count($insertChunk) > 0) {
            $this->sourceDataRepo->insert($insertChunk);
        }
    }

    private function getRowId($row, $table)
    {
        switch ($table) {
            case 'actions':
                return $row->action_id;
            case 'badge_users':
                return "{$row->user_id},{$row->badge_id}";
            case 'maintenance':
                return $row->allowed_ip;
            case 'permissions_roles':
                return "{$row->role_id},{$row->permission_id}";
            case 'roles_users':
                return "{$row->user_id},{$row->role_id}";
            case 'sessions':
                return $row->session_id;
            default:
                return $row->id ?? '';
        }
    }
}
