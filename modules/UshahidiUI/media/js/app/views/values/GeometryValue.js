/**
 * Post Geometry Value
 *
 * @module     GeometryValue
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['App', 'handlebars', 'views/values/Value', 'text!templates/values/GeometryValue.html'],
	function(App, Handlebars, ValueView, template) {
		return ValueView.extend(
		{
			className: 'post-value',
			template : Handlebars.compile(template),
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