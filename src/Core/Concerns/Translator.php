<?php

/**
 * Ushahidi Translator Trait
 *
 * Gives objects a method for storing an formatter instance.
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Platform
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Core\Concerns;

use Illuminate\Contracts\Translation\Translator as TranslatorInterface;

trait Translator
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param  TranslatorInterface $translator
     * @return self
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        return $this;
    }
}
