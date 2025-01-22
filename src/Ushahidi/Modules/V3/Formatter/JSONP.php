<?php

/**
 * Ushahidi JSON Formatter
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi\Application
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

namespace Ushahidi\Modules\V3\Formatter;

use Ushahidi\Contracts\Formatter;
use Ushahidi\Core\Tool\OutputFormatter;

class JSONP extends JSON implements Formatter, OutputFormatter
{
    /**
     * @var  string $callback name of JSONP function
     */
    private $callback;

    protected function getOptions()
    {
        $opts = parent::getOptions();
        // Some clients will not handle formatted JSONP, disable it
        return intval($opts) & ~JSON_PRETTY_PRINT;
    }

    /**
     * Sets the JSONP callback. Callback must be alpha-numeric, but can contain
     * a class name: foo, foo.bar, Foo.go123 are all valid callbacks.
     * @param  string  $callback
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setCallback($callback)
    {
        if (is_callable($callback)) {
            // Support using closures that return the callback names.
            $callback = $callback();
        }

        if (empty($callback)) {
            throw new InvalidArgumentException('JSONP callback must not be empty');
        }

        // Callback can be any of: foo, Foo.bar, foo.b123, f123.b123
        // But cannot be: 123, 1.23, foo-bar, or anything else weird
        if (!preg_match('/^(?:[a-z_][a-z0-9_]*\.)?[a-z_][a-z0-9_]*$/i', $callback)) {
            throw new InvalidArgumentException('JSONP callback is not valid: ' . $callback);
        }

        $this->callback = $callback;
        return $this;
    }

    // Formatter
    public function __invoke($input)
    {
        // Format input as JSON
        $json = parent::__invoke($input);

        // ... and wrap it in the callback, prepending /**/ to help prevent
        // content sniffing, see T455.
        return "/**/{$this->callback}({$json})";
    }

    // OutputFormatter
    public function getMimeType()
    {
        return 'application/javascript';
    }
}
