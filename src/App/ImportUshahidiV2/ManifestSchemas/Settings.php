<?php

namespace Ushahidi\App\ImportUshahidiV2\ManifestSchemas;

// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// (Readibility is enhanced in this case by having all the mapping def classes here)

// settings:
//   # settings for generated survey fields
//   fields:
//     explicitCategoryBind: true   # bind all categories as options to
//                                  # category fields in imported surveys.
//                                  # This was the only seamless option before circa v5.1

class Settings
{
    /**
     * @var FieldsSettings|null
     */
    public $fields;

    public static function getDefaults()
    {
        $new = new Settings();
        $new->fields = FieldsSettings::getDefaults();
        return $new;
    }
}

class FieldsSettings
{
    /**
     * @var boolean|true
     */
    public $explicitCategoryBind;

    public static function getDefaults()
    {
        $new = new FieldsSettings();
        $new->explicitCategoryBind = true;
        return $new;
    }
}
