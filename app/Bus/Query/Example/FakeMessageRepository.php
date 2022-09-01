<?php

namespace Ushahidi\App\Bus\Query\Example;

/**
 * This class is an example and is not to be used anywhere else in the project.
 */
final class FakeMessageRepository
{
    private $dataset = ['foo', 'bar', 'baz', 'hello world'];

    public function findByIndex(int $index): string
    {
        if (!array_key_exists($index, $this->dataset)) {
            throw new \Error('Element not found');
        }

        return $this->dataset[$index];
    }
}
