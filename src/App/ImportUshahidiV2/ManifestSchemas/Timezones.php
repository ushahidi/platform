<?php

namespace Ushahidi\App\ImportUshahidiV2\ManifestSchemas;

// timezones:
//   default: Europe/Madrid     # timezone to use if the site is not specifying one
//   force: Europe/Madrid       # timezone to use *even if* the site is specifying one

class Timezones
{
    /**
     * @var string
     */
    public $default;

    /**
     * @var @string
     */
    public $force;
}
