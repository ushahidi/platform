<?php

namespace Ushahidi\Authzn;

use Ushahidi\Core\Tool\Acl;
use Ushahidi\Contracts\Acl as AclInterface;
use Ushahidi\Contracts\Repository\Entity\RoleRepository;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AclInterface::class, Acl::class);

        $this->app->extend(Acl::class, function (Acl $acl) {
            return $acl->setRoleRepo($this->app[RoleRepository::class]);
        });

        // $this->app->singleton(Session::class, function ($app) {
        //     return new Session();
        // });
    }
}
