<?php

namespace Ushahidi\Modules\V5\Actions\Category\Commands;

use App\Bus\Command\Command;
use Ushahidi\Modules\V5\Models\Category;
use Ushahidi\Modules\V5\Requests\CategoryRequest;

class StoreCategoryCommand implements Command
{
    // todo: At some point we might want to change it into a parameter
    const DEFAULT_LANUGAGE = 'en';
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
     * @var ?array
     */
    private $role;

    /**
    * @var string
    */
    private $defaultLanguage;

    /**
    * @var array
    */
    private $availableLanguages;
    
    /**
    * @var array
    */
    private $translations;

    public function __construct(
        ?int $parentId,
        string  $slug,
        string $tag,
        string  $type,
        ?string $description,
        ?string $color,
        ?string  $icon,
        int     $priority,
        ?array  $role,
        array $translations,
        ?string $defaultLanguage = 'en',
        array   $availableanguages = []
    ) {
        $this->parentId    = $parentId;
        $this->tag         = $tag;
        $this->slug        = $slug;
        $this->type        = $type;
        $this->description = $description;
        $this->color       = $color;
        $this->icon        = $icon;
        $this->priority    = $priority;
        $this->role        = $role;
        $this->defaultLanguage = $defaultLanguage;
        $this->availableLanguages = $availableanguages;
        $this->translations = $translations;
    }

    public static function createFromRequest(CategoryRequest $request): self
    {
        $slug = $request->input('slug');
        if (!$slug) {
            $slug = Category::makeSlug($request->input('slug') ?? $request->input('tag'));
        }

        return new self(
            (int) $request->input('parent_id'),
            $slug,
            $request->input('tag'),
            $request->input('type'),
            $request->input('description'),
            $request->input('color'),
            $request->input('icon'),
            (int) $request->input('priority'),
            $request->input('role'),
            $request->input('translations')??[],
            self::DEFAULT_LANUGAGE,
            []
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
    public function getIcon(): ?string
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
     * @return array
     */
    public function getRole(): ?array
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

    public function getDefaultLanguage(): string
    {
        return $this->defaultLanguage;
    }

    public function getAvailableLanguages(): array
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
