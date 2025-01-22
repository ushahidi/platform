<?php

/**
 * Ushahidi Formatter Tool Trait
 *
 * Gives objects a method for storing an formatter instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Usecase\Concerns;

use Ushahidi\Contracts\Formatter as FormatterInterface;

trait Formatter
{
    /**
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * @param  FormatterInterface $formatter
     * @return self
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }
}
