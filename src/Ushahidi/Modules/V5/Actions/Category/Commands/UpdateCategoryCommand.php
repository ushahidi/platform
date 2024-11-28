<?php

namespace Ushahidi\Modules\V5\Actions\Category\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Requests\CategoryRequest;
use Ushahidi\Modules\V5\Models\Category;

class UpdateCategoryCommand implements Command
{
    const DEFAULT_LANUGAGE = 'en';
    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var ?string
     */
    private $parentId;

    /**
     * @var ?string
     */
    private $tag;

    /**
     * @var ?string
     */
    private $slug;

    /**
     * @var ?string
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
     * @var ?string
     */
    private $icon;

    /**
     * @var ?int
     */
    private $priority;

    /**
     * @var ?array
     */
    private $role;

    /**
     * @var ?string
     */
    private $defaultLanguage;

    /**
     * @var ?array
     */
    private $availableLanguages;
    
    /**
    * @var array
    */
    private $translations;

    public function __construct(
        int $categoryId,
        ?string $parentId,
        ?string $tag,
        ?string $slug,
        ?string $description,
        ?string $type,
        ?string $color,
        ?string $icon,
        ?int $priority,
        ?array $role,
        array $translations,
        ?string $defaultLanguage,
        ?array  $availableLanguages = []
    ) {
        $this->categoryId = $categoryId;
        $this->parentId = $parentId;
        $this->tag = $tag;
        $this->slug = $slug;
        $this->description = $description;
        $this->type = $type;
        $this->color = $color;
        $this->icon = $icon;
        $this->priority = $priority;
        $this->role = $role;
        $this->defaultLanguage = $defaultLanguage;
        $this->availableLanguages = $availableLanguages;
        $this->translations = $translations;
    }

    public static function fromRequest(int $id, CategoryRequest $request, Category $current_category): self
    {
        return new self(
            $id,
            $request->has('parent_id')?$request->input('parent_id'):$current_category->parent_id,
            $request->has('tag')?$request->input('tag'):$current_category->tag,
            $request->has('slug')?$request->input('slug'):$current_category->slug,
            $request->has('description')?$request->input('description'):$current_category->description,
            $request->has('type')?$request->input('type'):$current_category->type,
            $request->has('color')?$request->input('color'):$current_category->color,
            $request->has('icon')?$request->input('icon'):$current_category->icon,
            $request->has('priority')?$request->input('priority'):$current_category->priority,
            $request->has('role')?$request->input('role'):$current_category->role,
            $request->input('translations')??[],
            self::DEFAULT_LANUGAGE,
            []
        );
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function getRole(): ?array
    {
        return $this->role;
    }

    public function getDefaultLanguage(): ?string
    {
        return $this->defaultLanguage;
    }

    public function getAvailableLanguages(): ?array
    {
        return $this->availableLanguages;
    }
     
    /**
     * @return array
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }
}
