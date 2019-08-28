<?php

namespace Ushahidi\App\Listener;

use Ushahidi\Core\Entity;
use Ushahidi\App\Jobs\ExportPostsJob;

class QueueExportJob
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param int $id
     * @param Entity $entity
     * @return void
     */
    public function handle($id, Entity $entity)
    {
        dispatch(new ExportPostsJob($id));
    }
}
