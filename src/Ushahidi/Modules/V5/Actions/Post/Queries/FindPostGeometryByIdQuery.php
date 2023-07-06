<?php

namespace Ushahidi\Modules\V5\Actions\Post\Queries;

use App\Bus\Query\Query;
use Ushahidi\Modules\V5\Models\Post\Post;
use Illuminate\Http\Request;

class FindPostGeometryByIdQuery implements Query
{
    private $id;
    private $fields;
    private $hydrates;

    private $with_relationships;

    private $fields_for_relationships;


    public function __construct(int $id, array $fields = [], array $hydrates = [])
    {
        $this->id = $id;
        $this->fields = array_unique(array_merge($fields, Post::REQUIRED_FIELDS));
        $this->hydrates = $hydrates;
        $this->with_relationships = [];
        $this->fields_for_relationships = [];
        foreach ($hydrates as $hydrate) {
            if (Post::ALLOWED_RELATIONSHIPS[$hydrate]) {
                $this->with_relationships = array_unique(
                    array_merge(
                        $this->with_relationships,
                        Post::ALLOWED_RELATIONSHIPS[$hydrate]['relationships']
                    )
                );
                $this->fields_for_relationships = array_unique(
                    array_merge(
                        $this->fields_for_relationships,
                        Post::ALLOWED_RELATIONSHIPS[$hydrate]['fields']
                    )
                );
            }
        }
    }


    public static function fromRequest(int $id, Request $request): self
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Id must be a positive number');
        }

        if ($request->get('format') === 'minimal') {
            $fields = ['id', 'title', 'content'];
            $hydrates = ['translations'];
        } elseif (!$request->get('only')) {
            $fields = Post::ALLOWED_FIELDS;
            $hydrates = array_keys(Post::ALLOWED_RELATIONSHIPS);
        } else {
            $only_values = explode(',', $request->get('only'));
            $fields = [];
            $hydrates = [];
            foreach ($only_values as $only_value) {
                if (in_array($only_value, Post::ALLOWED_FIELDS)) {
                    $fields[] = $only_value;
                } elseif (array_key_exists($only_value, Post::ALLOWED_RELATIONSHIPS)) {
                    $hydrates[] = $only_value;
                }
            }
        }

        return new self($id, $fields, $hydrates);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getHydrates(): array
    {
        return $this->hydrates;
    }

    public function getWithRelationship(): array
    {
        return $this->with_relationships;
    }

    public function getFieldsForRelationship(): array
    {
        return $this->fields_for_relationships;
    }
}
