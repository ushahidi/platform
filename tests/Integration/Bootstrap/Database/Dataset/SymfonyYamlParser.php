<?php

namespace Ushahidi\Tests\Integration\Bootstrap\Database\Dataset;

use Symfony;

class SymfonyYamlParser
{
    public function parseYaml($yamlFile)
    {
        return Symfony\Component\Yaml\Yaml::parse(\file_get_contents($yamlFile));
    }
}
