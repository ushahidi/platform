<?php

namespace App\Console\Commands\Concerns;

use Ushahidi\Contracts\Formatter;

trait ConsoleFormatter
{
    protected function getFormatter()
    {
        return new class implements Formatter
        {
            /**
             * @param \Ushahidi\Contracts\Entity $entity
             * @return array
             */
            public function __invoke($entity)
            {
                $fields = $entity->asArray();

                $data = ['id' => $entity->id];

                foreach ($fields as $field => $value) {
                    if (is_string($value)) {
                        $value = trim($value);
                    }

                    switch ($field) {
                        case 'created':
                            $data[$field] = date(\DateTime::W3C, $value);
                            break;
                        case 'updated':
                            $data[$field] = $value ? date(\DateTime::W3C) : null;
                            break;
                        default:
                            $data[$field] = $value;
                            break;
                    }
                }

                return $data;
            }
        };
    }
}
