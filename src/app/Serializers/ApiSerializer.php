<?php
namespace App\Serializers;

use Illuminate\Support\Arr;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Serializer\ArraySerializer;

/**
 * @package App\Serializers
 */
class ApiSerializer extends ArraySerializer
{
    /**
     * Serialize a collection.
     *
     * @param string|null $resourceKey
     * @param array $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        if (empty($resourceKey)) {
            return $data;
        }
        return [$resourceKey => $data];
    }

    /**
     * Serialize null resource.
     *
     * @return null
     */
    public function null()
    {
        return null;
    }

    /**
     * Serialize the paginator.
     *
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = (int)$paginator->getCurrentPage();
        $lastPage = (int)$paginator->getLastPage();
        $pagination = [
            'total'        => (int)$paginator->getTotal(),
            'count'        => (int)$paginator->getCount(),
            'per_page'     => (int)$paginator->getPerPage(),
            'current_page' => $currentPage,
            'total_pages'  => $lastPage,
        ];

        return ['pagination' => $pagination];
    }

    /**
     * Serialize the meta.
     *
     * @param array $meta
     *
     * @return array
     */
    public function meta(array $meta)
    {
        if (empty($meta)) {
            return [];
        }
        if (isset($meta['pagination'])) {
            $meta = array_merge($meta['pagination'], Arr::except($meta, 'pagination'));
        }
        return $meta ;
    }
}
