<?php
namespace Ushahidi\Core\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Cache\Factory;

class Repository
{
    /**
     * @var \Illuminate\Contracts\Container\Container
     */
    private $container;

    /**
     * @var \Illuminate\Contracts\Cache\Factory
     */
    protected $cache;

    /**
     * The cache time to live in seconds.
     *
     * @var int
     */
    protected $cacheTTL = 3600;

    /**
     * The class name of the entity.
     *
     * @var string|null
     */
    protected $entity = null;

    /**
     * The eloquent model instance of an entity.
     *
     * @var \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     */
    protected $model = null;

    /**
     * EloquentRepository constructor.
     *
     * @param \Illuminate\Contracts\Container\Container $container
     * @param \Illuminate\Contracts\Cache\Factory $cache
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Container $container, Factory $cache)
    {
        $this->container = $container;
        $this->cache = $cache;

        if ($this->entity) {
            $this->resolveModel();
        }
    }

    /**
     * @param string $entity
     *
     * @return self
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setEntity($entity): self
    {
        $this->entity = $entity;
        $this->resolveModel();

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function resolveModel(): void
    {
        $this->model = $this->container->make($this->entity);
    }

        /**
     * Sets listed criteria for entity.
     *
     * @param mixed ...$criteria
     *
     * @return self
     */
    public function withCriteria(...$criteria): self
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterion) {
            /** @var \Ushahidi\Core\Eloquent\Criterion $criterion */
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }

    /**
     * Defines cache key.
     *
     * @return string
     */
    public function cacheKey(): string
    {
        return $this->model->getTable();
    }

    /**
     * Get cache time-to-live value from property or method if available.
     *
     * @return int
     */
    private function cacheTTLValue(): int
    {
        if (method_exists($this, 'cacheTTL')) {
            return $this->cacheTTL();
        }

        return $this->cacheTTL;
    }
}
