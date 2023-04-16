<?php

namespace Ushahidi\Modules\V5\Repository\Translation;

use Ushahidi\Modules\V5\Models\Translation;

class EloquentTranslationRepository implements TranslationRepository
{
    public function store(
        string $translatableType,
        int $translatableId,
        string $translatedKey,
        string $translation,
        string $language
    ): void {
        (new Translation([
            'translatable_type' => $translatableType,
            'translatable_id' => $translatableId,
            'translated_key' => $translatedKey,
            'translation' => $translation,
            'language' => $language
        ]))->saveOrFail();
    }
}
