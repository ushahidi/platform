/**
 * Single value LocalStorage persistence.
 *
 * @module     App.storage
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['ddt'],
	function(ddt)
	{
		/**
		 * Generator for localStorage access for a single value.
		 *
		 * Usage:
		 *
		 *     var keystore = new Storage('Acme', 'my-stored-key')
		 *         value = keystore.get(),
		 *         changed_value = 'foo';
		 *
		 *     console.log('got value', value);
		 *     keystore.set('foo');
		 *     console.log('changed value to', keystore.get());
		 *
		 * If the browser does not support localStorage, all calls will be void.
		 */
		if (!window.localStorage) {
			ddt.log('Storage', 'No localStorage available, all instances will be void');
			return function()
			{
				var noop = function(){};
				this.get = noop;
				this.set = noop;
				this.clear = noop;
			};
		}

		function getKey(ns, key)
		{
			// convert to Namespace:Key
			return [ns, key].join(':');
		}

		function getValue(ns, key)
		{
			key = getKey(ns, key);
			var value = window.localStorage.getItem(key);
			ddt.log('Storage', 'get', key, value);
			return value;
		}

		function setValue(ns, key, value)
		{
			key = getKey(ns, key);
			ddt.log('Storage', 'set', key, value);
			window.localStorage.setItem(key, value);
		}

		function clearValue(ns, key)
		{
			if (typeof getValue(ns, key) !== 'undefined') {
				key = getKey(ns, key);
				ddt.log('Storage', 'clear', key);
				window.localStorage.removeItem(key);
			}
		}

		return function(namespace, key)
		{
			this.get = function()
			{
				return getValue(namespace, key);
			};
			this.set = function(value)
			{
				return setValue(namespace, key, value);
			};
			this.clear = function()
			{
				return clearValue(namespace, key);
			};
		};
	});
