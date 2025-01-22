<?php

namespace Ushahidi\Core\Tool;

/**
 * Ushahidi Core Validation Tool
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Core
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html
 *             GNU Affero General Public License Version 3 (AGPL3)
 */

use Ushahidi\Contracts\ValidationEngine;
use Kohana\Validation\Validation as KohanaValidation;
use Illuminate\Contracts\Translation\Translator;

class KohanaValidationEngine extends KohanaValidation implements ValidationEngine
{
    /**
     * @var \Illuminate\Translation\Translator
     */
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;

        parent::__construct([], $this->getTranslationCallback());
    }

    public function getTranslationCallback()
    {
        return function ($file, $field, $error = null) {
            if ($error) {
                return $this->translator->has("$file.$field.$error") ?
                    $this->translator->get("$file.$field.$error") : false;
            } else {
                return $this->translator->has("$file.$field") ?
                    $this->translator->get("$file.$field") : false;
            }
        };
    }

    public function setData(array $data)
    {
        $this->_data = $data;
    }

    public function getData($key = null)
    {
        if ($key === null) {
            return $this->_data;
        }

        if (array_key_exists($key, $this->_data)) {
            return $this->_data[$key];
        }

        return null;
    }

    public function setFullData(array $fullData)
    {
        $this->bind(':fulldata', $fullData);
    }

    public function getFullData($key = null)
    {
        if ($key === null) {
            return $this->_bound[':fulldata'];
        }

        if (array_key_exists($key, $this->_bound[':fulldata'])) {
            return $this->_bound[':fulldata'][$key];
        }

        return null;
    }
}
