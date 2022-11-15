<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RoutesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'routes:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display all registered routes.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $routeCollection = app('router')->getRoutes();

        $headers = ['Verb', 'Path', 'Action'];
        $rows = [];
        $x = 0;
        foreach ($routeCollection as $route) {
            $handler = $route->action['uses'];
            if (! empty($handler) && ! ($handler instanceof \Closure)) {
                $data = explode('@', $handler);
                $action = $data[0];
            } else {
                $action = 'Closure func';
            }
            $rows[$x]['verb'] = $route->methods[0];
            $rows[$x]['path'] = $route->uri;
            $rows[$x]['action'] = $action;
            $x++;
        }

        $this->table($headers, $rows);
    }
}
