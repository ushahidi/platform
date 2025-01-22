<?php
/*
 * This file adapted from the DbUnit package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 * (c) Ushahidi Team <team@ushahidi.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ushahidi\Tests\Integration\Bootstrap\Database\Operation;

class Factory
{

    public static function NONE()
    {
        return new None();
    }

    public static function CLEAN_INSERT($cascadeTruncates = false)
    {
        return new Composite([
            self::TRUNCATE($cascadeTruncates),
            self::INSERT()
        ]);
    }

    public static function INSERT()
    {
        return new Insert();
    }

    public static function TRUNCATE($cascadeTruncates = false)
    {
        $truncate = new Truncate();
        $truncate->setCascade($cascadeTruncates);

        return $truncate;
    }
}
