<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\TransformerAbstract;

trait Transformer
{
    /**
     * Return collection response from the application
     *
     * @param array|LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection $collection
     * @param \Closure|TransformerAbstract $transformer
     * @param array|null $with
     * @param string $resourceKey
     * @return array
     */
    protected function transformCollection($collection, $transformer, ?array $with = null, string $resourceKey = 'data'): array
    {
        $resource = new Collection($collection, $transformer, $resourceKey);

        if (empty($collection) && $collection !== []) {
            $collection = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            $resource = new Collection($collection, $transformer);
        }
        if ($collection instanceof LengthAwarePaginator) {
            $resource->setPaginator(new IlluminatePaginatorAdapter($collection));
        } else {
            $resource->setMetaValue("total", count($collection));
        }

        return $this->transformResource($resource, $with);
    }

    /**
     * Return single item response from the application
     *
     * @param Model|array $item
     * @param \Closure|TransformerAbstract $transformer
     * @param array|null $with
     * @param string|null $resourceKey
     * @return array
     */
    protected function transformItem($item, $transformer, ?array $with = null, ?string $resourceKey = null): array
    {
        return $this->transformResource(new Item($item, $transformer, $resourceKey), $with);
    }

    /**
     * Return response from the resource item
     *
     * @param ResourceInterface $resource
     * @param array|null $with
     * @return array
     */
    private function transformResource(ResourceInterface $resource, ?array $with = null): array
    {
        /** @var Manager $fractal */
        $fractal = App::make(Manager::class);
        if ($with !== null || Request::has('with')) {
            $fractal->parseIncludes($with ?? Request::get('with'));
        }
        return $fractal->createData($resource)->toArray();
    }
}
