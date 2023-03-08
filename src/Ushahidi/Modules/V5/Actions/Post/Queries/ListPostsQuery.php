<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;

class ListPostsQuery implements Query
{
    private const DEFAULT_LIMIT = 20;

    private const ALLOWED_FIELDS = [
        'id',
        'parent_id',
        'base_language',
        'form_id',
        'status',
        'form_id',
        'user_id',
        'type',
        'title',
        'slug',
        'content',
        'author_email',
        'author_realname',
        'status',
        'published_to',
        'locale',
        'post_date',
        'base_language',
        'created',
        'updated'
    ];
    /**
     * @var array
     */
    private $fields;

    /**
     * @var int
     */
    private $limit;

    private function __construct(array $fields, int $limit)
    {
        $this->fields = $fields;
        $this->limit = $limit;
    }

    /**
     * The data parameter is an array containing two keys
     * fields: an array of fields to be returned
     * limit: the number of posts to be returned
     *
     * "fields" key is optional, if not provided, all fields will be returned,
     * if provided it must be an array of strings and must be a subset of the ALLOWED_FIELDS
     *
     * "limit" key is optional, if not provided, the default limit of DEFAULT_LIMIT will be used.
     * if provided it must be an integer greater than 0
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        if (array_key_exists('fields', $data)) {
            $fields = array_filter($data['fields'], function ($field) {
                return in_array($field, self::ALLOWED_FIELDS);
            });

            if (count($fields) !== count($data['fields'])) {
                throw new \InvalidArgumentException('Invalid fields provided');
            }
        }

        $fields = $data['fields'] ?? [];

        if (array_key_exists('limit', $data) && $data['limit'] < 1) {
            throw new \InvalidArgumentException('Limit must be greater than 0');
        }

        $limit = $data['limit'] ?? self::DEFAULT_LIMIT;

        return new self($fields, $limit);
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
