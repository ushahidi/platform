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

namespace Ushahidi\Core\Tool;

use Illuminate\Contracts\Translation\Translator;

trait TranslatorTrait
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param  Translator $translator
     * @return void
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
        return $this;
    }
}
