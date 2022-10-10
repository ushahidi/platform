<?php

namespace App\Providers;

use App\Bus\Command\CommandBus;
use App\Bus\Command\Example\ExampleCommand;
use App\Bus\Command\Example\ExampleCommandHandler;
use App\Bus\Query\Example\ExampleQuery;
use App\Bus\Query\Example\ExampleQueryHandler;
use App\Bus\Query\QueryBus;
use Illuminate\Support\ServiceProvider;
use Ushahidi\Modules\V5\Actions\CountryCode\Query\FetchCountryCodeByIdQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\Query\FetchCountryCodeQuery;
use Ushahidi\Modules\V5\Actions\CountryCode\QueryHandler\FetchCountryCodeByIdQueryHandler;
use Ushahidi\Modules\V5\Actions\CountryCode\QueryHandler\FetchCountryCodeQueryHandler;

class BusServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    public function register(): void
    {
        $this->registerCommands();
        $this->registerQueries();
    }

    private function registerCommands(): void
    {
        $this->app->singleton(CommandBus::class, function ($app) {
            $commandBus = new CommandBus($app);

            $commandBus->register(ExampleCommand::class, ExampleCommandHandler::class);

            return $commandBus;
        });
    }

    private function registerQueries(): void
    {
        $this->app->singleton(QueryBus::class, function ($app) {
            $queryBus = new QueryBus($app);

            $queryBus->register(FetchCountryCodeQuery::class, FetchCountryCodeQueryHandler::class);
            $queryBus->register(FetchCountryCodeByIdQuery::class, FetchCountryCodeByIdQueryHandler::class);

            return $queryBus;
        });
    }
}
