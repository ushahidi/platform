/**
 * Post Point Value
 *
 * @module     PointValue
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'views/values/Value', 'hbs!templates/values/PointValue'],
	function(App, ValueView, template) {
		return ValueView.extend(
		{
			tagName: 'dd',
			className: 'post-value',
			template : template,
			events : {
				'click .js-show-point-on-map' : 'showPoint'
			},
			showPoint : function(e)
			{
				e.preventDefault();
				App.vent.trigger('map:showValue', { key: this.key, value_id: this.value_id });
			}
		});
	});
