<?php

namespace Tests\Integration\Bootstrap\Database\DataSet;

use Symfony;

class SymfonyYamlParser
{
    public function parseYaml($yamlFile)
    {
        return Symfony\Component\Yaml\Yaml::parse(\file_get_contents($yamlFile));
    }
}