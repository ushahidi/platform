<?php
/*
 * This file is part of DbUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integration\Bootstrap\Database\DataSet;

use Symfony;

/**
 * The default YAML parser, using Symfony/Yaml.
 */
class SymfonyYamlParser
{
    public function parseYaml($yamlFile)
    {
        return Symfony\Component\Yaml\Yaml::parse(\file_get_contents($yamlFile));
    }
}