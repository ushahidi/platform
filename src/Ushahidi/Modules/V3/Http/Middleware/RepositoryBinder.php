<?php

namespace Ushahidi\Modules\V3\Http\Middleware;

use Closure;

class RepositoryBinder
{
    /**
     * The repository container binder resolver callback.
     *
     * @var \Closure
     */
    protected static $repositoryBinderResolver;

    /**
     * Set the repository container binder resolver callback.
     *
     * @param  \Closure  $resolver
     * @return void
     */
    public static function repositoryBinderResolver(Closure $resolver)
    {
        static::$repositoryBinderResolver = $resolver;
    }

    /**
     * Resolve the respository container binder.
     *
     * @return void
     */
    public static function resolveRepositoryBinder()
    {
        if (isset(static::$repositoryBinderResolver)) {
            call_user_func(static::$repositoryBinderResolver);
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle ($request, Closure $next)
    {
        static::resolveRepositoryBinder();

        $next($request);
    }
}
