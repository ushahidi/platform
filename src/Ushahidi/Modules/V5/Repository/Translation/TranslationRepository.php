<?php

namespace Ushahidi\Modules\V5\Repository\Translation;

interface TranslationRepository
{
    public function store(
        string $translatableType,
        int $translatableId,
        string $translatableKey,
        string $translation,
        string $language
    ): void;
}
