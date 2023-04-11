<?php

namespace Ushahidi\Modules\V5\Actions\Translation\Commands;

use App\Bus\Command\Command;

class AddTranslationCommand implements Command
{
    /**
     * Translatable type
     * @var string
     */
    private $type;

    /**
     * Translatable ID
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $translation;

    /**
     * @var string
     */
    private $language;

    public function __construct(
        string $type,
        int $id,
        string $key,
        string $translation,
        string $language
    ) {
        $this->type = $type;
        $this->id = $id;
        $this->key = $key;
        $this->translation = $translation;
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getTranslation(): string
    {
        return $this->translation;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }
}
