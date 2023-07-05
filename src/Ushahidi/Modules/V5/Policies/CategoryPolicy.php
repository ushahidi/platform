<?php

namespace Ushahidi\Modules\V5\Policies;

use Ushahidi\Core\Tool\Acl;
use Ushahidi\Authzn\GenericUser as User;
use Ushahidi\Core\Ohanzee\Entities\Tag as OhanzeeCategory;
use Ushahidi\Modules\V5\Models\Category as EloquentCategory;
use Ushahidi\Core\Tool\Authorizer\TagAuthorizer;

class CategoryPolicy
{
    protected $authorizer;

    public function __construct(Acl $acl, TagAuthorizer $authorizer)
    {
        $this->authorizer = $authorizer;
        $this->authorizer->setAcl($acl);
    }

    public function view(User $user, EloquentCategory $category)
    {
        $accessedCategory = new OhanzeeCategory($category->toArray());

        return $this->authorizer->setUser($user)->isAllowed($accessedCategory, 'search');
    }

    public function create(User $user)
    {
        return $this->authorizer->setUser($user)->isAllowed(new OhanzeeCategory, 'create');
    }

    public function show(User $user, EloquentCategory $category)
    {
        $accessedCategory = new OhanzeeCategory($category->toArray());

        return $this->authorizer->setUser($user)->isAllowed($accessedCategory, 'read');
    }

    public function delete(User $user, EloquentCategory $category)
    {
        $accessedCategory = new OhanzeeCategory($category->toArray());
        return $this->authorizer->setUser($user)->isAllowed($accessedCategory, 'delete');
    }

    public function update(User $user, EloquentCategory $category)
    {
        $accessedCategory = new OhanzeeCategory($category->toArray());
        return $this->authorizer->setUser($user)->isAllowed($accessedCategory, 'update');
    }
}
