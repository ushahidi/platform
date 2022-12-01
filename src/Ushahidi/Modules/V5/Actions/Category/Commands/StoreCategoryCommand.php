<?php

namespace Ushahidi\Modules\V5\Actions\Category\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Requests\StoreCategoryRequest;

class StoreCategoryCommand implements Command
{
    /**
     * @var ?string
     */
    private $parentId;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $type;

    /**
     * @var ?string
     */
    private $description;

    /**
     * @var ?string
     */
    private $color;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var string
     */
    private $role;

    public function __construct(
        ?string $parentId,
        string  $slug,
        string  $type,
        ?string $description,
        ?string $color,
        string  $icon,
        int     $priority,
        string  $role
    ) {
        $this->parentId    = $parentId;
        $this->slug        = $slug;
        $this->type        = $type;
        $this->description = $description;
        $this->color       = $color;
        $this->icon        = $icon;
        $this->priority    = $priority;
        $this->role        = $role;
    }

    public static function createFromRequest(StoreCategoryRequest $request): self
    {
        return new self(
            $request->input('parent_id'),
            $request->input('slug'),
            $request->input('type'),
            $request->input('description'),
            $request->input('color'),
            $request->input('icon'),
            $request->input('priority'),
            $request->input('role')
        );
    }

    /**
     * @return string|null
     */
    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }
}
