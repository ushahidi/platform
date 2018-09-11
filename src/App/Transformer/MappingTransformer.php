<?php

/**
 * Ushahidi Mapping Transformer
 *
 * A user defined transform, transforms records based on
 * - a source-destination mapping
 * - a set of fixed destination values
 *
 * Uses the MappingStep from ddeboer/import to process
 * the transformation
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\App\Transformer;

use Ushahidi\Core\Tool\MappingTransformer as MappingTransformerInterface;
use Ddeboer\DataImport\Step\MappingStep;

class MappingTransformer implements MappingTransformerInterface
{
    protected $map;
    // MappingTransformer
    public function setMap(array $map)
    {
        $this->map = new MappingStep($map);
    }

    protected $fixedValues;
    // MappingTransformer
    public function setFixedValues(array $fixedValues)
    {
        $this->fixedValues = $fixedValues;
    }

    // Tranformer
    public function interact(array $data)
    {
        $this->map->process($data);

        $data = array_merge($data, $this->fixedValues);

        return $data;
    }
}
